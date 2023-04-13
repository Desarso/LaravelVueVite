<div>
    <div class="form-row">
        <div class="form-group col-md-12">
            <div class="collapse"
                style="background:lightgray; margin-left:20px; border: 1px solid #ccc!important; padding:10px;" id="collapse-{{$option}}">
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-9 col-3">
                        <ul id="ul-note-{{$option}}" class="list-group">
                            @foreach ($notes as $item)
                                @if ($item->type == 1)
                                    <li class="list-group-item list-group-item-success">{{$item->note}}</li>
                                @else
                                    <li class="list-group-item list-group-item-success">
                                        <img style="position: static;" src="{{$item->note}}" alt="avatar" height="300" width="300">
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9 col-3">
                        <fieldset class="form-group">
                            <label for="option-commment">Observación de la línea</label>
                            <input id="option-note-{{$option}}" type="text" class="form-control" placeholder="Escribe un comentario..." value="" data-isnote="true">
                        </fieldset>
                    </div>
                    <div class="col-md-2 col-2">
                        <fieldset class="form-group">
                            <label for="basicInput"></label>
                            <button type="button" class="btnAddNoteOption btn btn-success" data-idchecklistoption="{{$option}}">Agregar</button>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>