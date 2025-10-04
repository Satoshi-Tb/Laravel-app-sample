@use(App\Enums\Color)
<div>
    <h1>TODO登録</h1>
    <ul>
        @foreach (Color::cases() as $color)
            <li>{{ $color->display() }}</li>
        @endforeach
    </ul>
</div>
