<header class="fixed top-0 left-0 bg-inherit z-000">
    <div class="w-screen h-header pl-5 pr-5 flex justify-between items-center">
        <div>
            <a href="{{ route('home') }}">
                <h1 class="text-3xl font-bold">{{ env('APP_NAME') }}</h1>
            </a>
        </div>

        <div class="flex gap-2">
            <a
                class="transition duration-300 ease-in-out hover:scale-[1.2] hover:text-blue-500"
                href="{{ route('todo.new') }}"
            >
                <span class="material-icons-round text-2xl">add</span>
            </a>
            <a
                class="transition duration-300 ease-in-out hover:scale-[1.2] hover:text-blue-500"
                href="{{ route('todo.index') }}"
            >
                <span class="material-icons-round text-2xl">list</span>
            </a>
        </div>
    </div>
</header>
