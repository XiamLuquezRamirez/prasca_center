let calendarCita;
let mainCalendar;
var disponibilidadJSON = [];
let fechaHoraSelCita
let fechaHoraInicio
let fechaHoraFinal
(function (l) {
    "use strict";

    // Constructor de CalendarApp
    function CalendarApp(calendarElement) {
        this.$calendar = $(calendarElement);
        this.eventos = []; // Asigna tus eventos aquí
    }

    // Inicializa el calendario de citas
    CalendarApp.prototype.init = function () {
        const calendarApp = this;
        var fechaActual = new Date().toISOString().split("T")[0];


        calendarApp.$calendarObj = new FullCalendar.Calendar(calendarApp.$calendar[0], {
            locale: 'es',
            slotDuration: "00:20:00",
            slotLabelInterval: "00:20",
            slotMinTime: "08:00:00",
            slotMaxTime: "19:00:00",
            themeSystem: "bootstrap",
            buttonText: { today: "Hoy", month: "Mes", week: "Semana", day: "Día", prev: "Anterior", next: "Siguiente" },
            defaultDate: fechaActual,
            initialView: "timeGridWeek",
            hiddenDays: [0],
            handleWindowResize: true,
            height: $(window).height() - 200,
            headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek,timeGridDay" },
            initialEvents: calendarApp.eventos,

            selectable: true,
            allDaySlot: false,
            slotLabelFormat: {
                hour: "2-digit",
                minute: "2-digit",
                omitZeroMinute: false,
                meridiem: true,
            },
            dateClick: function (info) {
                calendarApp.onSelect(info); // Llama a la función para manejar la selección en el calendario de citas
            },
            eventClick: function (info) {
                calendarApp.onEventClick(info); // Llama a onEventClick para manejar clic en eventos
            },
        });

        calendarApp.$calendarObj.render();
        calendarApp.addCustomButton(); // Si tienes esta función definida
    };

    // Función para manejar clic en el calendario de citas
    CalendarApp.prototype.onSelect = function (event) {
        if (event) {
            var duracionCita = parseInt(document.getElementById('duracionCita').value);
            var nuevaCitaStart = new Date(event.date);
            var nuevaCitaEnd = new Date(nuevaCitaStart.getTime() + duracionCita * 60000); // Duración en milisegundos

            // Verifica si la nueva cita se superpone con alguna cita existente
            var seSuperpone = disponibilidadJSON.some(function (cita) {
                var citaStart = new Date(cita.start);
                var citaEnd = new Date(cita.end);
                console.log(`${nuevaCitaStart} < ${citaEnd} && ${nuevaCitaEnd} > ${citaStart}`)
                return (nuevaCitaStart < citaEnd && nuevaCitaEnd > citaStart);
            });
            if (seSuperpone) {
                swal({
                    type: "warning",
                    title: "Oops...",
                    text: "La nueva cita se superpone con alguna cita existente, verifica la duración de la nueva",
                    confirmButtonClass: "btn btn-primary",
                    buttonsStyling: false
                });
                return;
            }
            if ($("#profesional").val() == "") {
                swal({
                    type: "warning",
                    title: "Oops...",
                    text: "Debes de seleccionar el profesional",
                    confirmButtonClass: "btn btn-primary",
                    buttonsStyling: false
                });
                return;

            }
            if ($("#especialidad").val() == "") {
                swal({
                    type: "warning",
                    title: "Oops...",
                    text: "Debes de seleccionar la especialidad de la consulta",
                    confirmButtonClass: "btn btn-primary",
                    buttonsStyling: false
                });
                return;
            }


            var select2Element = $('#especialidad');
            let motivo = select2Element.find('option:selected').text();

            var nuevaCita = {
                title: motivo,
                start: nuevaCitaStart,
                end: nuevaCitaEnd,
                backgroundColor: "#835ad9", // Color verde de fondo (puedes personalizarlo)
                borderColor: "#835ad9",    // Color del borde (opcional)
                textColor: "#ffffff"
            };

            const fechaHora = new Date(nuevaCita.start);

            // Obtiene el día, mes y año
            const dia = fechaHora.getDate().toString().padStart(2,
                '0'); // Asegura que el día tenga dos dígitos
            const mes = (fechaHora.getMonth() + 1).toString().padStart(2,
                '0'); // El mes se indexa desde 0
            const año = fechaHora.getFullYear();

            // Obtiene la hora y los minutos
            const hora = fechaHora.getHours().toString().padStart(2,
                '0'); // Asegura que la hora tenga dos dígitos
            const minutos = fechaHora.getMinutes().toString().padStart(2,
                '0'); // Asegura que los minutos tengan dos dígitos
            const segundos = fechaHora.getSeconds().toString().padStart(2, '0');
            // Combina los componentes para formar la fecha y hora en el formato deseado
            fechaHoraSelCita = `${dia}/${mes}/${año} ${hora}:${minutos}`;
            fechaHoraInicio = `${año}-${mes}-${dia}T${hora}:${minutos}:${segundos}`;


            const fechaHoraFin = new Date(nuevaCita.end);

            // Obtiene el día, mes y año
            const dia1 = fechaHoraFin.getDate().toString().padStart(2,
                '0'); // Asegura que el día tenga dos dígitos
            const mes1 = (fechaHoraFin.getMonth() + 1).toString().padStart(2,
                '0'); // El mes se indexa desde 0
            const año1 = fechaHoraFin.getFullYear();

            // Obtiene la hora y los minutos
            const hora1 = fechaHoraFin.getHours().toString().padStart(2,
                '0'); // Asegura que la hora tenga dos dígitos
            const minutos1 = fechaHoraFin.getMinutes().toString().padStart(2,
                '0'); // Asegura que los minutos tengan dos dígitos
            const segundos1 = fechaHoraFin.getSeconds().toString().padStart(2, '0');
            // Combina los componentes para formar la fecha y hora en el formato deseado

            fechaHoraFinal = `${año1}-${mes1}-${dia1}T${hora1}:${minutos1}:${segundos1}`;

            document.getElementById('fechaHoraSelCita').value = fechaHoraSelCita;
            document.getElementById('fechaHoraInicio').value = fechaHoraInicio;
            document.getElementById('fechaHoraFinal').value = fechaHoraFinal;

            actualizarCalendarioCita(disponibilidadJSON);

            const calendarObj = calendarCita.$calendarObj
            calendarObj.addEvent(nuevaCita)
        }
    };

    // Función para manejar clic en eventos
    CalendarApp.prototype.onEventClick = function (info) {
        // Implementa la lógica para manejar el clic en los eventos
        console.log("Evento clicado:", info.event.title);
    };

    // Función para agregar un botón personalizado (defínela según tus necesidades)
    CalendarApp.prototype.addCustomButton = function () {
        // Implementa la lógica para agregar botones personalizados aquí
    };

    // Constructor específico para el calendario principal
    function MainCalendar(calendarElement) {
        CalendarApp.call(this, calendarElement); // Llama al constructor de CalendarApp
    }

    // Herencia de CalendarApp
    MainCalendar.prototype = Object.create(CalendarApp.prototype);
    MainCalendar.prototype.constructor = MainCalendar;

    // Inicializa el calendario principal
    MainCalendar.prototype.init = function () {
        const calendarApp = this;

        calendarApp.$calendarObj = new FullCalendar.Calendar(calendarApp.$calendar[0], {
            locale: 'es',
            slotDuration: "00:20:00",
            slotLabelInterval: "00:20",
            slotMinTime: "08:00:00",
            slotMaxTime: "19:00:00",
            themeSystem: "bootstrap",
            buttonText: {
                today: "Hoy",
                month: "Mes",
                week: "Semana",
                day: "Día",
                prev: "Anterior",
                next: "Siguiente"
            },
            initialView: "timeGridWeek",
            hiddenDays: [0],
            handleWindowResize: true,
            height: $(window).height() - 200,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay"
            },
            initialEvents: calendarApp.eventos,
            selectable: true,
            allDaySlot: false,
            height: 'auto',
            slotLabelFormat: {
                hour: "2-digit",
                minute: "2-digit",
                omitZeroMinute: false,
                meridiem: true,
            },
            eventDidMount: function (info) {
                // Aplicar estilo flexbox para centrar el contenido horizontal y verticalmente
                info.el.style.display = 'flex';
                info.el.style.flexDirection = 'column';
                info.el.style.alignItems = 'center';
                info.el.style.justifyContent = 'center';
                info.el.style.textAlign = 'center';
                info.el.style.overflow = 'hidden';
                info.el.style.padding = '5px';

                const { prof, estado, bloq } = info.event.extendedProps;

                // Crear y añadir elementos personalizados
                if (bloq === "CITAS") {
                    if (prof) {
                        const profElement = document.createElement('div');
                        profElement.className = 'fc-event-prof';
                        profElement.textContent = `Prof: ${prof}`;
                        info.el.appendChild(profElement);
                    }
                    if (estado) {
                        const estadoElement = document.createElement('div');
                        estadoElement.className = 'fc-event-estado';
                        estadoElement.textContent = `Estado: ${estado}`;
                        estadoElement.style.fontSize = '10px';
                        info.el.appendChild(estadoElement);

                        // Cambiar el color de fondo según el estado
                        switch (estado) {
                            case 'Por atender':
                                info.el.style.backgroundColor = '#00B5B8';
                                break;
                            case 'Atendida':
                                info.el.style.backgroundColor = '#2196F3';
                                break;
                            case 'Confirmada':
                                info.el.style.backgroundColor = '#10C888';
                                break;
                            default:
                                info.el.style.backgroundColor = '#2DCEE3';
                        }
                    }
                    info.el.style.color = '#fff';
                } else {
                    info.el.style.color = '#fff';
                    info.el.style.backgroundColor = '#547A8B';
                }
            },
            dateClick: function (info) {
                calendarApp.onDateClick(info);
            },
            eventClick: function (info) {
                const { idCita, estado, bloq } = info.event.extendedProps;

                if (bloq === "CITAS") {
                    verCita(idCita);
                } else {
                    verBloq(idCita);
                }
            },
        });

        calendarApp.$calendarObj.render();
        calendarApp.addCustomButton();
    };



    MainCalendar.prototype.addCustomButton = function () {
        const fcLeftDiv = document.querySelector('.fc-toolbar-chunk');
        if (!fcLeftDiv) {
            console.error('No se encontró el elemento .fc-toolbar-chunk');
            return;
        }

        if (document.getElementById('agregarCita')) {
            console.log('El botón ya fue agregado.');
            return;
        }

        const iconElement = document.createElement('i');
        iconElement.className = 'fa fa-plus';

        const miBotonCita = document.createElement('button');
        miBotonCita.textContent = ' Agregar cita';
        miBotonCita.id = 'agregarCita';
        miBotonCita.classList.add('fc-today-button', 'btn', 'btn-info');
        miBotonCita.insertBefore(iconElement, miBotonCita.firstChild);

        fcLeftDiv.appendChild(miBotonCita);

        miBotonCita.addEventListener('click', function () {
            var modal = new bootstrap.Modal(document.getElementById("event-modal-add-cita"), {
                backdrop: 'static',
                keyboard: false
            });

            // Mostrar el modal
            modal.show();
            document.getElementById("idCita").value = ""

            // Remover eventos existentes del calendario
            calendarCita.$calendarObj.removeAllEvents(); // Elimina todos los eventos

            // Cambiar la vista del calendario a la semana
            calendarCita.$calendarObj.changeView('timeGridWeek', new Date());



            // Limpiar informacion de cita
            $('#profesional').val("").trigger('change.select2')
            $('#especialidad').val("").trigger('change.select2')
            $('#duracionCita').val("20").trigger('change.select2');
            document.getElementById("fechaHoraSelCita").value = ""
            document.getElementById('notifCliente').checked = false

            // Renderiza el calendario después de mostrar el modal
            modal._element.addEventListener('shown.bs.modal', function () {
                calendarCita.$calendarObj.render(); // Renderiza el calendario
            });
        });
    };

    // Nueva función para manejar clic en el calendario principal
    MainCalendar.prototype.onDateClick = function (info) {
        console.log("Fecha seleccionada en el calendario principal:", info.dateStr);
        // Aquí puedes implementar la lógica específica para el calendario principal
    };

    // Uso de las instancias
    calendarCita = new CalendarApp('#calendarCita');
    calendarCita.init();

    mainCalendar = new MainCalendar('#calendar');
    mainCalendar.init();

})(jQuery); // Asegúrate de que jQuery está disponible

