<?php

namespace App\Providers;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ChatViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('partials.chatClient', function ($view) {
            if (Auth::check()) {
                $userId = Auth::id();

                $conversations = Conversation::with([
                    'client',
                    'messages' => function ($query) {
                        $query->latest();
                    }
                ])
                    ->whereHas('messages', function ($query) use ($userId) {
                        $query->where('receiver_id', $userId);
                    })->get();

                $totalUnreadConversations = $conversations->filter(function ($conversation) {
                    return $conversation->unreadMessagesCount() > 0;
                })->count();

                $view->with('conversations', $conversations)
                    ->with('totalUnreadConversations', $totalUnreadConversations);
            }
        });
    }
}
