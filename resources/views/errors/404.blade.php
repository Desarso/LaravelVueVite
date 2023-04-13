@extends('layouts/fullLayoutMaster')

@section('title', 'Error 404')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/pages/error.css')) }}">
@endsection
@section('content')
<!-- error 404 -->
<section class="row flexbox-container">
  <div class="col-xl-7 col-md-8 col-12 d-flex justify-content-center">
    <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
      <div class="card-content">
        <div class="card-body text-center">
          <img src="{{ asset('images/pages/404.png') }}" class="img-fluid align-self-center" alt="branding logo">
          <h1 class="font-large-2 my-1">404 - Página no encontrada</h1>
          <p class="p-2">
            Regrese al login de la aplicación he ingrese sus credenciales.
          </p>
          <a class="btn btn-primary btn-lg mt-2" href="dashboard-tasks">Ir a Whagons</a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- error 404 end -->
@endsection