document.addEventListener('DOMContentLoaded', () => {
    $('[data-mask]').inputmask()
    cargarProfesionales()
    cargarEspecialidades()
    cargarCitas()
    cargarTipoUsuario()

   

    $.validator.addMethod("dateFormat", function(value, element) {
        // Verificar si la fecha está en el formato yyyy-mm-dd
        var dateParts = value.split("-")
        if (dateParts.length === 3) {
            var year = parseInt(dateParts[0], 10)
            var month = parseInt(dateParts[1], 10)
            var day = parseInt(dateParts[2], 10)

            // Comprobar si es una fecha válida
            var date = new Date(year, month - 1, day)
            return date && (date.getFullYear() === year) && (date.getMonth() === month - 1) && (date
                .getDate() === day)
        }
        return false
    }, "Por favor, ingresa una fecha válida en formato yyyy-mm-dd.")


    $.validator.addMethod("maxDate", function(value, element) {
        // Dividir la fecha en partes según el formato yyyy-mm-dd
        var dateParts = value.split("-")
        if (dateParts.length === 3) {
            var year = parseInt(dateParts[0], 10)
            var month = parseInt(dateParts[1], 10)
            var day = parseInt(dateParts[2], 10)

            // Convertir a objeto Date
            var inputDate = new Date(year, month - 1, day)
            var today = new Date() // Fecha actual sin hora

            // Asegurarse de que las horas, minutos y segundos no afecten la comparación
            today.setHours(0, 0, 0, 0)

            // Validar que la fecha de entrada no sea mayor a hoy
            return inputDate <= today
        }
        return false
    }, "La fecha no puede ser mayor a hoy.")

    const url = $('#urlBase').data("ruta")
    $("#formCita").validate({
        rules: {
            tipoId: {
                required: true
            },
            identificacion: {
                required: true,
                remote: {
                    url: `${url}verificar-identificacion`, // URL para verificar
                    type: "post",
                    data: {
                        identificacion: function () {
                            return $("#identificacion").val();
                        },
                        tipoId: function () {
                            return $("#tipoId").val(); // Tipo de identificación
                        },
                        id: function () {
                            return $("#idPaciente").val() || null; // Enviar id si es edición
                        },
                        _token: function () {
                            return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Token CSRF para seguridad
                        }
                    }
                },
                minlength: 5,
                maxlength: 20
            },

            primerNombre: {
                required: true
            },
            primerApellido: {
                required: true
            },
            tipoUsuario: {
                required: true
            },
            sexo: {
                required: true
            },
            zonaResidencial: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            telefono: {
                required: true,
                digits: true,
                minlength: 7
            },
            fechaNacimiento: {
                required: true,
                dateFormat: true,
                maxDate: true
            },
        },
        messages: {
            tipoId: {
                required: "Por favor, selecciona un tipo de identificación."
            },
            identificacion: {
                required: "Por favor, ingresa una identificación.",
                remote: "Esta identificación ya está registrada.",
                minlength: "La identificación debe tener al menos 5 caracteres.",
                maxlength: "La identificación no puede exceder los 20 caracteres."
            },
            primerNombre: {
                required: "Por favor, ingresa el primer nombre."
            },
            primerApellido: {
                required: "Por favor, ingresa el primer apellido."
            },
            tipoUsuario: {
                required: "Por favor, seleccione el tipo de usuario."
            },
            sexo: {
                required: "Por favor, seleccione el sexo."
            },
            zonaResidencial: {
                required: "Por favor, seleccione la zona de residencia ."
            },

            email: {
                required: "Por favor, ingresa un email.",
                email: "Por favor, ingresa un email válido."
            },
            telefono: {
                required: "Por favor, ingresa un número de teléfono.",
                digits: "Por favor, ingresa solo dígitos.",
                minlength: "El número de teléfono debe tener al menos 7 dígitos."
            },
            fechaNacimiento: {
                required: "Por favor, ingresa tu fecha de nacimiento.",
                dateFormat: "Por favor, ingresa una fecha válida en formato dd/mm/yyyy.",
                maxDate: "La fecha de nacimiento no puede ser mayor que hoy."
            },
        }
    });

    fechaNacimiento.addEventListener("change", validarIdentificacionPorEdad);
    fechaNacimiento.addEventListener("input", validarIdentificacionPorEdad);
    fechaNacimiento.addEventListener("blur", validarIdentificacionPorEdad);
    document.getElementById("tipoId").addEventListener("change", validarIdentificacionPorEdad);


});

