<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 antialiased leading-none">
    <div id="app" class="h-screen">
        <nav class="fixed w-full bg-gray-900 shadow py-6 z-10">
            <div class="container mx-auto px-6 md:px-0">
                <div class="flex items-center justify-center">
                    <div class="mr-6">
                        <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-100 no-underline">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                    <div class="flex-1 text-right">
                        @guest
                        @else
                            <span class="text-gray-300 text-sm pr-4">{{ Auth::user()->name }}</span>

                            <a href="{{ route('admin.logout') }}"
                               class="no-underline hover:underline text-gray-300 text-sm p-3"
                               onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
                                {{ csrf_field() }}
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex flex-grow">
            @if (!Request::is('admin/login'))
                <div
                    class="fixed top-0 bottom-0 w-64 mt-16 bg-gray-800 text-white shadow lg:block"
                >
                    <div class="my-1 h-full overflow-y-scroll">
                        <a
                            href="/"
                            class="flex justify-between items-center px-4 py-4 font-bold text-sm hover:bg-gray-900 border-b border-gray-600"
                        >
                            <span>
                                ダッシュボード
                            </span>
                        </a>
                        <a
                            href="/"
                            class="flex justify-between items-center px-4 py-4 font-bold text-sm hover:bg-gray-900 border-b border-gray-600"
                        >
                            <span>
                                ニュース管理
                            </span>
                        </a>
                    </div>
                </div>
            @endif
            <div
                class="w-full overflow-hidden mt-16 px-2 py-4 lg:px-12 lg:py-8 {{ Request::is('admin/login') ? '' : 'ml-64' }}"
            >
                @yield('content')
            </div>
        </div>

        
    </div>
</body>
</html>
