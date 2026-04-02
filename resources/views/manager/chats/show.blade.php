@extends('layouts.master')

@section('content')
<div class="app-content">
    <div class="section-header">
        <div>
            <a href="{{ route('manager.chats.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Back to Chats
            </a>
            <h1>Chat about {{ $chat->client->company_name }}</h1>
            <p class="text-muted">
                Between {{ $chat->salesRep->name }} and {{ $chat->manager->name }}
            </p>
        </div>
    </div>

    @livewire('manager-client-chat-component', ['chat' => $chat])
</div>
@endsection
