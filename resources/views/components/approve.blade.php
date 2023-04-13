<div class="form-group col-md-2">
    <div style="margin-top:19px;" class="col-md-auto" >
        <input {{ $isApprover ? '' : 'disabled' }} id="up-{{$option->idchecklistoption}}"  data-option="{{$option->idchecklistoption}}" type="radio" class="approve radio-custom radio-up radio-inline" name="approve-{{$option->idchecklistoption}}" value="1" {{ $option->approved == "1" ? 'checked' : '' }}>
        <label for="up-{{$option->idchecklistoption}}" class="radio-custom-label"></label>
        <input {{ $isApprover ? '' : 'disabled' }} id="down-{{$option->idchecklistoption}}" data-option="{{$option->idchecklistoption}}" type="radio" class="approve radio-custom radio-down radio-inline" name="approve-{{$option->idchecklistoption}}" value="0" {{ $option->approved == "0" ? 'checked' : '' }}>
        <label for="down-{{$option->idchecklistoption}}" class="radio-custom-label"></label>
    </div>
</div>