function verCita(idCita) {

    document.getElementById("idCita").value = idCita
    var modal = new bootstrap.Modal(document.getElementById("modalCitasDeta"), {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = $('#urlBase').data("ruta")

    fetch(`${url}citas/informacionCita`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Esta cabecera adicional
        },
        body: JSON.stringify({ idCita: idCita }) // Envía la fecha seleccionada
    })
        .then(response => response.json())
        .then(response => {
            //datos de citas
            $("#especialidadCita").html(response.detaCita.nespec);
            $("#profesionalCita").html(response.detaCita.nomprof);
            var nuevoFormatoIni = convertirFormato(response.detaCita
                .inicio);
            $("#inicioCita").html(nuevoFormatoIni);
            var nuevoFormatoFinal = convertirFormato(response.detaCita
                .final);
            $("#finalcita").html(nuevoFormatoFinal);
            $("#cometarioCita").html(response.detaCita.comentario);
            //datos de paciente
            $("#idPaciente").val(response.paciente.id);
            let pEdda = response.paciente.edad.split(" ");
            $("#edadPacienteCita").val(pEdda[0]);            
            $("#identificacionCita").html(response.paciente
                .tipo_identificacion + " " + response.paciente
                    .identificacion);
            $("#nombreCita").html(response.paciente.primer_nombre + " " + response
                .paciente.segundo_nombre + " " + response.paciente.primer_apellido + " " + response.paciente.segundo_apellido);
            $("#npacientedetCita").html(response.paciente.primer_nombre + " " + response
                .paciente.segundo_nombre + " " + response.paciente.primer_apellido + " " + response.paciente.segundo_apellido);
            let sexo =
                (response.paciente.sexo === "H") ? "Hombre" :
                    (response.paciente.sexo === "M") ? "Mujer" :
                        "Indeterminado o Intersexual";
            $("#sexoCita").html(sexo);
            var fechNaci = convertirFormatoNac(response.paciente
                .fecha_nacimiento);
            $("#nacimientoCita").html(`${fechNaci} (${response.paciente.edad})`);
            $("#emailCita").html(response.paciente.email);
            $("#telefonoCita").html(response.paciente.telefono);
            $("#direccionCita").html(response.paciente.direccion);

            document.getElementById("estadocita").value = response.detaCita.estado

            let usuario =
                (response.paciente.tipo_usuario === "01") ? "Contributivo cotizante" :
                    (response.paciente.tipo_usuario === "02") ? "Contributivo beneficiario" :
                        (response.paciente.tipo_usuario === "03") ? "Contributivo adicional" :
                            (response.paciente.tipo_usuario === "04") ? "Subsidiado" :
                                (response.paciente.tipo_usuario === "05") ? "No afiliado" :
                                    (response.paciente.tipo_usuario === "06") ? "Especial o Excepcion cotizante" :
                                        (response.paciente.tipo_usuario === "07") ? "Especial o Excepcion beneficiario" :
                                            (response.paciente.tipo_usuario === "08") ? "Personas privadas de la libertad a cargo del Fondo Nacional de Salud" :
                                                (response.paciente.tipo_usuario === "09") ? "Tomador / Amparado ARL" :
                                                    (response.paciente.tipo_usuario === "10") ? "Tomador / Amparado SOAT" :
                                                        "Sin Especificar";

            $("#tipoUsuarioCita").html(usuario);

            $("#acompananteNombreCita").html(response.paciente.acompanante);
            $("#acompananteParentescoCita").html(response.paciente.parentesco);
            $("#acompananteTelefonoCita").html(response.paciente.telefono_acompanate);

            var foto = response.paciente.foto;
            const previewImage = document.getElementById(
                'previewImageDetCita');
            let url = $('#Ruta').data("ruta");
            previewImage.src = url + "/images/FotosPacientes/" + foto;

            $("#edadDetaCita").html(response.paciente.edad);
        })
        .catch(error => console.error('Error al cargar los datos:', error));
}


