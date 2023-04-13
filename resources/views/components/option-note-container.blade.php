<div>
    <div class="form-row">
        <div class="form-group col-md-12">
            <div class="collapse"
                style="background:lightgray; margin-left:20px; border: 1px solid #ccc!important; padding:10px;"
                id="collapse-{{$option}}">
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-9 col-3">
                        <ul id="ul-note-{{$option}}" class="list-group">
                            @foreach ($notes as $item)
                                @if ($item['type'] == 1)
                                    <a href="#" class="list-group-item list-group-item-action list-group-item-success">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">{{$item['created_by']['fullname']}}</h5>
                                            <small>{{$item['created_at']}}</small>
                                        </div>
                                        <p class="">{{$item['note']}}</p>
                                    </a>
                                @else
                                <a href="#" class="list-group-item list-group-item-action list-group-item-success">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{$item['created_by']['fullname']}}</h5>
                                        <small>{{$item['created_at']}}</small>
                                    </div>
                                    <img style="position: static;" src="{{$item['note']}}" alt="avatar" height="300" width="300">
                                </a>
                                @endif
                            @endforeach
                        </ul>
                        <div class="list-group">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9 col-3">
                        <fieldset class="form-group">
                            <label for="option-commment">Observación de la línea</label>
                            <input id="option-note-{{$option}}" type="text" class="form-control"
                                placeholder="Escribe un comentario..." value="" data-isnote="true">
                        </fieldset>
                    </div>
                    <div class="col-md-2 col-2">
                        <fieldset class="form-group">
                            <label for="basicInput"></label>
                            <button type="button" class="btnAddNoteOption btn btn-success"
                                data-idchecklistoption="{{$option}}">Agregar</button>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>