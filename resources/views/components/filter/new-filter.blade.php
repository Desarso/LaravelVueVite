<div id="btnNewFilter" class="btn-group dropup ml-2 mb-2" style="display: none;">
    <button type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        {{__('locale.Save') }}
    </button>
    <div class="dropdown-menu" x-placement="top-start"
        style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -7px, 0px);">
        <p class="dropdown-header">{{__('locale.Save filter for future use') }}</p>
        <div class="dropdown-divider"></div>
        <form class="px-2 py-2">
            <div class="form-group">
                <label for="filterName">{{__('locale.Name') }}</label>
                <input type="text" class="form-control" id="filterName" placeholder="{{__('locale.Filter Name') }}">
            </div>
            <button id="btnSaveFilter" class="btn btn-success waves-effect waves-light">{{__('locale.Save') }}</button>
        </form>
    </div>
</div>