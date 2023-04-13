@forelse ($next_status as $status)
    <button type="button" data-idstatus="{{ $status->id }}" class="btn-status btn btn-danger" style="background-color:{{ $status->color }} !important; border-color:{{ $status->color }};">{{ $status->name }}</button>
@empty
    <i class="fas fa-exclamation-triangle"></i>
    <p>No hay estado siguiente</p>
@endforelse