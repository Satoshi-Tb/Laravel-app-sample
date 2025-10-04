@php
    use App\Enums\Color;
@endphp

@extends('layout')

@section('main')
<div>
    <h1>ToDo登録</h1>
    <ul>
        @foreach (Color::cases() as $color)
            <li>{{ $color->display() }}</li>
        @endforeach
    </ul>
</div>
@endsection
