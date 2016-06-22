$(function () {
    //datetimepicker
    $('.datetimepicker').each(function() {
        var ele = $(this);
        var format = ele.data('format');

        ele.datetimepicker({
            format: format
        });
    });
});