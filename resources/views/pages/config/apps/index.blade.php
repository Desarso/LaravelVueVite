@section('icon')
<i class="fad fa-shapes"></i>
@endsection
@extends('layouts/contentLayoutMaster') @section('title', __('locale.' . 'Apps'))

@section('content')
<style>
  .card {
    transition: all .3s ease-in-out;
  }

  .card:hover {
    box-shadow: rgba(0, 0, 0, 0.22) 0px 19px 43px;
    transform: translate3d(0px, -1px, 0px);
    font-size: 1.5rem;
  }

  .carddisabled {
    opacity: 0.4;
    background-color: lightgray;
  }

</style>
<div class="container-fluid">

  <div class="row">
    @foreach ($apps as $app)
        <div class="col-md-3 col-lg-2">

            @if ($app->enabled)
        
            <div class="configcard card waves-effect waves-dark carddisabled{{$app->enabled}}" style="cursor: pointer" 
                data-configlink="{{'config-' . str_replace(' ', '', strtolower($app->name)) }}">
                 
            @else
            <div class="configcard card waves-effect waves-dark carddisabled{{$app->enabled}}" style="cursor: pointer" 
                data-configlink="#">   
            @endif
            <div class="card-body">
                            <div class=" icon-with-dropdown d-flex justify-content-between">
                                <i class="{{ $app->icon }}" style="font-size: 4rem; color: {{ $app->color }}"></i>
                                <div class=" hidden dropdown-items-wrapper">
                                    <div class="feather icon-more-vertical text-muted" id="dropdownMenuLink1" role="button" data-toggle="dropdown" aria-expanded="false">
                                    </div>
                        
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink1">
                                        <a class="dropdown-item" href="javascript:void(0)"> <i class="feather icon-plus"></i> Configure</a>
                                        <a class="dropdown-item" href="javascript:void(0)"> <i class="feather icon-edit-2"></i> Export</a>
                                    </div>
                                </div>
                            </div>
                            <div class="storage-name my-1">
                                <h5> {{ __('locale.' . $app->name ) }}</h5>
                            </div>
                            <div class="about-capecity">
                               
                            </div>
                        </div>


            </div>  <!-- end card -->
        </div>  <!-- end column -->
    @endforeach

  </div>
</div>





@endsection

@section('page-script') {{-- Page js files --}}

@endsection
