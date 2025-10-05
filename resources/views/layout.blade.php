<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Round" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+1:wght@100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Murecho:wght@100..900&family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/ts/app.ts'])
    @stack('scripts')
    <title>{{ env('APP_NAME') }}</title>
</head>
<body class="min-h-dvh w-screen bg-[#F2F5F9] text-slate-800 [&>:box-border]">
    @unless (View::hasSection('hide-header'))
        @include('common.header')
    @endunless
    <main class="w-full h-full @unless(View::hasSection('hide-header')) pt-header @endunless">
        @yield('main')
    </main>
</body>
</html>
