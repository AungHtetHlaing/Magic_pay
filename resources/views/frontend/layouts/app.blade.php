<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/backend.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
</head>

<body>
    <div id="app">
        {{-- <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav> --}}

        <nav class=" d-flex justify-content-center align-items-center position-fixed top-0 w-100 bg-primary-subtle py-2"
            style="z-index: 1000">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-4 text-center">
                        @if (!request()->is('/'))
                            <p class=" mb-0 back-btn" style="cursor: pointer !important"><i class="fa-solid fa-angle-left"></i></p>
                        @endif
                    </div>
                    <div class="col-4 text-center">
                        <h4 class="mb-0">@yield('title')</h4>
                    </div>
                    <div class="col-4 text-center ">
                        <a href="{{route('notification')}}" class=" text-decoration-none d-block position-relative">
                            <i class="fa-solid fa-bell fs-5"></i>
                            @if ($unread_notifications_count > 0)

                            <span class=" badge bg-danger rounded-pill position-absolute" style="top: -8px;right:120px">{{$unread_notifications_count}}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <main class=" py-5">
            @yield('content')
        </main>
    </div>

    <a class="bg-white position-fixed mx-auto d-flex justify-content-center" style="bottom: 35px; right:0; left:0; width:60px; height:60px; border-radius: 50%; z-index:3">
        <div class="bg-primary-subtle d-flex justify-content-center align-items-center" style="width: 55px; height:55px ; border-radius: 50%">
            <i class="fa-solid fa-qrcode fs-5"></i>
        </div>
    </a>
    <footer class="d-flex justify-content-center position-fixed bottom-0 w-100 bg-primary-subtle pt-2">
        <div class="col-md-8">
            <div class="row">
                <div class="col-3 text-center">
                    <a href="{{ route('home') }}" class=" text-decoration-none d-block">
                        <i class="fa-solid fa-house fs-5"></i>
                        <p class=" mb-0">Home</p>
                    </a>
                </div>
                <div class="col-3 text-center">
                    <a href="{{route('wallet')}}" class=" text-decoration-none d-block">
                        <i class="fa-solid fa-wallet fs-5"></i>
                        <p class=" mb-0">Wallet</p>
                    </a>
                </div>
                <div class="col-3 text-center">
                    <a href="{{route('transaction')}}" class=" text-decoration-none d-block">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                        <p class=" mb-0">Transaction</p>
                    </a>
                </div>
                <div class="col-3 text-center">
                    <a href="{{ route('profile') }}" class=" text-decoration-none d-block">
                        <i class="fa-solid fa-user fs-5"></i>
                        <p class=" mb-0">Account</p>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="{{asset('js/jscroll.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {

            let token = document.head.querySelector('meta[name="csrf-token"]');
            if (token) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-Token': token.content,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    }
                });
            }

            $('.back-btn').on('click', function() {
                window.history.go(-1);
                return false;
            });
        })
    </script>

    @stack('script')
</body>

</html>
