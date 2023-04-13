<div class="d-flex align-items-center" style="justify-content: flex-end">
    @if (empty($idevaluator))
    <div class="custom-control custom-switch custom-switch-success mr-2">
        <p class="d-inline mb-0 mr-1">Evaluar</p>
        <input type="checkbox" class="custom-control-input" id="switchEvaluator">
        <label class="custom-control-label" for="switchEvaluator">
            <span class="switch-icon-left"><i class="feather icon-check"></i></span>
            <span class="switch-icon-right"><i class="feather icon-check"></i></span>
        </label>
    </div>
    @else
    <div class="avatar mr-50">
        <img src="{{$checklist->urlpicture}}" height="35" width="35">
    </div>
    <div class="user-page-info">
        <h6 class="mb-0">{{$checklist->evaluator}}</h6>
        <span class="font-small-2">Evaluador</span>
    </div>
    @endif
</div>

<form id="formChecklist" data-approve={{Auth::id() == $idevaluator ? 'true' : 'false'}}>
    @foreach ($options as $option)

    @switch($option->optiontype)

    @case(1)

    <!-- checkbox -->
    <div class="form-row">
        @if ($idstatus == 4)
        <x-approve :option="$option" :idevaluator="$idevaluator" />
        @endif

        <div class="form-group col-md-{{ $idstatus == 4 ? '10' : '12' }}">
            <x-collapse :option="$option->idchecklistoption" :notes="$option->notes" />

            <div class="vs-checkbox-con vs-checkbox-primary">
                <input name="{{ $option->idchecklistoption }}" type="checkbox" {{ $option->value == 1 ? 'checked' : '' }}>
                <span class="vs-checkbox">
                    <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                    </span>
                </span>
                <span class="">{{ $option->name }}</span>
            </div>
        </div>
    </div>

    <x-option-note-container :option="$option->idchecklistoption" :notes="$option->notes" />

    @break

    @case(2)

    <!-- radio -->
    <div class="form-row">

        @if ($idstatus == 4)
        <x-approve :option="$option" :idevaluator="$idevaluator" />
        @endif

        <div class="form-group col-md-{{ $idstatus == 4 ? '10' : '12' }}">
            <x-collapse :option="$option->idchecklistoption" :notes="$option->notes" />
            <span for="{{ $option->idchecklistoption }}">{{ $option->name }}</span>
            <ul class="list-unstyled mb-0">
                @foreach ($option->data as $item)
                <li class="d-inline-block mr-2">
                    <fieldset>
                        <div class="vs-radio-con">
                            <input type="radio" name="{{ $option->idchecklistoption }}" value="{{$item->value}}" {{ $option->value == $item->value ? 'checked' : '' }}>
                            <span class="vs-radio">
                                <span class="vs-radio--border"></span>
                                <span class="vs-radio--circle"></span>
                            </span>
                            <span class="">{{ $item->text }}</span>
                        </div>
                    </fieldset>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <x-option-note-container :option="$option->idchecklistoption" :notes="$option->notes" />

    @break

    @case(3)

    <!-- input text -->
    <div class="form-row">
        @if ($idstatus == 4)
        <x-approve :option="$option" :idevaluator="$idevaluator" />
        @endif
        <div class="form-group col-md-{{ $idstatus == 4 ? '10' : '12' }}">
            <x-collapse :option="$option->idchecklistoption" :notes="$option->notes" />
            <span for="{{ $option->idchecklistoption }}">{{ $option->name }}</span>
            <input type="text" name="{{ $option->idchecklistoption }}" class="form-control" placeholder="Complete este campo..." value="{{ $option->value }}">
        </div>
    </div>

    <x-option-note-container :option="$option->idchecklistoption" :notes="$option->notes" />

    @break

    @case(4)

    <!-- input number -->
    <div class="form-row">
        @if ($idstatus == 4)
        <x-approve :option="$option" :idevaluator="$idevaluator" />
        @endif
        <div class="form-group col-md-{{ $idstatus == 4 ? '10' : '12' }}">
            <x-collapse :option="$option->idchecklistoption" :notes="$option->notes" />
            <span for="{{ $option->idchecklistoption }}">{{ $option->name }}</span>
            <input type="number" name="{{ $option->idchecklistoption }}" class="form-control" placeholder="Complete este campo..." value="{{ $option->value }}">
        </div>
    </div>

    <x-option-note-container :option="$option->idchecklistoption" :notes="$option->notes" />

    @break

    @case(5)

    <!-- dropdown -->
    <div class="form-row">
        @if ($idstatus == 4)
        <x-approve :option="$option" :idevaluator="$idevaluator" />
        @endif
        <div class="form-group col-md-{{ $idstatus == 4 ? '10' : '12' }}">
            <x-collapse :option="$option->idchecklistoption" :notes="$option->notes" />
            <span for="{{ $option->idchecklistoption }}">{{ $option->name }}</span>
            <select class="custom-select" name="{{ $option->idchecklistoption }}">
                <option value="null" selected>--Seleccione--</option>
                @foreach ($option->data as $item)
                <option value="{{ $item->value }}" {{ $option->value == $item->value ? 'selected=selected' : '' }}>
                    {{ $item->text }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <x-option-note-container :option="$option->idchecklistoption" :notes="$option->notes" />

    @break

    @case(6)
    <!-- header -->
    <legend class="header-checklist">{{ $option->name }}</legend>
    @break

    @case(8)
    <!-- Fixed text -->
    <p>{{ $option->name }}</p>
    @break

    @case(9)

    <!-- date -->
    <div class="form-row">
        @if ($idstatus == 4)
        <x-approve :option="$option" :idevaluator="$idevaluator" />
        @endif
        <div class="form-group col-md-{{ $idstatus == 4 ? '10' : '12' }}">
            <x-collapse :option="$option->idchecklistoption" :notes="$option->notes" />
            <span for="{{ $option->idchecklistoption }}">{{ $option->name }}</span>
            <input type="date" name="{{ $option->idchecklistoption }}" class="form-control" placeholder="Complete este campo..." value="{{ $option->value }}">
        </div>
    </div>

    <x-option-note-container :option="$option->idchecklistoption" :notes="$option->notes" />
    @break

    @case(10)

    <!-- time -->
    <div class="form-row">
        @if ($idstatus == 4)
        <x-approve :option="$option" :idevaluator="$idevaluator" />
        @endif
        <div class="form-group col-md-{{ $idstatus == 4 ? '10' : '12' }}">
            <x-collapse :option="$option->idchecklistoption" :notes="$option->notes" />
            <span for="{{ $option->idchecklistoption }}">{{ $option->name }}</span>
            <input type="time" name="{{ $option->idchecklistoption }}" class="form-control" placeholder="Complete este campo..." value="{{ $option->value }}">
        </div>
    </div>

    <x-option-note-container :option="$option->idchecklistoption" :notes="$option->notes" />
    @break

    @case(11)

    <!-- Image -->
    @if ($option->value != "")
    <div class="form-row">
        <img class="rounded mx-auto d-block img-thumbnail" src="{{ $option->value }}" alt="avatar" height="300" width="300" />
    </div>
    @endif

    @break

    @case(12)

    <!-- Signature -->
    @if ($option->value != "")
    <div class="form-row">
        <img class="rounded mx-auto d-block img-thumbnail" src="{{ $option->value }}" alt="avatar" height="300" width="300" />
    </div>
    @endif

    @break

    @case(13)

    <!-- Photo -->
    @if ($option->value != "")
    <div class="form-row">
        <span for="{{ $option->idchecklistoption }}">{{ $option->name }}</span>
        <img class="rounded mx-auto d-block img-thumbnail" src="{{ $option->value }}" alt="avatar" height="300" width="300" />
    </div>
    @endif

    @break

    @case(14)

    <!-- time control -->
    <div class="form-row">
        @if ($idstatus == 4)
        <x-approve :option="$option" :idevaluator="$idevaluator" />
        @endif
        <div class="form-group col-md-{{ $idstatus == 4 ? '10' : '12' }}">
            <x-collapse :option="$option->idchecklistoption" :notes="$option->notes" />
            <span for="{{ $option->idchecklistoption }}">{{ $option->name }}</span>
            <div class="row ml-1 mt-1">
                @switch($option->value)
                @case(1)
                <div class="chip mr-1">
                    <div class="chip-body">
                        <div class="avatar bg-danger">
                            <span><i class="fa fa-exclamation"></i></span>
                        </div>
                        <span class="chip-text">Pendiente</span>
                    </div>
                </div>
                @break

                @case(2)
                <div class="chip mr-1">
                    <div class="chip-body">
                        <div class="avatar bg-success">
                            <span><i class="fa fa-play"></i></span>
                        </div>
                        <span class="chip-text">En progreso</span>
                    </div>
                </div>
                @break

                @case(3)
                <div class="chip mr-1">
                    <div class="chip-body">
                        <div class="avatar bg-warning">
                            <span><i class="fa fa-pause"></i></span>
                        </div>
                        <span class="chip-text">Pausado</span>
                    </div>
                </div>
                @break

                @case(4)
                <div class="chip mr-1">
                    <div class="chip-body">
                        <div class="avatar">
                            <span><i class="fa fa-pause"></i></span>
                        </div>
                        <span class="chip-text">Finalizado</span>
                    </div>
                </div>
                <div class="chip chip-primary mr-1">
                    <div class="chip-body">
                        <span class="chip-text" style="font-size: 1rem;">{{ gmdate("H:i:s", json_decode($option->properties)->duration)}}</span>
                    </div>
                </div>
                @break
                @endswitch
            </div>
        </div>
    </div>

    <x-option-note-container :option="$option->idchecklistoption" :notes="$option->notes" />

    @break

    @case(16)

    <table class="table table-striped mt-1">

        <tbody>
            @foreach ($option->children as $child)

            @if ($loop->first)
            <thead>
                <tr>
                    @foreach ($child as $cell)
                        <th scope="col">{{ $cell->name }}</th>
                    @endforeach
                </tr>
            </thead>
            @endif

                <tr>
                    @foreach ($child as $cell)

                        @if ($cell->optiontype != 13)
                            <td>{{ $cell->value }}</td>
                        @else
                            <td>
                                @if (!is_null($cell->value) || $cell->value != "")
                                    <img class="rounded mx-auto d-block img-thumbnail" src="{{ $cell->value }}" alt="avatar" max- height="300" width="300" style="max-width: 300px; max-height: 300px;" />
                                @endif
                            </td>
                        @endif
                            
                    @endforeach
                </tr>

            @endforeach
        </tbody>
    </table>


    @break

    @default
    Option type no contemplado...

    @endswitch

    @endforeach
</form>