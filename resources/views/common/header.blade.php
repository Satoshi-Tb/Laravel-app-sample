<header class="fixed top-0 left-0 bg-inherit z-000">
    <div class="w-screen h-header pl-5 pr-5 flex justify-between items-center">
        <div>
            <a href="{{ route('home') }}">
                <h1 class="text-3xl font-bold">{{ env('APP_NAME') }}</h1>
            </a>
        </div>

        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
            @endauth

            <x-link href="{{ route('todo.new') }}">
                <x-google-icon name="add" class="text-2xl"/>
            </x-link>
            <x-link href="{{ route('todo.index') }}">
                <x-google-icon name="list" class="text-2xl"/>
            </x-link>
        </div>
    </div>
</header>
