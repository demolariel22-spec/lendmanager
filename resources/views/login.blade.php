@extends('layout')
@section('content')
    <section>
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh">
            <div class="border border-success rounded-4 p-3" style="width: min(96%, 300px); height: fit-content">
                <div class="d-flex justify-content-center" style="border: 1px solid black">
                    <img src="{{asset('img/logo.png')}}" width="150px" alt="">
                </div>
                <h2 class="text-center">Login</h2>
                <form method="POST" action="{{route('login.submit')}}">
                    @csrf
                    <div>
                        <label for="email">Email:</label>
                        <input class="form-control border border-2 border-success py-1 " type="email" id="email" name="email" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input class="form-control border border-2 border-success py-1" type="password" id="password" name="password" required>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <a href="{{ route('register') }}" class="btn btn-link py-1">Register</a>
                        <button type="submit" class="btn btn-primary py-1">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection