@extends('layouts/fullLayoutMaster')

@section('title', 'Bienvenido')



@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(('css/pages/authentication.css')) }}">

@endsection

@section('content')
<section>
<div class='container-fluid login px-0' style='height: 100vh;'>

<div class='d-lg-flex'>

  <div class='col-lg-6 d-flex justify-content-center align-items-center' style='background-color: #27C1A7; height: 100vh;'>
    <div class='d-flex flex-column px-lg-0 px-3'>
        <div class='d-flex justify-content-center py-5' style='gap: 2%;'>
            <img class='img-fluid' src="{{ asset('images/login/logow.svg') }}" alt="">
            <h1 style='color: #ffffff; font-weight: 700;'>Whagons 5</h1>
        </div>
        <div class='py-5'>
            <img class='img-fluid' src="{{ asset('images/login/background-login.svg') }}" alt="">
        </div>
        <div class='py-5 text-center'>
            <h2 style='color: #ffffff; font-weight: 600;'>La app que optimiza las tareas de la empresa</h2>
        </div>
    </div>
  </div>

  <div class='col-lg-6 flex-column justify-content-center align-items-center' style='height: 100vh; background-color: #ffffff;'>

    <div class='flex-column justify-content-center align-items-center m-lg-5 m-3 p-lg-5 p-3 rounded-3' style='background-color: #FAFAFA; height: 90vh;'>
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class=''>
          <h1 class="h3 mb-3 fw-normal text-center" style='color: #211D29; font-weight: 600 !important; font-size: 32px;'>Iniciar Sesión</h1>
        </div>

        <div class='d-flex justify-content-center'>
          <div class='col-xl-8 col-12'>
            <div class=''>
              <label for="username" class='py-2' style='color: #7A777F; font-weight: 600 !important; font-size: 14px;'>Usuario</label> 
              <div class="form-floating">
                <input type="text" class="form-control @error('username') is-invalid @enderror border-0" id="username" name="username"
                  placeholder="" value="{{ old('username') }}">
              </div>
            </div>

            <div class='py-2'>
              <label for="password" class='py-2' style='color: #7A777F; font-weight: 600 !important; font-size: 14px;'>Password</label>
              <div class="form-floating">
                <input type="password" class="form-control @error('password') is-invalid @enderror border-0" id="password"
                  placeholder="" name="password">
              </div>
            </div>

            @error('username')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
            @error('password')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
            <div class="checkbox mb-3">
              <label style='color: #7A777F; font-weight: 600 !important; font-size: 14px;'> 
                <input type="checkbox" value="remember-me"> Recordame
              </label>
            </div>
            <div class=''>
              <button class="w-100 btn btn-lg btn-primary border-0" style='background-color: #27C1A7; font-weight: 700 !important;' type="submit">Iniciar Sesión</button>
            </div>

          </div>
        </div>


        <div class='d-flex justify-content-center py-3'>

          <div class='col-xl-8 col-12'>

          <!--
          <div class='py-3'>
              <div class="position-relative d-flex justify-content-start align-items-center">
                <button style='color: #7A777F; font-weight: 600 !important; font-size: 18px;' class="form-control login text-center form-control-lg border-0" type="text" placeholder="Iniciar Sesión con Google"> <img class='px-2' alt="" src="{{ asset('images/login/icon-google.svg') }}" alt="">Iniciar Sesión con Google</button>
              </div>
            </div>
          -->
          
            <div class='py-3'>
              <a onclick="goToMicrosoftLogin()" class="form-control login text-center form-control-lg border-0" style='color: #7A777F; font-weight: 600 !important; font-size: 18px; text-decoration: none;'>
                <img class='px-2' alt="" src="{{ asset('images/login/icon-microsoft.svg') }}" alt="">
                Iniciar Sesión con Microsoft
              </a>
            </div>
          </div>
        </div>
        <input class="form-control" id="timezone" name="timezone" type="text" readonly="readonly" hidden>
      </form>      
    </div>

  </div>

</div>

</div>
</section>

@endsection

@section('page-script')
<script>
  $('#timezone').val(moment.tz.guess());

  function goToMicrosoftLogin() {
      let url = window.location.href;
      url = url.replace("/login", "") + "/login365BE";
      
      console.log("goToMicrosoftLogin goToMicrosoftLogin");
      console.log( url );
      window.location = `https://webadmin.whagons.com/ms-signin?url=${url}`;
    }
</script>
@endsection