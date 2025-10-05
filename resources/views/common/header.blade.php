<header class="fixed top-0 left-0 bg-inherit z-000">
    <div class="w-screen h-header pl-5 pr-5 flex justify-between items-center">
        <div>
            <a href="{{ route('home') }}">
                <h1 class="text-3xl font-bold">{{ env('APP_NAME') }}</h1>
            </a>
        </div>

        <div class="flex items-center gap-4">
            @auth
                <span class="px-3 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">
                    {{ auth()->user()->name }} さんがログイン中
                </span>

                <x-link href="{{ route('todo.new') }}" class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-50">
                    <x-google-icon name="add" class="text-2xl"/>
                </x-link>
                <x-link href="{{ route('todo.index') }}" class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-50">
                    <x-google-icon name="list" class="text-2xl"/>
                </x-link>
            @else
                <span class="px-3 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full">
                    未ログイン
                </span>

                <x-link href="{{ route('login') }}" class="px-3 py-1 text-sm font-semibold text-blue-600 border border-blue-400 rounded-full">
                    ログイン
                </x-link>
                <x-link href="{{ route('signup') }}" class="px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded-full">
                    新規登録
                </x-link>
            @endauth
        </div>
    </div>
</header>
