@extends('layouts.master')

@section('content')
<div class="app-content">
    <div class="section-header">
        <h1>Manager-Rep Chats</h1>
    </div>

    @livewire('manager-chat-list')
</div>
@endsection
