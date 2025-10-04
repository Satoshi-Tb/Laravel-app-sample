@extends('layout')

@section('main')
<div>
    <h1>TODO一覧</h1>
    <ui>
        @foreach ($todos as $todo)
            <li>{{ $todo->title }} - {{ $todo->deadline }}</li>
        @endforeach
    </ui>
</div>
@endsection


