$(function () {
    //create row
    $('.create-row-button').click(function(ele) {
        var ele = $(ele.target);

        $.ajax({
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=utf-8',
            url: ele.data('url'),
            data: {},
            success: function(response) {
                $('.main').first().html(response);
            }
        });
    });

    //update row
    $('.update-row-button, .update-row').click(function(ele) {
        window.location.href = $(ele.target).data('url');
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

    //search rows
    var searchable = $('.searchable');

    if (searchable.length) {
        $('#search').hideseek({
            highlight: false,
            nodata: 'No results found',
            list: '.searchable',
            element: 'tr'
        });
    }

    //sort rows
    var orderable = $('.orderable');

    if (orderable.length) {
        Sortable.create(orderable.get(0), {
            handle: '.orderable-handle',
            animation: 150,
            onEnd: function () {
                var order = [];

                $('.orderable > tr').each(function() {
                    var id = $(this).data('id');
                    order.push(id);
                });

                $.ajax({
                    type: 'POST',
                    contentType: 'application/x-www-form-urlencoded; charset=utf-8',
                    url: document.location.href,
                    data: {
                        order: order
                    }
                });
            }
        });
    }
});