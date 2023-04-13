@extends('layouts/fullLayoutMaster')

@section('title', 'Resumen')

@section('content')
<!-- maintenance -->
<section id="nav-justified">
  <div class="row justify-content-md-center">

    <div class="col-sm-6">
      @if(isset($ticket))
      <div class="card overflow-hidden mx-auto">
        <div class="card-header">
          <h4 class="card-title"><i class="{{ $ticket->item->tickettype->icon }}"
              style="color:{{ $ticket->item->tickettype->color }}"></i> {{ $ticket->name }}</h4>
        </div>
        <div class="card-content">
          <div class="card-body">
            <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab"
                  aria-controls="home-just" aria-selected="true">Detalles</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab"
                  aria-controls="profile-just" aria-selected="false">Notas</a>
              </li>
            </ul>


            <div class="tab-content pt-1">
              <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                <div class="col-12 col-md-12">
                  <h5>{{ $ticket->spot->name }}
                  </h5>
                  <p class="text-muted">Creado por : {{ $ticket->createdby->fullname }} </p>
                  <div class="ecommerce-details-price d-flex flex-wrap">

                    <p class="text-primary font-medium-3 mr-1 mb-0" title="Código">{{ $ticket->code }}</p>

                    <span class="pl-1 font-medium-3 border-left mr-1">
                      <div class="badge badge-pill badge-danger"
                        style="background-color: {{ $ticket->status->color }};">{{ $ticket->status->name}}</div>
                    </span>

                    <span class="pl-1 font-medium-3 border-left mr-1" style="color: {{ $ticket->priority->color }};"
                      title="Estado">{{ $ticket->priority->name}}</span>

                  </div>
                  <hr>
                  <p>{{ $ticket->description }}</p>
                  <p class="font-weight-bold mb-25"> <i class="fas fa-users"></i> {{ $ticket->team->name }}
                  </p>
                  <p class="font-weight-bold"> <i class="far fa-clock"></i> {{ $ticket->created_at }}
                  </p>
                  <hr>
                  <p class="font-weight-bold mb-1">Responsables</p>
                  @forelse($ticket->users as $user)
                  <span>
                    <span class="avatar">
                      <img src="{{ $user->urlpicture }}" height="32" width="32">
                    </span> {{ $user->fullname }}
                  </span><br>
                  @empty
                    <p class="text-muted">No hay responsables</p>
                  @endforelse
                  <hr>
                  <p class="font-weight-bold mb-1">Archivos adjuntos</p>

                  @isset($ticket->files)
                    <input type="file" name="images" class="form-control-file" id="images">
                  @endisset

                  @empty($ticket->files)
                    <p class="text-muted">No hay archivos</p>
                  @endempty
                  
                </div>
              </div>
              <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                <div class="col-lg-12 col-md-12 col-12 overflow-auto">
                  <div class="card chat-widget overflow-auto">
                    <div class="chat-app-window">
                      <div class="user-chats ps overflow-auto">
                        <div class="chats">

                          @forelse ($ticket->notes as $note)

                          <div class="chat chat-left">
                            <div class="chat-avatar">
                              <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title=""
                                data-original-title="">
                                <img src="{{ $note->createdBy->urlpicture }}" alt="avatar" height="40" width="40">
                              </a>
                            </div>
                            <div class="chat-body">
                              <div class="chat-content"
                                style="color:white; background: linear-gradient(118deg, #7367f0, rgba(115, 103, 240, 0.7));">
                                <p style="float:left;" class="font-weight-bold mb-0 mr-2">{{ $note->createdBy->fullname
                                  }}
                                </p>
                                <p style="float:right;" class="font-weight-bold"> {{ $note->created_at }}</p><br>
                                @if($note->type == 1)
                                <p style="float:left;" class="mt-1">{{ $note->note }}</p>
                                @else
                                <img class="mt-2" style="position: static;" src="{{$note->note}}" alt="avatar"
                                  height="300" width="300" />
                                @endif
                              </div>
                            </div>
                          </div>

                          @empty
                          <div class="alert alert-warning" role="alert">
                            <h4 class="alert-heading">Mensaje</h4>
                            <p class="mb-0">
                              No hay notas registradas
                            </p>
                          </div>
                          @endforelse

                        </div>
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                          <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps__rail-y" style="top: 0px; right: 0px;">
                          <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @else
      <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">Mensaje</h4>
        <p class="mb-0">
          No se econtró ningún registro
        </p>
      </div>
      @endif
    </div>
  </div>
</section>
<!-- maintenance end -->
@endsection

@section('page-script')
<script>

  const initialPreview = [];

  const initialPreviewConfig = [];

  const { files } = {!! $ticket !!};    

  const filesArray = files.split(",");

  $.each(filesArray, function(index, item) {
    initialPreview.push(item);
    initialPreviewConfig.push({ caption: "Imagen.jpg", downloadUrl: item, width: "120px", key: index});
  });

  $(document).ready(function() {

    $("#images").fileinput({
        initialPreview: initialPreview,
        initialPreviewAsData: true,
        initialPreviewConfig: initialPreviewConfig,
        theme: 'fa',
        language: 'es',
        uploadUrl: '#',
        actionUpload: false,
        showRemove: false,
        showUpload: false,
        showBrowse: false,
        showUploadedThumbs: false,
        dropZoneEnabled: false,
        overwriteInitial: true,
        mainClass: "input-group-md",
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        fileActionSettings: { 
            showUpload: false,
            showRemove: false, 
        }
    });

  });
  
</script>
@endsection