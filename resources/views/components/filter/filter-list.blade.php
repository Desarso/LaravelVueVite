<div class="btn-group mb-2 ml-1">
    <div class="dropdown">
        <button id="filterList" class="btn btn-light dropdown-toggle mr-1 waves-effect waves-light" type="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ __('locale.Filters') }}
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton6" x-placement="bottom-start"
            style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 38px, 0px);">
            <h6 class="dropdown-header">{{ __('locale.Saved Filters') }}</h6>
            <table id="table-filter">
                @foreach ($filters as $filter)
                    <tr id="row-filter-{{ $filter->id }}" class="dropdown-item">
                        <td><i data-idfilter="{{ $filter->id }}" data-name="{{ $filter->name }}"
                                class="btn-delete-filter fad fa-trash-alt"></i></td>
                        <td data-filter="{{ $filter->data }}" data-name="{{ $filter->name }}" class="item-filter">
                            {{ $filter->name }}</td>
                            <!--
                            <td><i data-idfilter="{{ $filter->id }}" data-filter="{{ $filter->data }}" data-name="{{ $filter->name }}"  data-toggle="modal"
                                data-target="#modalFilterEdit"
                                class="btn-open-modal-filter item-edit-filter fas fa-pen"></i></td>
-->
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
