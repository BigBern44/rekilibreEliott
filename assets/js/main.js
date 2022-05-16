$(document).ready(function () {

    window.setTimeout(function () {
        $(".alert").fadeTo(500, 0).slideUp(500, function () {
            $(this).remove();
        });
    }, 2000);

    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
    $('select').selectpicker({
        title: 'Aucune sélection'
    });

    $('.dateyearpicker').datetimepicker({
        viewMode: 'years',
        locale: 'fr',
        format: 'L',
    });

    $('.datepicker').datetimepicker({
        locale: 'fr',
        format: 'L',
    });

    $('.timepicker').datetimepicker({
        locale: 'fr',
        format: 'LT'
    });
});