<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>{{$ticketChecklist->checklist->name}}</title>
  
  <style>
    
    @page {
      margin:0px;
    }

    .clearfix:after {
      content: "";
      display: table;
      clear: both;
    }

    a {
      color: #5D6975;
      text-decoration: underline;
    }

    body {
      position: relative;
      width: 21cm;
      height: 29.7cm;
      margin: 0 auto;
      color: #001028;
      background: #FFFFFF;
      font-family: Arial, sans-serif;
      font-size: 12px;
      font-family: Arial;
    }

    header {
      padding: 10px 0;
      margin-bottom: 30px;
    }

    #logo {
      text-align: center;
      margin-bottom: 10px;
    }

    .section {
      text-align: center;
      font-size: 15px !important;
    }

    #logo img {
      width: 90px;
    }

    h1 {
      border-top: 1px solid #5D6975;
      border-bottom: 1px solid #5D6975;
      color: #5D6975;
      font-size: 2.4em;
      line-height: 1.4em;
      font-weight: normal;
      text-align: center;
      margin: 0 0 20px 0;
    }

    #project {
      float: left;
    }

    #project span {
      color: #5D6975;
      text-align: right;
      width: 52px;
      margin-right: 10px;
      display: inline-block;
      font-size: 1em;
    }

    #company {
      float: right;
      text-align: right;
    }

    #project div,
    #company div {
      white-space: nowrap;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
      margin-bottom: 20px;
    }

    table tr:nth-child(2n-1) td {
      background: #F5F5F5;
    }

    table th,
    table td {
      text-align: center;
    }

    table th {
      padding: 5px 20px;
      color: #5D6975;
      border-bottom: 1px solid #C1CED9;
      white-space: nowrap;
      font-weight: normal;
    }

    table .service {
      text-align: left;
    }

    table .desc {
      text-align: right;
    }

    table td {
      padding: 20px;
      /*text-align: right;*/
    }

    table td.service,
    table td.desc {
      vertical-align: top;
    }

    footer {
      color: #5D6975;
      width: 100%;
      height: 30px;
      position: absolute;
      bottom: 0;
      border-top: 1px solid #C1CED9;
      padding: 8px 0;
      text-align: center;
    }
  </style>
</head>

<body>

  <header class="clearfix">
    <!--
      <div id="logo">
        <img src="logo.png">
      </div>
      -->
    <h1>{{$ticketChecklist->checklist->name}}</h1>
    <div id="project">
      <div><span><b>Lugar</b></span> {{$ticketChecklist->ticket->spot->name}}</div>
      <div><span><b>Fecha</b></span> {{$ticketChecklist->ticket->created_at->setTimezone('America/Costa_Rica')}}</div>
    </div>
  </header>
  <main>
    <table>
      <thead>
        <tr>
          <th class="service"></th>
          <th class="service">Valor</th>
          
          @if($ticketChecklist->ticket->approved != null)
            <th class="service">Evaluación</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach ($options as $option)
        <tr>
          @switch($option->optiontype)

          @case(6)
          <td class="section" colspan="2"><h3>{{ $option->name }}</h3></td>
          @break

          @case(8)
          <td class="" colspan="2">{{ $option->name }}</td>
          @break

          @case(11)
          <td colspan="2">
            <img style="display: block;" class="rounded mx-auto d-block img-thumbnail" src="{{ $option->value }}"
              alt="avatar" height="120" width="300" />
          </td>
          @break

          @case(12)
          <td class="service"><b>{{ $option->name }}</b></td>
          <td>
            <img style="" class="rounded img-thumbnail" src="{{ $option->value }}"
              alt="avatar" height="120" width="300" />
          </td>
          @break

          @case(13)
          <td class="service"><b>{{ $option->name }}</b></td>
          <td>
            <img style="" class="rounded img-thumbnail" src="{{ $option->value }}"
              alt="avatar" height="120" width="300" />
          </td>
          @break

          @case(14)
          <td class="service">
            <b>{{ $option->name }}</b>
          </td>
          <td class="service">
            {{ gmdate("H:i:s", $option->properties->duration) }}
          </td>
          @break

          @default
            <td class="service">
              <b>{{ $option->name }}</b>

              @if($option->notes->count() > 0)


              @foreach($option->notes->sortBy('type')->groupby('type') as $key => $group)
                @if ($key == 1)

                  <ul>

                    <li><b>Notas:</b></li>

                    @foreach ($group as $note)

                      <li>{{ $note->createdBy->fullname }}: {{ $note->note }}</li>

                    @endforeach

                  </ul>

                @else
                
                <ul style="list-style-type:none; margin-top:50px !important;">
                  @foreach ($group as $note)

                    @if ($loop->iteration % 2 != 0)
                      <li>
                    @endif
                    <a href="{{ $note->note }}" target="_blank"> 
                      <img src="{{ $note->note }}" width="200px;" height="200px" max-width="200px;" max-height="200px;">
                    </a>
                    @if ($loop->iteration % 2 == 0)
                      </li>
                    @endif

                  @endforeach
                </ul>

                @endif

              @endforeach

              @endif
            </td>
            <td class="service">{{ $option->value }}</td>
          @endswitch

          @if($ticketChecklist->ticket->approved != null)
            <td class="service">{{ $option->approved }}</td>
          @endif
        </tr>
        @endforeach
        <tr>
          <td class="service"><b>Promedio:</b></td>
          <td class="service"><b>{{ $averages['average'] }}</b></td>
          @if($averages['approved'] != 0)
            <td class="service"><b>{{ $averages['approved'] }}</b></td>
          @endif
        </tr>
      </tbody>
    </table>
  </main>
  <footer>
    Formulario generado por Whagons International.
  </footer>
</body>

</html>