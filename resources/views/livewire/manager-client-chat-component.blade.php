<div class="chat-container">
    <div class="messages-container" id="messagesContainer">
        @foreach($messages as $message)
            <div class="message {{ $message->sender_id === auth()->id() ? 'message-sent' : 'message-received' }}">
                <div class="message-avatar">
                    <img src="{{ $message->sender->personal_image }}" alt="{{ $message->sender->name }}">
                </div>
                <div class="message-content">
                    <div class="message-header">
                        <span class="message-sender">{{ $message->sender->name }}</span>
                        <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                    </div>
                    <div class="message-text">{{ $message->message }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage" class="message-input-form">
        <textarea 
            wire:model="message" 
            placeholder="Type your message..." 
            class="form-control message-input"
            rows="3"
            required
        ></textarea>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Send
        </button>
    </form>
</div>

<style>
.chat-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    height: 600px;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message {
    display: flex;
    gap: 0.75rem;
    max-width: 70%;
}

.message-sent {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-received {
    align-self: flex-start;
}

.message-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.message-content {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 0.75rem 1rem;
}

.message-sent .message-content {
    background: #0d6efd;
    color: white;
}

.message-header {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 0.25rem;
}

.message-sender {
    font-weight: 600;
    font-size: 0.875rem;
}

.message-sent .message-sender {
    color: rgba(255,255,255,0.9);
}

.message-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.message-sent .message-time {
    color: rgba(255,255,255,0.7);
}

.message-text {
    font-size: 0.9375rem;
    line-height: 1.5;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.message-input-form {
    padding: 1rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 0.75rem;
}

.message-input {
    flex: 1;
    resize: none;
}

.message-input-form button {
    align-self: flex-end;
}
</style>

<script>
    document.addEventListener('livewire:initialized', () => {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });

    Livewire.on('messageSent', () => {
        const container = document.getElementById('messagesContainer');
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    });
</script>
