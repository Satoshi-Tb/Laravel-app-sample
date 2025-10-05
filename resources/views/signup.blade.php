@extends('layout')

@section('main')
<div class="w-full h-auto pt-14 pb-10 flex justify-center items-start">
    <div class="h-auto w-[70vh] py-10 px-14 bg-white border-solid border-[0.5px] border-gray-300 rounded-lg shadow-md shadow-gray-200">
        <form action="{{ route('user.create') }}" method="POST">
            @csrf
            <h2 class="text-xl font-semibold tracking-wider">
                Sign Up
            </h2>
            <div class="w-full h-[1px] mt-2 bg-gray-300"></div>
            <div class="mt-9 flex gap-6 flex-col">
            <input type="text" name="username" value="{{ @old('username') }}" placeholder="ユーザー名" autocomplete="off" class="p-3 text-xs rounded-md border border-gray-400 focus:outline-blue-500">
            @error('username')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            <input type="email" name="email" value="{{ @old('email') }}" placeholder="メールアドレス" autocomplete="off" class="p-3 text-xs rounded-md border border-gray-400 focus:outline-blue-500">
            @error('email')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            <input type="password" name="password" value="{{ @old('password') }}" placeholder="パスワード" autocomplete="off" class="p-3 text-xs rounded-md border border-gray-400 focus:outline-blue-500">
            @error('password')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            <input type="password" name="password_confirmed" value="{{ @old('password_confirmed') }}" placeholder="パスワード（確認用）" autocomplete="off" class="p-3 text-xs rounded-md border border-gray-400 focus:outline-blue-500">
            @error('password_confirmed')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            </div>
            <div class="mt-10 flex justify-end">
                <x-submit-button>
                    登録
                </x-submit-button>
            </div>
        </form>
    </div>
</div>
@endsection
