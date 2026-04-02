<div class="chat-list-container">
    <div class="chat-list-header">
        <input type="text" wire:model.live="search" placeholder="Search chats..." class="form-control">
    </div>

    <div class="chat-list">
        @forelse($chats as $chat)
            <a href="{{ route('manager.chats.show', $chat) }}" class="chat-item {{ $chat->unread_count > 0 ? 'unread' : '' }}">
                <div class="chat-avatar">
                    <img src="{{ $chat->client->company_logo }}" alt="{{ $chat->client->company_name }}">
                </div>
                <div class="chat-info">
                    <div class="chat-header">
                        <h4>{{ $chat->client->company_name }}</h4>
                        @if($chat->unread_count > 0)
                            <span class="badge badge-primary">{{ $chat->unread_count }}</span>
                        @endif
                    </div>
                    <p class="chat-participants">
                        {{ $chat->salesRep->name }} ↔ {{ $chat->manager->name }}
                    </p>
                    @if($chat->latestMessage)
                        <p class="chat-preview">{{ Str::limit($chat->latestMessage->message, 50) }}</p>
                        <span class="chat-time">{{ $chat->latestMessage->created_at->diffForHumans() }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="empty-state">
                <i class="fas fa-comments fa-3x text-muted"></i>
                <p>No chats yet.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
.chat-list-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    overflow: hidden;
}

.chat-list-header {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.chat-list {
    max-height: 600px;
    overflow-y: auto;
}

.chat-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s;
}

.chat-item:hover {
    background-color: #f8f9fa;
}

.chat-item.unread {
    background-color: #e7f3ff;
}

.chat-avatar img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.chat-info {
    flex: 1;
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}

.chat-header h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.chat-participants {
    font-size: 0.75rem;
    color: #6c757d;
    margin: 0 0 0.25rem 0;
}

.chat-preview {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
}

.chat-time {
    font-size: 0.75rem;
    color: #adb5bd;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}
</style>
