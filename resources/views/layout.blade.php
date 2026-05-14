<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dept Management|POS</title>
    <link rel="stylesheet" href="{{asset('bootstrap/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="icon" href="{{asset('img/logo.png')}}">
    <style>

    </style>
    @yield('style')
</head>
<body class="p-0 m-0" style="height: 100vh">
    <div class="position-absolute z-3 top-0 end-0">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{$error ?? '--'}}</li>
                    @endforeach
                </ul>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <ul>
                    <li>{{session('error')}}</li>
                </ul>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <ul>
                    <li>{{session('success')}}</li>
                </ul>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
    @yield('modal')
    <div class="container">
        <div class="home-content p-3">
            @yield('content')
        </div>
    </div>
    <script src="{{asset('bootstrap/bootstrap.bundle.min.js')}}"></script>
    @yield('js')
</body>
</html>