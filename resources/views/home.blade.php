@extends('layout')

@section('hide-header', true)

@section('main')
<div class="w-full h-[50dvh] flex flex-col items-center justify-center gap-10 px-6 py-20 text-center">
    <div class="max-w-3xl space-y-6">
        <h1 class="text-4xl md:text-5xl font-semibold leading-tight">
            {{ env('APP_NAME') }}で
            <span class="text-blue-500">タスク管理をシンプルに。</span>
        </h1>
        <p class="text-sm md:text-base text-slate-600 leading-relaxed">
            カラータグでカテゴリを瞬時に把握し、締切と進捗をワンクリックで更新。
            直感的で軽快な UI が日々のタスク整理をサポートします。
        </p>
    </div>
    <div class="flex flex-col items-center justify-center gap-4">
        <a href="{{ route('login') }}" class="px-6 py-3 text-sm font-semibold text-white bg-blue-500 rounded-full shadow-sm shadow-blue-100 hover:bg-blue-600 transition">
            ログイン
        </a>
        <a href="{{ route('signup') }}" class="text-sm text-blue-500 hover:underline">
            新規登録
        </a>
    </div>
</div>

<section class="w-full px-6 pb-20">
    <div class="max-w-4xl mx-auto space-y-8">
        <div class="text-center space-y-3">
            <h2 class="text-2xl md:text-3xl font-semibold text-slate-800">アプリの特徴</h2>
            <p class="text-sm md:text-base text-slate-600">見やすく・迷わず・すぐ行動できる UI を目指しました。</p>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            <div class="h-full rounded-2xl bg-white/80 backdrop-blur shadow-md shadow-blue-100 border border-white/70 p-6 text-left space-y-4">
                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <x-google-icon name="palette" class="text-xl" />
                </div>
                <h3 class="text-base font-semibold text-slate-700">カラータグで分類</h3>
                <p class="text-sm text-slate-600 leading-relaxed">色でカテゴリや優先度を表現。リスト全体を俯瞰しても迷いません。</p>
            </div>
            <div class="h-full rounded-2xl bg-white/80 backdrop-blur shadow-md shadow-blue-100 border border-white/70 p-6 text-left space-y-4">
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <x-google-icon name="schedule" class="text-xl" />
                </div>
                <h3 class="text-base font-semibold text-slate-700">締切管理が簡単</h3>
                <p class="text-sm text-slate-600 leading-relaxed">日付と時間をセットするだけで、期限順に並び替えて表示します。</p>
            </div>
            <div class="h-full rounded-2xl bg-white/80 backdrop-blur shadow-md shadow-blue-100 border border-white/70 p-6 text-left space-y-4">
                <div class="w-10 h-10 rounded-full bg-violet-500/10 flex items-center justify-center text-violet-500">
                    <x-google-icon name="task_alt" class="text-xl" />
                </div>
                <h3 class="text-base font-semibold text-slate-700">ワンクリックで完了</h3>
                <p class="text-sm text-slate-600 leading-relaxed">チェックボックスや編集ボタンで進捗更新。ストレスのない操作感です。</p>
            </div>
        </div>
    </div>
</section>
@endsection
