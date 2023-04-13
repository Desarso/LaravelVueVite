@extends('layouts/fullLayoutMaster')

@section('title', 'Bienvenido')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(mix('css/pages/authentication.css')) }}">

@endsection

@section('content')
<section>
  <div class="row mt-5">
    <div class="col-xl-12 col-11 d-flex justify-content-center">
      <div class="card bg-authentication rounded-0 mb-0">
        <div class="row m-0">
          <div class="col-lg-12 col-12 p-0 mt-5">
            <div id="card-login" class="card rounded-0 mb-0 px-2">
              <div class="card-header pb-1 text-center d-block">
                <div class="card-title">
                  <img class="mb-5" src="{{ asset('images/pages/whagons-login.png') }}" alt="branding logo"
                    height="200px">
                  <h3 id="title-login" class="">Bienvenido</h3>
                </div>
              </div>
              <div class="card-content">
                <div class="card-body pt-1">

                  <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                      <input id="username" type="text"
                        class="input-login form-control-lg form-control @error('username') is-invalid @enderror" name="username"
                        placeholder="Usuario" value="{{ old('username') }}" required autocomplete="email" autofocus>

                      <div class="form-control-position mt-0">
                        <i class="icon-login feather icon-user"></i>
                      </div>
                      @error('username')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </fieldset>
                    <fieldset class="form-label-group position-relative has-icon-left">
                      <input id="password" type="password"
                        class="input-login form-control-lg form-control @error('password') is-invalid @enderror" name="password"
                        placeholder="Contraseña" required autocomplete="current-password">

                      <div class="form-control-position">
                        <i class="icon-login feather icon-lock"></i>
                      </div>
                      @error('password')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </fieldset>
                    <input class="form-control hidden" id="timezone" name="timezone" type="text" readonly="readonly">
                      <div>
                        <a class="" href="ms-signin" style="color: white;">
                          <img style="height: 50px;" src="https://dingdonecdn.nyc3.digitaloceanspaces.com/general/whagons3/microsoft.png" alt="">
                          Login con Microsoft
                        </a>
                      </div>
                    <button id="btn-login" type="submit" class="btn btn-primary btn-lg btn-block">INGRESAR</button>
                  </form>

                </div>
              </div>
              <div class="login-footer  mt-5" style="padding-bottom: 2rem !important">
                <div class="footer-btn d-inline">
                  <p id="text-version" class="card-text text-center">Versión 3.0</p>
                  <hr>
                  <p id="text-client" class="card-text text-center">{{ json_decode($organization)->name }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@section('page-script')
<script>
  $('#timezone').val(moment.tz.guess());
</script>
@endsection