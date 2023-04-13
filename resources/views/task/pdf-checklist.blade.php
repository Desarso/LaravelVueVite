<html lang="en">

<head>
  <meta charset="utf-8">
  <title>{{$ticketChecklist->checklist->name}}</title>
  <style>
    
    @page {
      margin-top: 5px;
      margin-left: 20px;
      font-family: "Montserrat", Helvetica, Arial, serif;
    }

    .text-center{
      text-align: center !important;
    }

    .group{
      position: relative;
      display: block;
      padding: 0.75rem 1.25rem;
      margin-bottom: -1px;
      background-color: #fff;
      border: 1px solid rgba(0,0,0,.125);
    }

    .color-value{
      color: #7a777f;
    }
    
  </style>
</head>

<body class="">

  <h1 class="text-center">{{$ticketChecklist->checklist->name}}</h1>

  <hr>
    <div style="display: flex;">
      <div><b>Lugar:</b> <span class="color-value"> {{ $ticketChecklist->ticket->spot->name }} </span> </div>
      <div style="position:absolute; right:0px;"><b>Fecha:</b> <span class="color-value"> {{ $ticketChecklist->ticket->created_at->setTimezone('America/Costa_Rica') }} </span> </div>
    </div>
  <hr style="margin-bottom: 15px;">

  @foreach ($options as $option)

    @switch($option->optiontype)

      @case(6)
      <!-- Header -->
      <div class="header" style="">
        <h3 class="text-center">{{ $option->name }}</h3>
      </div>
      @break

      @case(8)
      <!-- Fixed text -->
      <div class="row">
        <p class="">{{ $option->name }}</p>
      </div>
      @break

      @case(11)
      <!-- Image -->
      <div class="mt-1" style="text-align:center; margin-top: 0px !important;">
        <img class="" src="{{ $option->value }}" alt="avatar" height="160" width="300" max-width="300px;" max-height="160px;"/>
      </div>
      @break

      @case(12)
      <!-- Signature -->
      <p><b>{{ $option->name }}</b></p>
      <img style="" class="rounded img-thumbnail" src="{{ $option->value }}" alt="avatar" height="120" width="300" max-width="300px;" max-height="120px;"/>
      @break

      @case(13)
      <!-- Photo -->
      <p><b>{{ $option->name }}</b></p>
      <a href="{{ $option->value }}" target="_blank">
        <img style="margin-top:30px !important; margin-left: 20px !important;" src="{{ $option->value }}" alt="Foto" height="300px;" width="300px;" max-width="300px;" max-height="300px;">
      </a>

      @break

      @case(14)

      <div>
        <p style="margin: 0px !important;"><b>{{ $option->name }}</b></p>
        <ul style="margin-top: 2px !important;">
          <li class="color-value">{{ (is_null($option->value) || $option->value == "") ? "_______" : gmdate("H:i:s", $option->properties->duration) }}</li>
        </ul>
      </div>

      @break

      @case(16)
        <!-- table -->
        <p class=""><b>{{ $option->name }}</b></p>

        @foreach ($option->children as $child)

        <div class="group" style="margin: 0px !important;">
          @foreach ($child as $cell)

              <h5 style="margin-top: 10px !important; margin-bottom: 1px !important;">{{ $cell->name }}</h5>

              @if ($cell->optiontype != 13)
                <small class="color-value" style="margin: 0px !important;">{{ $cell->value }}</small>
              @else
                <img style="margin-top: 5px !important;" src="{{ $cell->value }}" alt="Foto" width="200px;" height="200px" max-width="200px;" max-height="200px;">
              @endif

          @endforeach
        </div>

        @endforeach

      @break

    @default

    <div>
      <p style="margin: 0px !important;"><b>{{ $option->name }}</b></p>
      <ul style="margin-top: 2px !important;">
        <li class="color-value">{{ (is_null($option->value) || $option->value == "") ? "_______" : $option->value }}</li>
      </ul>
    </div>

    <div>
      @if($option->notes->count() > 0)

        @foreach($option->notes->sortBy('type')->groupby('type') as $key => $group)

          @if ($key == 1)

            <ul style="margin-top:0px !important;">

              <li><b>Notas:</b></li>

              @foreach ($group as $note)

              <li> {{ $note->createdBy->fullname }}: <span class="color-value"> {{ $note->note }} </span> </li>

              @endforeach

            </ul>

          @else

            <ul style="list-style-type:none; margin-top: 50px !important;">

              @foreach ($group as $note)

                @if ($loop->iteration == 1 || $loop->index % 3 == 0)
                  <li>
                @endif

                    <a href="{{ $note->note }}" target="_blank">
                      <img src="{{ $note->note }}" width="200px;" height="200px" max-width="200px;" max-height="200px;">
                    </a>

                @if ($loop->iteration % 3 == 0)
                  </li>
                @endif

              @endforeach
              
            </ul>

          @endif

        @endforeach

      @endif
    </div>

    @endswitch

  @endforeach
</body>

</html>