@extends('layout')

@section('main')
<div class="w-full h-full flex justify-center">
    <h1 class="text-5xl font-semibold -translate-header tracking-widest" style="text-shadow: 10px 10px 5px rgba(0, 0, 0, 0.2)">
        {{ env('APP_NAME')}}
    </h1>
</div>
@endsection
@php
    // phpinfo()
@endphp
