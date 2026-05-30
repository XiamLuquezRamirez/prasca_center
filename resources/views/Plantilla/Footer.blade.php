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

<!-- Script para notificaciones de cumpleaños -->
<script>
$(document).ready(function() {
    // Cargar datos de cumpleaños al cargar la página
    cargarCumpleanos();
    
    // Actualizar cada 5 minutos
    setInterval(function() {
        cargarCumpleanos();
    }, 300000);
    
    function cargarCumpleanos() {
        $.ajax({
            url: '{{ route("cumpleanos.datos") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    actualizarNotificacionCumpleanos(response.pacientesHoy, response.totalHoy);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar cumpleaños:', error);
            }
        });
    }
    
    function actualizarNotificacionCumpleanos(pacientes, total) {
        const badge = $('#cumpleanos-badge');
        const list = $('#cumpleanos-list');
        
        // Actualizar badge
        if (total > 0) {
            badge.text(total).show();
        } else {
            badge.hide();
        }
        
        // Actualizar lista
        if (pacientes.length > 0) {
            let html = '';
            pacientes.forEach(function(paciente) {
                const nombreCompleto = paciente.primer_nombre + ' ' + 
                                     (paciente.segundo_nombre ? paciente.segundo_nombre + ' ' : '') +
                                     paciente.primer_apellido + ' ' + 
                                     (paciente.segundo_apellido ? paciente.segundo_apellido : '');
                
                html += `
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            ${paciente.foto ? 
                                `<img src="{{ asset('app-assets/images/FotosPacientes/') }}/${paciente.foto}" 
                                      class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="Foto">` :
                                `<div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" 
                                      style="width: 40px; height: 40px;">
                                     <i class="fas fa-user text-white"></i>
                                 </div>`
                            }
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">${nombreCompleto}</h6>
                            <small class="text-muted">${paciente.edad} años</small>
                        </div>
                        <div class="ms-2">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-birthday-cake"></i> ¡Hoy!
                            </span>
                        </div>
                    </div>
                `;
            });
            list.html(html);
        } else {
            list.html(`
                <div class="text-center text-muted">
                    <i class="fas fa-birthday-cake fa-2x mb-2"></i>
                    <p>No hay cumpleaños hoy</p>
                    <small>¡Pero siempre es un buen día para celebrar!</small>
                </div>
            `);
        }
    }
});
</script>


