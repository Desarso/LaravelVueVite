<html lang="en">

<head>
  <meta charset="utf-8">

  <style>
    @page {
      margin-top: 5px;
      margin-left: 20px;
      font-family: "Montserrat", Helvetica, Arial, serif;
    }

    .text-center {
      text-align: center !important;
    }

    .color-value {
      color: #7a777f;
    }
  </style>
</head>

<body class="">

  <hr>
  <div style="display: flex;">
    <div><b>Checklist:</b> <span class="color-value"> {{ $checklist }} </span> </div>
  </div>
  <hr style="margin-bottom: 15px;">

  <table>

    <tr>

      <th>Ítem</th>
      <th>Nota</th>
    </tr>

    @foreach ($notes as $note)

    <tr>

      <td>
        <div style="margin-top: 0px;">
          <div style="margin-bottom: 5px;"> <b>{{ $note->spot }}</b><br></div>
          {{ $note->option }} <br>
          <div style="margin-top: 5px;"><small class="color-value">{{ $note->created_at }}</small></div>
        </div>
      </td>
      <td>
        @if ($note->type == 1)
        <div style="margin-bottom: 30px;">
          {{ $note->note }}
        </div>

        @else

        <div style="margin-top: 30px;">
          <a href="{{ $note->note }}" target="_blank">
            <img src="{{ $note->note }}" width="100px;" height="100px" max-width="100px;" max-height="100px;">
          </a>
        </div>


        @endif
      </td>
    </tr>

    @endforeach

  </table>

</body>

</html>