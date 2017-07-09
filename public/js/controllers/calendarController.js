liriaApp.controller('calendarController', function($rootScope, $scope, $log, Login, $location, Calendar, Utils, Tratamentos, Utils) {

    $rootScope.logged = true;
    $scope.logged = true;

    $scope.entradaCalendario = new Object();

    Login.checkLogin()

        .success(function (data) {

            if (data.error) {
                $location.path('/login');
                $rootScope.logged = false;
                $scope.logged = false;
            }
        });

    Tratamentos.get()

        .success(function(data) {

            if(data.error){

                $rootScope.logged = false;
                $scope.logged = false;
                $location.path('/login');

            }else{
                $rootScope.logged = true;
                $scope.logged = true;
                $scope.dataTratamento = data;
            }
        })

    //Sets up the calendar
    $(document).ready(function() {

        $('#calendar').fullCalendar({
            locale: 'pt-br',
            displayEventEnd: true,
            defaultView: 'agendaWeek',
            allDaySlot: false,
            minTime: "07:00:00",
            maxTime: "20:00:00",
            slotDuration: "00:30:00",
            slotLabelInterval: "00:30:00",
            slotLabelFormat: 'H:mm',
            header: {
                left: 'today prev,next',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },

            eventClick: function(calEvent, jsEvent, view) {

                $scope.warning = null;
                $scope.isExistingEvent = true;

                Utils.setAutoCompleteSearchParam(calEvent.title);

                field = $('#cliente');
                field.val(calEvent.client);
                field.trigger('input');

                field = $('#dataIniEvento');
                field.val(Utils.formatCalendarTimestampToDate(calEvent.start.format(), 'date'));
                field.trigger('input');
                field.removeAttr('disabled');

                field = $('#horaIniEvento');
                field.val(Utils.formatCalendarTimestampToDate(calEvent.start.format(), 'hour'));
                field.trigger('input');
                field.removeAttr('disabled');

                field = $('#dataFimEvento');
                field.val(Utils.formatCalendarTimestampToDate(calEvent.end.format(), 'date'));
                field.trigger('input');
                field.removeAttr('disabled');

                field = $('#horaFimEvento');
                field.val(Utils.formatCalendarTimestampToDate(calEvent.end.format(), 'hour'));
                field.trigger('input');

                //Display the modal to input the schedule information
                $('#myModalCalendarEvent').modal({
                    show: 'true'
                });

                $scope.selectedEvent = calEvent;
            },

            //Called every time the schedule date range changes
            viewRender: function(view, element){

                $('#calendar').fullCalendar('removeEvents');

                $scope.searchSchedule = new Object();
                $scope.searchSchedule.dataIniEvento = Utils.formatCalendarToDate(view.start.format()) + ' 00:00';
                $scope.searchSchedule.dataFimEvento = Utils.formatCalendarToDate(view.end.format()) + ' 23:59';

                Calendar.getCalendarEvents($scope.searchSchedule)

                    .success(function(data){

                        $scope.scheduleEntry = data.result;

                        for(i = 0; i < $scope.scheduleEntry.length ; i++){

                            var newEvent = new Object();

                            newEvent.id = $scope.scheduleEntry[i].id; //The ID of the event in the database
                            newEvent.title = $scope.scheduleEntry[i].tratamento;
                            newEvent.start = Utils.formatDateToCalendar($scope.scheduleEntry[i].data_inicio);
                            newEvent.end = Utils.formatDateToCalendar($scope.scheduleEntry[i].data_final);
                            newEvent.allDay = false;
                            newEvent.client = $scope.scheduleEntry[i].cliente;
                            $('#calendar').fullCalendar('renderEvent', newEvent);
                        }
                    });
            },

            //Addes the
            eventRender: function(event, element) {
                var title = element.find('.fc-title').append("<br/>" + event.client);
            },

            dayClick: function(date, jsEvent, view) {


                $scope.warning = null;
                $scope.isExistingEvent = false;

                $scope.entradaCalendario.tratamento = '';
                $scope.entradaCalendario.cliente = '';
                $scope.entradaCalendario.dataIniEvento = '';
                $scope.entradaCalendario.horaIniEvento = '';
                $scope.entradaCalendario.dataFimEvento = '';
                $scope.entradaCalendario.horaFimEvento = '';

                var view = $('#calendar').fullCalendar('getView');

                //Adjusts the date and time contend depending on the view
                if(view.name == 'agendaWeek' || view.name == 'agendaDay'){

                    var data = $('#dataIniEvento');
                    data.val(Utils.formatCalendarTimestampToDate(date.format(), 'date'));
                    data.trigger('input');

                    data = $('#dataFimEvento');
                    data.val(Utils.formatCalendarTimestampToDate(date.format(), 'date'));
                    data.trigger('input');

                    var hour = $('#horaIniEvento');
                    hour.val(Utils.formatCalendarTimestampToDate(date.format(), 'hour'));
                    hour.trigger('input');
                    $("#horaIniEvento").attr('disabled','disabled');

                }else{

                    var hour = $('#horaIniEvento');
                    hour.val(null);
                    hour.trigger('input');

                    $("#horaIniEvento").removeAttr('disabled');

                    var data = $('#dataIniEvento');
                    data.val(Utils.formatCalendarToDate(date.format()));
                    data.trigger('input');

                    data = $('#dataFimEvento');
                    data.val(Utils.formatCalendarToDate(date.format()));
                    data.trigger('input');

                }

                //Display the modal to input the schedule information
                $('#myModalCalendarEvent').modal({
                    show: 'true'
                });

            }
        })

    });

    $scope.createCalendarEvent = function(){

        $scope.entradaCalendario.usuario = window.localStorage.getItem('name');

        if($scope.entradaCalendario.tratamento == ''
            || $scope.entradaCalendario.dataIniEvento == ''
            || $scope.entradaCalendario.horaIniEvento == ''
            || $scope.entradaCalendario.dataFimEvento == ''
            || $scope.entradaCalendario.horaFimEvento == ''
            || $scope.entradaCalendario.cliente == ''){

            $scope.warning = "Todos os campos devem ser preenchidos.";

        }else {

            Calendar.createCalendarEvent($scope.entradaCalendario)

                .success(function (data) {

                    var newEvent = new Object();

                    newEvent.id = data.result.id; //The ID of the event in the database
                    newEvent.title = $scope.entradaCalendario.tratamento;
                    newEvent.start = Utils.formatDateToCalendar($scope.entradaCalendario.dataIniEvento + " " + $scope.entradaCalendario.horaIniEvento);
                    newEvent.end = Utils.formatDateToCalendar($scope.entradaCalendario.dataFimEvento + " " + $scope.entradaCalendario.horaFimEvento);
                    newEvent.allDay = false;
                    newEvent.client = $scope.entradaCalendario.cliente;
                    $('#calendar').fullCalendar('renderEvent', newEvent);

                    $('#myModalCalendarEvent').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                });
        }
    }

    $scope.updateCalendarEvent = function(){

        if($('#tratamento').val() == ''
            || $('#dataIniEvento').val() == ''
            || $('#horaIniEvento').val() == ''
            || $('#dataFimEvento').val() == ''
            || $('#horaFimEvento').val() == ''
            || $('#cliente').val() == ''){

            $scope.warning = "Todos os campos devem ser preenchidos.";

        }else {

            $scope.entradaCalendario.usuario = window.localStorage.getItem('name');
            $scope.entradaCalendario.id = $scope.selectedEvent.id;
            $scope.entradaCalendario.tratamento = $('#tratamento').val();
            Calendar.updateCalendarEvent($scope.entradaCalendario)

                .success(function (data) {

                    $scope.selectedEvent.title = $('#tratamento').val();
                    $scope.selectedEvent.start = Utils.formatDateToCalendar($('#dataIniEvento').val() + " " + $('#horaIniEvento').val());
                    $scope.selectedEvent.end = Utils.formatDateToCalendar($('#dataFimEvento').val() + " " + $('#horaFimEvento').val());
                    $scope.selectedEvent.allDay = false;
                    $scope.selectedEvent.client = $('#cliente').val();
                    $('#calendar').fullCalendar('updateEvent', $scope.selectedEvent);

                    $('#myModalCalendarEvent').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();

                });
        }
    }

    $scope.removeCalendarEvent = function(){

        $scope.entradaCalendario.id = $scope.selectedEvent.id;
        Calendar.removeCalendarEvent($scope.entradaCalendario)

            .success(function(data){

                $('#calendar').fullCalendar('removeEvents', $scope.selectedEvent.id);

                $('#myModalCalendarEvent').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });
    }

    $scope.renderTreatment = function(tratamento){
        return tratamento.nome;
    }

});