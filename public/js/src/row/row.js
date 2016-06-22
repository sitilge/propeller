$(function () {

    //create row
    $('.create-row-button').click(function(ele) {
        var ele = $(ele.target);
        var form = $('#form');

        $.ajax({
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=utf-8',
            url: ele.data('url'),
            data: form.serialize(),
            success: function(response) {
                window.location.href = $.parseJSON(response);
            }
        });
    });

    //update row
    $('.update-row-button').on('click', function(ele) {
        var ele = $(ele.target);
        var form = $('#form');

        $.ajax({
            type: 'PUT',
            contentType: 'application/x-www-form-urlencoded; charset=utf-8',
            url: ele.data('url'),
            data: form.serialize(),
            success: function (response) {
                window.location.href = $.parseJSON(response);
            }
        });
    });

    //delete row
    $('.delete-row-button').on('click', function(ele) {
        var ele = $(ele.target);
        var row = ele.parent().parent();

        swal({
                title: 'Are you sure?',
                text: 'The row will be deleted permanently.',
                type: 'error',
                confirmButtonClass: 'btn-danger',
                confirmButtonText: 'Delete',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                closeOnConfirm: true
            },
            function() {
                $.ajax({
                    type: 'DELETE',
                    contentType: 'application/x-www-form-urlencoded; charset=utf-8',
                    url: ele.data('url'),
                    data: {
                        id: ele.data('id')
                    },
                    success: function(response) {
                        if (row.is('tr')) {
                            row.remove();
                        } else {
                            window.location.href = $.parseJSON(response);
                        }
                    }
                });
            });
    });
});