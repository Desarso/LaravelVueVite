{{-- Vendor Scripts --}}
<script src="{{ asset(('vendors/js/vendors.min.js')) }}"></script>
<script src="{{ asset(('vendors/js/ui/prism.min.js')) }}"></script>
@yield('vendor-script')

<!-- Added ny Matthias...trying to make the jquery ujs work ... which was adde via require in app.js after 
     installing like npm install --save jquery-ujs
   -->

{{-- Theme Scripts --}}
<script src="{{ asset(('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(('js/core/app.js')) }}"></script>
<script src="{{ asset(('js/app.js')) }}"></script>
<script src="{{ asset(('js/moment.js')) }}"></script>
<script src="{{ asset(('js/print.min.js')) }}"></script>

<script src="{{ asset(('js/scripts/components.js')) }}"></script>
@if($configData['blankPage'] == false)
<script src="{{ asset(('js/scripts/customizer.js')) }}"></script>
<script src="{{ asset(('js/scripts/footer.js')) }}"></script>
@endif

{{-- Kendo Script --}}
<script src="{{ asset(('kendo/kendo.all.min.js')) }}"></script>
<script src="{{ asset(('kendo/jszip.min.js')) }}"></script>
<!-- <script src="{{ asset(('kendo/kendo.culture.es-ES.min.js')) }}"></script> -->
{{-- Kendo locale --}}
<script src="{{ asset(('kendo/kendo.messages.'. App\Helpers\Helper::kendoLocale() .'.min.js')) }}"></script>

{{-- Global Translations --}}
<script>
   window.translations = [];
   window.translations.auth = {!!  json_encode(trans('auth')) !!};
   window.translations.locale = {!!  json_encode(trans('locale')) !!};
   window.translations.pagination = {!!  json_encode(trans('pagination')) !!};
   window.translations.passwords = {!!  json_encode(trans('passwords')) !!};
   window.translations.validation = {!!  json_encode(trans('validation')) !!};
</script>

{{-- Whagons Script --}}
<script src="{{ asset(('js/scripts/whagons/utils.js')) }}"></script>
<script src="{{ asset(('js/scripts/whagons/treeview.js')) }}"></script>

<!-- input-file-->
<script src="{{ asset('input-file/sortable.min.js') }}"></script>
<script src="{{ asset('input-file/purify.min.js') }}"></script>
<script src="{{ asset('input-file/fileinput.min.js') }}"></script>
<script src="{{ asset('input-file/locales/es.js') }}"></script>
<script src="{{ asset('input-file/fa/theme.js') }}"></script>
<script src="{{ asset('js/FileInputModule.js') }}"></script>
<!-- input-file-->

<!-- web notifications -->
<script src="https://www.gstatic.com/firebasejs/8.0.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.1/firebase-messaging.js"></script>
<script src="{{ asset('web-push-notifications.js') }}"></script>
<!-- sweetalert2 -->
<script src="{{ asset(('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>

<!-- toastr -->
<script src="{{ asset(('vendors/js/extensions/toastr.min.js')) }}"></script>

<!-- user-profile-functions -->
<script src="{{ asset(('js/user.js')) }}"></script>

<!-- notifications -->
<script src="{{ asset(('js/notification.js')) }}"></script>

<!-- global-functions -->
<script src="{{ asset(('js/global-functions.js')) }}"></script>

<!-- jqBootstrapValidation -->
<script src="{{ asset(('vendors/js/forms/validation/jqBootstrapValidation.js')) }}"></script>

{{-- page script --}}
@yield('page-script')
{{-- page script --}}

<script>

PNotify.defaults.icons = 'fontawesome5'; // Font Awesome 4
PNotify.defaults.styling = 'bootstrap4';

window.lenguague = '{!!  App::getLocale() !!}'; 
moment.locale(window.lenguague);

window.path = '{!!Request::path()!!}';

//show button bitacora 
if($.inArray(window.path, ["/", "dashboard-tasks"]) != -1)
{
  // $("#btnLog").show();
   document.getElementById('btnLog').style.display = "block";
   console.log("mostrar boton")
}else{
   // $("#btnLog").hidden();
    document.getElementById('btnLog').style.display = "none";
    console.log("ocultar boton")
}


{{-- window.dataSearch = {!!  json_encode($dataSearch) !!}; --}}

</script>

<script id="template-search-panel" type="text/x-kendo-template">
    <span class="k-textbox k-grid-search k-display-flex" style="float: left;">
        <input autocomplete="off" placeholder="{{ __('locale.Search') }}..." title="{{ __('locale.Search') }}..." class="k-input">
        <span class="k-input-icon">
            <span class="k-icon k-i-search"></span>
        </span>
    </span>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button id="btn-new-config" class="btn btn-primary" type="button"></button>
    </div>
</script>