function editCita() {
    var modal = new bootstrap.Modal(document.getElementById("event-modal-add-cita"), {
        backdrop: 'static',
        keyboard: false
    });

    modal.show()
    $('#modalCitasDeta').modal('toggle');

    let idCita = document.getElementById('idCita').value
    document.getElementById('accionCita').value = "editar"
    var btnGuardar = document.getElementById("btnGuardar");
    btnGuardar.textContent = " Actualizar citas";
    var iconElement = document.createElement('i');
    iconElement.className = 'fa fa-check';
    document.getElementById("opc").value = 3;

    btnGuardar.insertBefore(iconElement, btnGuardar.firstChild);

    btnGuardar.onclick = function () {
        guardarCita(3);
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = $('#urlBase').data("ruta")
    fetch(`${url}citas/informacionCita`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Esta cabecera adicional
        },
        body: JSON.stringify({ idCita: idCita }) // Envía la fecha seleccionada
    })
        .then(response => response.json())
        .then(response => {
            //datos de citas
            $("#especialidadCita").html(response.detaCita.nespec);
            $("#profesionalCita").html(response.detaCita.nomprof);
            var nuevoFormatoIni = convertirFormato(response.detaCita
                .inicio);
            $("#inicioCita").html(nuevoFormatoIni);
            var nuevoFormatoFinal = convertirFormato(response.detaCita
                .final);
            $("#finalcita").html(nuevoFormatoFinal);
            $("#cometarioCita").html(response.detaCita.comentario);


            $('#profesional').val(response.detaCita.profesional).trigger('change.select2')
            $('#especialidad').val(response.detaCita.motivo).trigger('change.select2')
            $('#duracionCita').val(response.detaCita.duracion)

            $('#fechaHoraInicio').val(response.detaCita.inicio)
            $('#fechaHoraFinal').val(response.detaCita.final)

            let covFech = convFechaISO(response.detaCita.inicio)

            $('#fechaHoraSelCita').val(covFech)



        })
        .catch(error => console.error('Error al cargar los datos:', error));


}
function convFechaISO(fechaISO) {

    // Convertir la fecha ISO a un objeto Date
    const fecha = new Date(fechaISO);

    // Extraer los componentes de la fecha
    const dia = fecha.getDate().toString().padStart(2, '0'); // Día con 2 dígitos
    const mes = (fecha.getMonth() + 1).toString().padStart(2, '0'); // Mes con 2 dígitos (indexado desde 0)
    const año = fecha.getFullYear(); // Año completo

    // Extraer los componentes de la hora
    const hora = fecha.getHours().toString().padStart(2, '0'); // Hora con 2 dígitos
    const minutos = fecha.getMinutes().toString().padStart(2, '0'); // Minutos con 2 dígitos

    // Formatear la fecha y hora
    return `${dia}/${mes}/${año} ${hora}:${minutos}`;
}

