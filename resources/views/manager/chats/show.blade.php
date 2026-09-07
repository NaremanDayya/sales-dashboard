@extends('layouts.master')

@section('content')
<x-page-header :title="'محادثة بخصوص ' . $chat->client->company_name" :subtitle="'بين ' . $chat->salesRep->name . ' و ' . $chat->manager->name">
    <x-slot name="actions">
        <a href="{{ route('manager.chats.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-right"></i> رجوع للمحادثات
        </a>
    </x-slot>
</x-page-header>

@livewire('manager-client-chat-component', ['chat' => $chat])
@endsection
