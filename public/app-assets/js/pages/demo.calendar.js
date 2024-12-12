!(function (l) {
    "use strict";
    function e() {
        this.$body = l("body");
        const eventModal = document.getElementById("event-modal");
        const calendar = l("#calendar");
        const formEvent = l("#form-event");

        // Solo inicializar si los elementos existen
        if (eventModal && calendar.length && formEvent.length) {
            this.$modal = new bootstrap.Modal(eventModal, { backdrop: "static" });
            this.$calendar = calendar;
            this.$formEvent = formEvent;
            this.$btnNewEvent = l("#btn-new-event");
            this.$btnDeleteEvent = l("#btn-delete-event");
            this.$btnSaveEvent = l("#btn-save-event");
            this.$modalTitle = l("#modal-title");
            this.$calendarObj = null;
            this.$selectedEvent = null;
            this.$newEventData = null;
        }
    }

    e.prototype.onEventClick = function (e) {
        this.$formEvent[0].reset(),
            this.$formEvent.removeClass("was-validated"),
            (this.$newEventData = null),
            this.$btnDeleteEvent.show(),
            this.$modalTitle.text("Edit Event"),
            this.$modal.show(),
            (this.$selectedEvent = e.event),
            l("#event-title").val(this.$selectedEvent.title),
            l("#event-category").val(this.$selectedEvent.classNames[0]);
    };

    e.prototype.onSelect = function (e) {
        this.$formEvent[0].reset(),
            this.$formEvent.removeClass("was-validated"),
            (this.$selectedEvent = null),
            (this.$newEventData = e),
            this.$btnDeleteEvent.hide(),
            this.$modalTitle.text("Add New Event"),
            this.$modal.show(),
            this.$calendarObj.unselect();
    };

    e.prototype.init = function () {
        var e = new Date(l.now());

        var t = [
            { title: "Lunch", start: new Date(l.now() + 178e6), end: new Date(l.now() + 138e6), className: "bg-warning" },
            { title: "Your Birthday", start: e, end: e, className: "bg-success" },
            { title: "Do Homework", start: new Date(l.now() + 168e6), className: "bg-primary" },
            { title: "SLEEP TIGHT", start: new Date(l.now() + 338e6), end: new Date(l.now() + 338e6), className: "bg-danger" },
        ];

        var a = this;
        a.$calendarObj = new FullCalendar.Calendar(a.$calendar[0], {
            locale: 'es',
            slotDuration: "00:20:00",
            slotMinTime: "08:00:00",
            slotMaxTime: "19:00:00",
            themeSystem: "bootstrap",
            bootstrapFontAwesome: !1,
            buttonText: { today: "Hoy", month: "Mes", week: "Semana", day: "Día", prev: "Anterior", next: "Siguiente" },
            initialView: "timeGridWeek",
            hiddenDays: [0],
            handleWindowResize: !0,
            height: l(window).height() - 200,
            headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek,timeGridDay" },
            initialEvents: t,
            editable: !0,
            droppable: !0,
            selectable: !0,
            allDaySlot: false,
            height: 'auto',
            slotLabelFormat: {
                hour: "2-digit",
                minute: "2-digit",
                omitZeroMinute: false,
                meridiem: true,
            },
            dateClick: function (e) {
                a.onSelect(e);
            },
            eventClick: function (e) {
                a.onEventClick(e);
            },
        });

        a.$calendarObj.render();

        a.$btnNewEvent.on("click", function (e) {
            a.onSelect({ date: new Date(), allDay: !0 });
        });

        a.$formEvent.on("submit", function (e) {
            e.preventDefault();
            var t,
                n = a.$formEvent[0];
            n.checkValidity()
                ? (a.$selectedEvent
                      ? (a.$selectedEvent.setProp("title", l("#event-title").val()), a.$selectedEvent.setProp("classNames", [l("#event-category").val()]))
                      : ((t = { title: l("#event-title").val(), start: a.$newEventData.date, allDay: a.$newEventData.allDay, className: l("#event-category").val() }), a.$calendarObj.addEvent(t)),
                  a.$modal.hide())
                : (e.stopPropagation(), n.classList.add("was-validated"));
        });

        l(a.$btnDeleteEvent.on("click", function (e) {
            a.$selectedEvent && (a.$selectedEvent.remove(), (a.$selectedEvent = null), a.$modal.hide());
        }));
    };

    // Solo inicializar CalendarApp si los elementos existen
    if (document.getElementById("event-modal") && l("#calendar").length && l("#form-event").length) {
        l.CalendarApp = new e();
        l.CalendarApp.Constructor = e;
    }
})(window.jQuery),

(function () {
    "use strict";
    if (window.jQuery.CalendarApp) {
        window.jQuery.CalendarApp.init();
    }
})();