function convertirFormato(fechaHora) {
    // Crear un objeto Date a partir de la cadena de fecha y hora
    var fecha = new Date(fechaHora);

    // Obtener los componentes de la fecha y la hora
    var dia = fecha.getDate();
    var mes = fecha.getMonth() + 1; // Los meses comienzan desde 0, por lo que sumamos 1
    var anio = fecha.getFullYear();
    var horas = fecha.getHours();
    var minutos = fecha.getMinutes();
    var ampm = horas >= 12 ? 'PM' : 'AM';

    // Formatear los componentes en el nuevo formato
    horas = horas % 12;
    horas = horas ? horas : 12; // Si es 0, cambiar a 12
    minutos = minutos < 10 ? '0' + minutos : minutos;

    // Crear la cadena formateada
    var nuevoFormato = `${dia}/${mes}/${anio} ${horas}:${minutos} ${ampm}`;

    return nuevoFormato;
}

function convertirFormatoNac(fecha) {
    // Crear un objeto Date a partir de la cadena de fecha y hora
    var fecha = new Date(fecha);

    // Obtener los componentes de la fecha y la hora
    var dia = fecha.getDate();
    var mes = fecha.getMonth() + 1; // Los meses comienzan desde 0, por lo que sumamos 1
    var anio = fecha.getFullYear();



    // Crear la cadena formateada
    var nuevoFormato = `${dia}/${mes}/${anio}`;

    return nuevoFormato;
}

function validarIdentificacionPorEdad() {
    const tipoId = document.getElementById("tipoId").value
    const fechaNacimiento = document.getElementById("fechaNacimiento").value

    if (!fechaNacimiento) return // Si no hay fecha de nacimiento, salir

    const hoy = new Date()
    const nacimiento = new Date(fechaNacimiento.split('/').reverse().join('-')) // Convertir a formato ISO

    // Cálculo inicial de años, meses y días
    let anios = hoy.getFullYear() - nacimiento.getFullYear()
    let meses = hoy.getMonth() - nacimiento.getMonth()
    let dias = hoy.getDate() - nacimiento.getDate()

    // Ajustar si los meses o días son negativos
    if (meses < 0 || (meses === 0 && dias < 0)) {
        anios--
        meses += 12
    }
    if (dias < 0) {
        const ultimoDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth(), 0).getDate();
        dias += ultimoDiaMesAnterior
        meses--
    }

    // Crear la cadena de edad en el formato deseado
    const edad =
        `${anios} ${anios === 1 ? 'Año' : 'Años'}, ${meses} ${meses === 1 ? 'Mes' : 'Meses'} y ${dias} ${dias === 1 ? 'Día' : 'Días'}`;

    let tiposPermitidos = []

    // Determinar los tipos de documento permitidos según la edad
    if (anios <= 6) {
        tiposPermitidos = ["RC", "NV", "PT", "CD", "PE", "MS"]
    } else if (anios >= 7 && anios <= 17) {
        tiposPermitidos = ["TI", "CE", "PT", "CD", "PE", "MS"]
    } else if (anios >= 18) {
        tiposPermitidos = ["CC", "TI", "CE", "PT", "CD", "PE", "AS"]
    }

    // Verificar si el tipo de identificación es válido
    if (!tiposPermitidos.includes(tipoId)) {
        document.getElementById('fechaNacimiento').value = ""
        document.getElementById('edad').value = ""
        swal("¡Alerta!", `El tipo de identificación "${tipoId}" no corresponde con la edad de ${edad}.`, "warning");
    } else {
        document.getElementById('edad').value = edad
    }
}

