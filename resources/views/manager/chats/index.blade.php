@extends('layouts.master')

@section('content')
<x-page-header title="محادثات المدير والمندوبين" subtitle="جميع المحادثات بين المديرين وأعضاء فريقهم" />

@livewire('manager-chat-list')
@endsection
