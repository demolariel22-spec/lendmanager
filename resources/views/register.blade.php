@extends('layout')
@section('content')
    <section>
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh">
            <div class="border border-success rounded-4 p-3" style="width: min(95%, 300px); height: fit-content">
                <div class="d-flex justify-content-center">
                    <img src="{{asset('img/logo.png')}}" width="150px" alt="">
                </div>
                <h2 class="text-center">Register</h2>
                <form method="POST" action="{{route('register.submit')}}" id="register-form">
                    @csrf
                    <div>
                        <label for="username">Username:</label>
                        <input class="form-control border border-2 border-success py-1 " type="text" id="username" name="username" required>
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input class="form-control border border-2 border-success py-1 " type="email" id="email" name="email" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input class="form-control border border-2 border-success py-1" type="password" id="password" name="password" required>
                    </div>
                    <div>
                        <label for="password_confirmation">Confirm Password:</label>
                        <input class="form-control border border-2 border-success py-1" type="password" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary py-1">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('js')
<script>
    document.getElementById('register-form').addEventListener('submit', function(e){
        e.preventDefault();
        let pass = document.getElementById('password');
        let cpass = document.getElementById('password_confirmation')
        let user = document.getElementById('username');

        if(Number(user.value)){
            alert('Username cannot be a number!');
            return;
        }

        if(pass.value !== cpass.value){
            alert('Passwords do not match!');
            return;
        }else if(pass.value.length < 6){
            alert('Password must be at least 6 characters!');
            return;
        }


        this.submit();
    })
</script>
@endsection