// Funciones para cargar datos


function cargarTipoUsuario() {
    return new Promise((resolve, reject) => {
          const urltotal = $('#urlBase').data("ruta")
        let select = document.getElementById("tipoUsuario")
        select.innerHTML = ""
        const url = `${urltotal}pacientes/tipoUSuario`

        let defaultOption = document.createElement("option")
        defaultOption.value = "" // Valor en blanco
        defaultOption.text = "Selecciona una opción" // Texto que se mostrará
        defaultOption.selected = true // Que aparezca seleccionada por defecto
        select.appendChild(defaultOption)

        fetch(url)
            .then(response => response.json())
            .then(data => {
                data.forEach(tipo => {
                    let option = document.createElement("option")
                    option.value = tipo.id
                    option.text = tipo.descripcion
                    select.appendChild(option)
                })
                resolve() // Resuelve la promesa cuando los datos han sido cargados
            })
            .catch(error => {
                console.error('Error:', error)
                reject(error) // Rechaza la promesa si ocurre un error
            })
    })
}

function cargarProfesionales() {
    const url = $('#urlBase').data("ruta")
    const urlProfesionales = `${url}profesionales/cargarListaProf`;
    fetch(urlProfesionales)
        .then(response => response.json())
        .then(data => {
            const selectProfesional = document.getElementById('profesional');
            llenarSelect(selectProfesional, data);
        })
        .catch(error => console.error('Error al cargar profesionales:', error));
}

function cargarEspecialidades() {
    const url = $('#urlBase').data("ruta")
    const urlEspecialidades = `${url}especialidad/cargarListaEsp`;
    
    fetch(urlEspecialidades)
        .then(response => response.json())
        .then(data => {
            const selectEspecialidad = document.getElementById('especialidad');
            llenarSelect(selectEspecialidad, data);
        })
        .catch(error => console.error('Error al cargar especialidades:', error));
}

function llenarSelect(selectElement, data) {
    selectElement.innerHTML = '<option value="">Seleccione una opción</option>';
    data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.nombre;
        selectElement.appendChild(option);
    });
}

function cargarDisponibilidad(idProfesional) {
    let idCita = document.getElementById("idCita").value;
    const url = $('#urlBase').data("ruta")
    fetch(`${url}citas/disponibilidad`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            idProf: idProfesional,
            idCita: idCita
        }) // Envía la fecha seleccionada
    })
        .then(response => response.json())
        .then(data => {
            const eventos = data.disponibilidad.map(item => {
                if (item.tblo == "CITAS") {
                    return {
                        "start": item.inicio,
                        "end": item.final,
                        title: `${item.primer_nombre} ${item.primer_apellido}`,
                        "id": item.id,
                        "bloq": item.tblo
                    };
                }
            }).filter(Boolean); // Filtra los eventos nulos

            // Actualiza el calendario con los eventos de disponibilidad
            actualizarCalendarioCita(eventos);
        })
        .catch(error => console.error('Error al cargar disponibilidad:', error));
}

function actualizarCalendarioCita(eventos) {
    const calendarObj = calendarCita.$calendarObj;

    disponibilidadJSON = eventos;

    // Eliminar eventos existentes para evitar duplicados
    calendarObj.getEvents().forEach(event => event.remove());

    // Agregar los nuevos eventos al calendario
    eventos.forEach(evento => calendarObj.addEvent(evento));
}

function continuar() {
    if ($("#fechaHoraSelCita").val().trim() == "") {
        swal({
            type: "warning",
            title: "Oops...",
            text: "Debes de seleccionar la feha de la cita",
            confirmButtonClass: "btn btn-primary",
            timer: 2500,
            buttonsStyling: false
        });
        return;
    }

    cargarPacientes();
    limpiarPacientes();
    document.getElementById("calendaCita").style = "display: none";
    document.getElementById("calendaCitaPaci").style = "display: block";

    var btnGuardar = document.getElementById("btnGuardar");
    var btm_atras = document.getElementById("btnAtras");
    var cancelRegistro = document.getElementById("cancelRegistro");
    cancelRegistro.style.display = "none";
    btm_atras.style.display = "initial";
    btnGuardar.textContent = " Confirmar Cita";
    var iconElement = document.createElement('i');
    iconElement.className = 'fa fa-check';

    btnGuardar.insertBefore(iconElement, btnGuardar.firstChild);

    btnGuardar.onclick = function () {
        guardarCita(2);
    };
}

function limpiarPacientes() {
    document.getElementById("tipoId").value = ""
    document.getElementById("identificacion").value = ""
    document.getElementById("tipoUsuario").value = ""
    document.getElementById("fechaNacimiento").value = ""
    document.getElementById("edad").value = ""
    document.getElementById("primerNombre").value = ""
    document.getElementById("segundoNombre").value = ""
    document.getElementById("primerApellido").value = ""
    document.getElementById("segundoApellido").value = ""
    document.getElementById("sexo").value = ""
    document.getElementById("email").value = ""
    document.getElementById("telefono").value = ""
    document.getElementById("direccion").value = ""
    document.getElementById("zonaResidencial").value = ""
    document.getElementById("observaciones").value = ""
}

