<header class="fixed top-0 left-0 bg-inherit z-000">
    <div class="w-screen h-header pl-5 pr-5 flex justify-between items-center">
        <div>
            <a href="{{ route('home') }}">
                <h1 class="text-3xl font-bold">{{ env('APP_NAME') }}</h1>
            </a>
        </div>

        <div class="flex items-center gap-4">
            @if (Route::is('home'))
                <x-link href="{{ route('login') }}">
                    <p class="text-xs">ログイン</p>
                </x-link>
                <x-link href="{{ route('signup') }}">
                    <p class="text-xs">新規登録</p>
                </x-link>
            @else
                @auth
                    <span class="px-3 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">
                        {{ auth()->user()->name }} さんがログイン中
                    </span>
                    <x-link href="{{ route('todo.new') }}">
                        <x-google-icon name="add" class="text-2xl"/>
                    </x-link>
                    <x-link href="{{ route('todo.index') }}">
                        <x-google-icon name="list" class="text-2xl"/>
                    </x-link>
                    <x-link href="{{ route('auth.logout') }}">
                        <p class="text-xs">ログアウト</p>
                    </x-link>
                @endauth
            @endif
        </div>
    </div>
</header>
