$(function() {

    $( ".datepicker" ).datetimepicker({
        controlType: 'select',
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        stepMinute: 15,
        timeFormat: "HH:mm"
    });

});