function cargarPacientes() {
    const url = $('#urlBase').data("ruta")
    const urlEspecialidades = `${url}pacientes/cargarListaPacientes`;
    fetch(urlEspecialidades)
        .then(response => response.json())
        .then(data => {
            const paciente = document.getElementById('paciente');
            paciente.innerHTML = '<option value="">Seleccione el paciente</option>';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.identificacion + " - " + item.nombre
                paciente.appendChild(option)
            });
        })
        .catch(error => console.error('Error al cargar especialidades:', error));
}

function eliminarCita(){
    var idCita = $("#idCita").val();

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = $('#urlBase').data("ruta")
    fetch(`${url}citas/eliminarcita`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Esta cabecera adicional
        },
        body: JSON.stringify({
            idCita: idCita
        })
    })
    .then(response => response.json())
        .then(data => {
            if (data.success) {
                swal("¡Buen trabajo!",
                    data.message,
                    "success");
                    cargarCitas();
                    $('#modalCitasDeta').modal('toggle');
            } else {
                swal("¡Alerta!",
                    "La operación fue realizada exitosamente",
                    data.message,
                    "success");
            }
        })
        .catch(error => console.error('Error al cargar disponibilidad:', error));
}

function atrasAddCita() {
    document.getElementById("calendaCita").style = "display: block"
    document.getElementById("calendaCitaPaci").style = "display: none"
    var btm_atras = document.getElementById("btnAtras")
    btm_atras.style.display = "none"

    var cancelRegistro = document.getElementById("cancelRegistro");
    cancelRegistro.style.display = "initial";

    var btnGuardar = document.getElementById("btnGuardar")
    btnGuardar.textContent = " Continuar"
    btnGuardar.disabled = false

    var iconElement = document.createElement('i')
    iconElement.className = 'fa fa fa-arrow-right'
    btnGuardar.insertBefore(iconElement, btnGuardar.firstChild)

    btnGuardar.onclick = function () {
        continuar();
    };
}

function habPacExist() {
    var btnGuardar = document.getElementById("btnGuardar")
    btnGuardar.onclick = function () {
        guardarCita(2);
    };
}

function salirAddCita() {
    $('#event-modal-add-cita').modal('toggle')

    $('#profesional').val("").trigger('change.select2')
    $('#especialidad').val("").trigger('change.select2')
    $('#duracionCita').val("20").trigger('change.select2')
    $('#fechaHoraSelCita').val("")

    atrasAddCita()
}

function selecPaciente(id) {
    document.getElementById("idPaciente").value = id
}

function habPacNuevo() {
    var btnGuardar = document.getElementById("btnGuardar");
    btnGuardar.onclick = function () {
        guardarCita(1);
    };
}

