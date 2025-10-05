@extends('layout')

@section('main')
<div class="w-full min-h-[calc(100dvh-5vh)] px-6 py-16 flex flex-col items-center justify-center md:flex-row md:justify-between gap-16">
    <div class="max-w-xl space-y-6 text-center md:text-left">
        <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold tracking-[0.3em] uppercase text-blue-500 bg-white/70 rounded-full shadow-sm shadow-blue-100">
            COLORFUL TODO
        </span>
        <h1 class="text-4xl md:text-5xl font-semibold leading-tight">
            {{ env('APP_NAME') }}で
            <span class="text-blue-500">今日のタスク</span>をスマートに整理
        </h1>
        <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            カラータグと締切で優先度がひと目でわかるシンプルなタスク管理アプリです。
            新規登録から追加・更新・完了チェックまで、すべてブラウザ上で完結します。
        </p>
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
            <a href="{{ route('todo.index') }}" class="px-5 py-3 text-sm font-semibold text-white bg-blue-500 rounded-full shadow-sm shadow-blue-100 hover:bg-blue-600 transition">
                ToDoを見る
            </a>
            <a href="{{ route('login') }}" class="px-5 py-3 text-sm font-semibold text-blue-600 bg-white rounded-full shadow-sm shadow-blue-100 hover:text-blue-700 transition">
                ログイン
            </a>
            <a href="{{ route('signup') }}" class="px-5 py-3 text-sm font-semibold text-white bg-emerald-500 rounded-full shadow-sm shadow-emerald-100 hover:bg-emerald-600 transition">
                新規登録
            </a>
        </div>
    </div>

    <div class="w-full max-w-md">
        <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur shadow-xl shadow-blue-100 border border-white/60">
            <div class="absolute -top-32 -right-24 w-64 h-64 bg-blue-100 rounded-full blur-3xl opacity-70"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-emerald-100 rounded-full blur-3xl opacity-80"></div>
            <div class="relative z-10 p-8 space-y-6">
                <p class="text-xs font-semibold tracking-[0.2em] text-slate-400 uppercase">FEATURES</p>
                <ul class="space-y-4 text-sm text-slate-600">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-block w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                        カラーラベルで優先度やカテゴリを瞬時に把握。
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-block w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        締切日時を設定し、スケジュール管理をシンプルに。
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-block w-2.5 h-2.5 rounded-full bg-violet-400"></span>
                        ワンクリックで完了状態をトグル。進捗がひと目でわかります。
                    </li>
                </ul>
                <div class="flex items-center gap-3 pt-4">
                    <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center">
                        <x-google-icon name="event_available" class="text-2xl text-blue-500" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">もうタスクを見失わない</p>
                        <p class="text-xs text-slate-500">シンプルな UI と軽快な操作で、日々のやることをすっきり整理。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@php
    // phpinfo()
@endphp
