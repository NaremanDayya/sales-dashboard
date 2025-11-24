<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ChatList extends Component
{
    public $client;
    public $selectedConversation;
    public $search = '';
    public $perPage = 20;
    public $hasMore = true;
    public $loading = false;
    public $page = 1;
    public $allConversations;
    private $lastBatchHasMore = false;
    public $filter = 'newest';

    // Add cache key for this user's unread counts
    private function getUnreadCountCacheKey()
    {
        return 'user_' . Auth::id() . '_conversation_unread_counts';
    }

    // Method to get cached unread counts
    private function getCachedUnreadCounts()
    {
        return Cache::get($this->getUnreadCountCacheKey(), []);
    }

    // Method to update cached unread count for a conversation
    private function updateCachedUnreadCount($conversationId, $count)
    {
        $cachedCounts = $this->getCachedUnreadCounts();
        $cachedCounts[$conversationId] = $count;
        Cache::put($this->getUnreadCountCacheKey(), $cachedCounts, now()->addHours(2));
    }

    // Method to clear all cached unread counts
    private function clearCachedUnreadCounts()
    {
        Cache::forget($this->getUnreadCountCacheKey());
    }

    protected $listeners = [
        'conversationUpdated' => 'handleConversationUpdate',
        'newMessageReceived' => 'handleNewMessage',
        'refreshChatList' => 'refresh',
        'markMessagesAsRead' => 'handleMarkMessagesAsRead', // Add this new listener
    ];

    public function mount($client = null)
    {
        $this->client = $client;
        $this->loadInitialConversations();
    }

    public function loadInitialConversations()
    {
        $this->allConversations = $this->getConversations(1);
        $this->hasMore = $this->lastBatchHasMore;
    }

    public function loadMore()
    {
        if ($this->loading || !$this->hasMore) {
            return;
        }

        $this->loading = true;
        $this->page++;

        $additionalConversations = $this->getConversations($this->page);
        $this->hasMore = $this->lastBatchHasMore;

        // Merge and ensure unread counts are preserved
        $existingConversations = $this->allConversations->keyBy('id');
        $newConversations = $additionalConversations->keyBy('id');

        // Preserve unread counts from existing conversations
        foreach ($existingConversations as $id => $conv) {
            if ($newConversations->has($id)) {
                $newConversations[$id]->unread_count = $conv->unread_count ?? $newConversations[$id]->unread_count;
            }
        }

        $this->allConversations = $existingConversations
            ->merge($newConversations)
            ->values();
        $this->loading = false;
    }

    public function handleConversationUpdate($conversationId)
    {
        // Refresh the specific conversation
        $this->refreshConversation($conversationId);
    }

    public function handleNewMessage($data)
    {
        if (isset($data['conversation_id'])) {
            $this->refreshConversation($data['conversation_id']);
        } else {
            $this->refresh();
        }
    }

    public function handleMarkMessagesAsRead($conversationId)
    {
        // Update cached unread count to 0 when messages are marked as read
        $this->updateCachedUnreadCount($conversationId, 0);

        // Also update in the current collection
        if ($this->allConversations) {
            $this->allConversations = $this->allConversations->map(function ($conversation) use ($conversationId) {
                if ($conversation->id == $conversationId) {
                    $conversation->unread_count = 0;
                }
                return $conversation;
            });
        }
    }

    public function refresh()
    {
        $this->page = 1;
        $this->hasMore = true;
        $this->loading = false;
        $this->loadInitialConversations();
    }

    // New method to refresh a specific conversation
    private function refreshConversation($conversationId)
    {
        $refreshedConversation = Conversation::with([
            'client:id,sales_rep_id,company_name,company_logo',
            'client.salesRep:id,name',
        ])->find($conversationId);

        if ($refreshedConversation) {
            $enhancedConversation = $this->enhanceConversationsWithMessages(collect([$refreshedConversation]))->first();

            if ($this->allConversations) {
                $this->allConversations = $this->allConversations->map(function ($conversation) use ($enhancedConversation) {
                    return $conversation->id == $enhancedConversation->id ? $enhancedConversation : $conversation;
                });
            }
        }
    }

    public function updatedFilter()
    {
        $this->refresh();
    }

    private function getConversations($page = 1)
    {
        $user = Auth::user();
        $offset = ($page - 1) * $this->perPage;

        $query = Conversation::with([
            'client:id,sales_rep_id,company_name,company_logo',
            'client.salesRep:id,name',
        ])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('company_name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('salesRep', function ($q2) {
                            $q2->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->addSelect([
                'latest_message_created_at' => Message::selectRaw('MAX(created_at)')
                    ->whereColumn('conversation_id', 'conversations.id')
            ]);

        // Get cached unread counts to ensure consistency
        $cachedUnreadCounts = $this->getCachedUnreadCounts();

        $query->withCount(['messages as unread_count' => function ($sub) use ($user) {
            $sub->whereNull('read_at')
                ->where('sender_id', '!=', $user->id);
        }]);

        // Apply filter conditions
        $query->when($this->filter === 'unread', function ($q) {
            $q->having('unread_count', '>', 0);
        });

        $query->when($this->filter === 'read', function ($q) {
            $q->having('unread_count', '=', 0);
        });

        // Apply ordering using latest message timestamp
        if ($this->filter === 'oldest') {
            $query->orderBy('unread_count', 'desc')
                ->orderBy('latest_message_created_at', 'asc')
                ->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('unread_count', 'desc')
                ->orderBy('latest_message_created_at', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $conversations = $query
            ->skip($offset)
            ->take($this->perPage + 1)
            ->get();

        $this->lastBatchHasMore = $conversations->count() > $this->perPage;
        if ($this->lastBatchHasMore) {
            $conversations = $conversations->slice(0, $this->perPage)->values();
        }

        return $this->enhanceConversationsWithMessages($conversations);
    }

    private function enhanceConversationsWithMessages($conversations)
    {
        if ($conversations->isEmpty()) {
            return $conversations;
        }

        $conversationIds = $conversations->pluck('id');
        $cachedUnreadCounts = $this->getCachedUnreadCounts();

        // Get latest messages
        $latestMessages = Message::whereIn('conversation_id', $conversationIds)
            ->whereIn('id', function($query) use ($conversationIds) {
                $query->select(\DB::raw('MAX(id)'))
                    ->from('messages')
                    ->whereIn('conversation_id', $conversationIds)
                    ->groupBy('conversation_id');
            })
            ->select('id', 'conversation_id', 'message', 'created_at', 'sender_id')
            ->get()
            ->keyBy('conversation_id');

        // Enhance conversations with message data
        $conversations->each(function ($conversation) use ($latestMessages, $cachedUnreadCounts) {
            $latestMessage = $latestMessages->get($conversation->id);

            $conversation->latest_message_text = $latestMessage ? $latestMessage->message : '';
            $conversation->latest_message_time = $latestMessage ? $latestMessage->created_at : null;
            $conversation->latest_message_sender_id = $latestMessage ? $latestMessage->sender_id : null;
            $conversation->receiver_name = $conversation->getReceiver()->name;
            $conversation->is_last_message_read = $conversation->isLastMessageReadByUser();

            // Use cached unread count if available, otherwise calculate and cache it
            if (isset($cachedUnreadCounts[$conversation->id])) {
                $conversation->unread_count = $cachedUnreadCounts[$conversation->id];
            } else {
                if (!isset($conversation->unread_count)) {
                    $conversation->unread_count = $conversation->unreadMessagesCount();
                }
                // Cache the calculated unread count
                $this->updateCachedUnreadCount($conversation->id, $conversation->unread_count);
            }

            \Log::debug("Conversation {$conversation->id} unread_count: " . $conversation->unread_count);
        });

        return $conversations;
    }

    public function updatedSearch()
    {
        $this->refresh();
    }

    public function render()
    {
        return view('livewire.chat-list', [
            'conversations' => $this->allConversations ?? collect(),
        ]);
    }
}