function guardarCita(opc) {
    var notCli;

    document.getElementById("opc").value = opc;

    if ($("#profesional").val().trim() === "") {
        swal({
            type: "warning",
            title: "Oops...",
            text: "Debes de seleccionar el profesional...",
            confirmButtonClass: "btn btn-primary",
            timer: 1500,
            buttonsStyling: false
        });
        return;
    }
    if ($("#especialidad").val().trim() === "") {
        swal({
            type: "warning",
            title: "Oops...",
            text: "Debes de seleccionar el tipo de consulta",
            confirmButtonClass: "btn btn-primary",
            timer: 1500,
            buttonsStyling: false
        });
        return;
    }
    if ($("#fechaHoraSelCita").val().trim() === "") {
        swal({
            type: "warning",
            title: "Oops...",
            text: "Debes de seleccionar la hora y fecha de cita",
            confirmButtonClass: "btn btn-primary",
            timer: 1500,
            buttonsStyling: false
        });
        return;
    }

    const notifCliente = document.getElementById('notifCliente');

    if (notifCliente.checked) {
        notCli = "si";
    } else {
        notCli = "no";
    }

    if (opc == 1) {

        if ($("#tipoId").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de seleccionar tipo de indetificación",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }
        if ($("#identificacion").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de ingresar el número de indetificación",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }

        if ($("#tipoUsuario").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de seleccionar el tipo de usuario",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }


        if ($("#fechaNacimiento").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de ingresar la fecha de nacimiento",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }
        if ($("#primerNombre").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de ingresar el nombre del paciente",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }
        if ($("#primerApellido").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de ingresar el apellido del paciente",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }
        if ($("#telefono").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de ingresar el teléfono del paciente",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }
        if ($("#email").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de ingresar el email del paciente",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }
    } else if (opc === 2) {
        if ($("#paciente").val().trim() === "") {
            swal({
                type: "warning",
                title: "Oops...",
                text: "Debes de seleccionar el paciente",
                confirmButtonClass: "btn btn-primary",
                timer: 1500,
                buttonsStyling: false
            });
            return;
        }
    }

    loader = document.getElementById('loader');
    loadNow(0);
    $("#notCliente").remove();
    const formProfesional = document.getElementById('formCita');
    formProfesional.append("<input type='hidden' id='notCliente' name='notCliente'  value='" +
        notCli +
        "'>");
    const formData = new FormData(formProfesional);
    const url = $('#urlBase').data("ruta")
    fetch(`${url}citas/guardar`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                loadNow(1)
                swal(data.title, data.message, data.success)
                setTimeout(() => {
                    salirAddCita()
                }, 1000)
                cargarCitas()
            } else {
                swal(data.title, data.message, data.success)
            }
        })
        .catch(error => {
            console.error("Error al enviar los datos:", error);
        });

}


function cargarCitas() {

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = $('#urlBase').data("ruta")
    fetch(`${url}citas/agenda`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Esta cabecera adicional
        },
        body: JSON.stringify({})
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const eventos = data.disponibilidad.map(item => {
                if (item.tblo == "CITAS") {
                    return {
                        start: item.inicio,
                        end: item.final,
                        title: `${item.primer_nombre} ${item.primer_apellido}`,
                        prof: item.nomprof,
                        estado: item.estado,
                        idCita: item.id,
                        bloq: item.tblo
                    };
                }
            }).filter(Boolean); // Filtra los eventos nulos

            // Actualiza el calendario con los eventos de disponibilidad
            actualizarCalendario(eventos);
        })
        .catch(error => console.error('Error al cargar disponibilidad:', error));
}

function actualizarCalendario(eventos) {
    const calendarObj = mainCalendar.$calendarObj;
    disponibilidadJSON = eventos;
    // Eliminar eventos existentes para evitar duplicados
    calendarObj.getEvents().forEach(event => event.remove());
    // Agregar los nuevos eventos al calendario
    eventos.forEach(evento => calendarObj.addEvent(evento));
}

function cambioEstado(estado) {
    swal({
        title: "Esta seguro de cambiar el estado a " + estado + " ?",
        text: "¡No podrás revertir esto!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, cambiar!",
        cancelButtonText: "Cancelar",
        confirmButtonClass: "btn btn-warning",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false
    }, function (isConfirm) {
        if (isConfirm) {
            procederCambiarEstado(estado);

        } 
    });

}


function procederCambiarEstado(estado) {
    var idCita = $("#idCita").val();

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = $('#urlBase').data("ruta")
    fetch(`${url}citas/cambiaEstadoCita`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Esta cabecera adicional
        },
        body: JSON.stringify({
            idCita: idCita,
            estadoCita: estado
        })
    })
    .then(response => response.json())
        .then(data => {
            if (data.estado == "success") {
                swal({
                    type: "success",
                    title: "¡Buen trabajo!",
                    text: "El estado de la cita fue cambiada a " +
                        estado + " exitosamente",
                    confirmButtonClass: "btn btn-primary",
                    timer: 2000,
                    buttonsStyling: false
                });
                cargarCitas();
            }else{
                swal({
                    type: "error",
                    title: "¡Error!",
                    text: "Error al cambiar el estado de la cita",
                    confirmButtonClass: "btn btn-primary",
                    timer: 2000,
                    buttonsStyling: false
                });
            }
        })
        .catch(error => console.error('Error al cargar disponibilidad:', error));
}

function salirComentario() {
    $('#modalComentarios').modal('toggle');
    var modal = new bootstrap.Modal(document.getElementById("modalCitasDeta"), {
        backdrop: 'static',
        keyboard: false
    });

    modal.show();
}

function addComentario() {
    var modal = new bootstrap.Modal(document.getElementById("modalComentarios"), {
        backdrop: 'static',
        keyboard: false
    });

    $('#modalCitasDeta').modal('toggle');

    // Mostrar el modal
    modal.show();
    var idCita = $("#idCita").val();
    const url = $('#urlBase').data("ruta")
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(`${url}/citas/cargarComentario`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Esta cabecera adicional
        },
        body: JSON.stringify({
            idCita: idCita
        })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            $("#comentarioCitaVal").val(data.comentario);
        })
        .catch(error => console.error('Error al cargar disponibilidad:', error));

}

function notifCPaciente(){
    var idCita = $("#idCita").val();
    const url = $('#urlBase').data("ruta")
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(`${url}citas/notificarPaciente`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Esta cabecera adicional
        },
        body: JSON.stringify({
            idCita: idCita
        })
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.estado == "success") {
                swal({
                    type: "success",
                    title: "¡Buen trabajo!",
                    text: "Notificación enviada exitosamente",
                    confirmButtonClass: "btn btn-primary",
                    timer: 2000,
                    buttonsStyling: false
                });
            }else{
                swal({
                    type: "error",
                    title: "¡Error!",
                    text: "Error al enviar notificación",
                    confirmButtonClass: "btn btn-primary",
                    timer: 2000,
                    buttonsStyling: false
                });
            }
        })
        .catch(error => console.error('Error al cargar disponibilidad:', error));
}


function guardarComentario() {
    var idCita = $("#idCita").val();
    var comentario = $("#comentarioCitaVal").val();
    const url = $('#urlBase').data("ruta")
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(`${url}citas/guardarComentario`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Esta cabecera adicional
        },
        body: JSON.stringify({
            idCita: idCita,
            comentario: comentario
        })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.estado == "ok") {
                swal({
                    type: "success",
                    title: "",
                    text: "Operación realizada exitosamente",
                    confirmButtonClass: "btn btn-primary",
                    timer: 2000,
                    buttonsStyling: false
                });

                $("#cometarioCita").html(data.comentario);
            }
        })
        .catch(error => console.error('Error al cargar disponibilidad:', error));
}
