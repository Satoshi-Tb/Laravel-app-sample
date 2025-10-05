@use(Illuminate\Support\Carbon)

@push('scripts')
    @vite('resources/ts/todo/toggle.ts')
@endpush

@extends('layout')

@section('main')
<div class="w-full h-auto pt-14 pb-10 flex flex-col justify-start items-center gap-6">

    @foreach ($todos as $todo)

        <div class="h-auto w-[70vh] py-5 pl-13 pr-4 border-solid border-[0.5px] border-gray-300 rounded-lg shadow-md shadow-gray-200 relative overflow-hidden">
            <div class="w-[7%] h-full absolute top-0 left-0" style="background-color: {{ $todo->color }}"></div>
            <form action="{{ route('todo.delete') }}" method="POST">
                @method('DELETE')
                @csrf
                <input type="number" name="id" value="{{ $todo->id }}" class="h-0 invisible">
                <div class="flex items-start gap-6 ml-7">
                    <div class="mt-2">
                        <input
                            id="todo-{{ $todo->id }}"
                            type="checkbox"
                            @checked($todo->done)
                            class="w-[25px] h-[25px] cursor-pointer todo-done"
                            todo-id="{{ $todo->id }}"
                        >
                    </div>
                    <div class="flex-1 flex flex-col items-start gap-3">
                        <label for="todo-{{ $todo->id }}" class="text-md font-semibold tracking-wider cursor-pointer">
                            {{ $todo->title }}
                        </label>
                        @if ($todo->memo !== null)
                            <span class="text-xs">
                                {{ $todo->memo }}
                            </span>
                        @endif
                        <div class="flex gap-2">
                            <x-google-icon name="schedule" class="text-[20px]! text-gray-400"/>
                            <span class="text-xs text-gray-400">
                                {{ Carbon::create($todo->deadline)->isoFormat('YYYY/MM/DD（ddd）HH:mm') }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-auto flex items-start gap-2">
                        <x-link
                            href="{{ route('todo.edit', ['id' => $todo->id]) }}"
                            class="px-4 py-2 text-xs font-semibold text-white bg-blue-500 rounded-md shadow-sm shadow-gray-200 hover:bg-blue-600"
                        >
                            編集
                        </x-link>
                        <button
                            type="submit"
                            class="px-4 py-2 text-xs font-semibold text-white bg-red-500 rounded-md shadow-sm shadow-gray-200 hover:bg-red-600"
                            onclick="return confirm('削除しますか？');"
                        >
                            削除
                        </button>
                    </div>
                </div>
            </form>
        </div>

    @endforeach

</div>
@endsection
