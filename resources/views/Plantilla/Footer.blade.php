<footer class="main-footer">
	  &copy; <script>document.write(new Date().getFullYear())</script> <a href="#">Prasca Center</a>. Todos los derechos reservados.
  </footer>

  <script src="{{ asset('app-assets/js/vendors.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/pages/chat-popup.js') }}"></script>
  <script src="{{ asset('app-assets/icons/feather-icons/feather.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/ckeditor/ckeditor.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/bootstrap-select/dist/js/bootstrap-select.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/select2/dist/js/select2.full.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/jquery-steps-master/build/jquery.steps.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/jquery-validation-1.17.0/dist/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/29.2.0/classic/ckeditor.js"></script>
  <script src="{{ asset('app-assets/vendor_plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/full-calendar/moment.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/full-calendar/fullcalendar.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_plugins/input-mask/jquery.inputmask.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
  {{-- <script src="{{ asset('app-assets/vendor_components/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js') }}"></script> --}}
  <script src="{{ asset('app-assets/vendor_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.js') }}"></script>
  <script src="{{ asset('app-assets/vendor_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

  <script src="{{asset('app-assets/js/pdfmake/pdfmake.min.js')}}" type="text/javascript"></script>
<script src="{{asset('app-assets/js/pdfmake/vfs_fonts.js')}}" type="text/javascript"></script>
<script src="https://www.amcharts.com/lib/4/core.js"></script>
    <script src="https://www.amcharts.com/lib/4/charts.js"></script>
    <script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

  <!-- InvestX App -->
  <script src="{{ asset('app-assets/js/demo.js') }}"></script>
  <script src="{{ asset('app-assets/js/template.js') }}"></script>
  @if (Route::currentRouteName() == 'inicio')
    <script src="{{ asset('app-assets/js/agenda.js') }}"></script>
@endif


