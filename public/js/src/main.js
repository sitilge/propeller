$(function () {
    //delete rows
    $('.delete-row-button').on('click', function(ele) {
        var ele = $(ele.target);
        var row = ele.parent().parent();

        swal({
                title: 'Are you sure?',
                text: 'The row will be removed permanently.',
                type: 'error',
                confirmButtonClass: 'btn-danger',
                confirmButtonText: 'Delete',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                closeOnConfirm: true
            },
            function() {
                $.ajax({
                    type: 'POST',
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

    //update rows
    $('.update-row').click(function(ele) {
        window.location.href = $(ele.target).data('url');
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
    var sortable = $('.sortable');

    if (sortable.length) {
        Sortable.create(sortable.get(0), {
            handle: '.sortable-handle',
            animation: 150,
            onEnd: function () {
                var order = [];

                $('.sortable > tr').each(function() {
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

    //edit text
    var summernote = $('.summernote');

    if (summernote.length) {
        summernote.summernote({
            height: 200,
            toolbar:[
                ['paragraph', ['style', 'ul', 'ol', 'paragraph', 'height']],
                ['font', ['fontname', 'fontsize', 'color', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['insert', ['link', 'picture', 'video', 'table']],
                ['misc', ['fullscreen', 'undo', 'redo']]
            ],
            callbacks: {
                onChange: function() {
                    var code = $(this).summernote('code');
                    var textarea = $('#summernote-' + $(this).attr('id'));
                    textarea.val(code);
                }
            }
        });
    }

    //slugify text
    $('.slugify').on('keyup', function() {
        slugify(this);
    });

    $('.slugify').on('blur', function() {
        slugify(this, true);
    });

    //image plugin
    $('#gallery-button').on('click',function() {
        preview = $(this).parent().parent().children(':first-child').find('.image').attr('id');
    });

    $(document).on('change', '.btn-file :file', function() {
        var input = $(this);

        if (input.prop('files') && input.prop('files')[0]) {
            var reader = new FileReader();

            reader.onload = function (ele) {
                var preview = $(input).attr('preview');

                var previewEle = $('#' + preview);
                previewEle.css('background-image','url("' + ele.target.result + '")');
                previewEle.css('width','100%');
                previewEle.css('height','100%');
                previewEle.css('background-size','cover');
                previewEle.css('background-position','center');

                var file = input.val().replace(/^C:\\fakepath\\/, '');
                var filename = sanitize(file.split('.').shift());
                var extension = sanitize(file.split('.').pop());

                var imagePath = dir + filename + '.' + extension;

                $('#' + preview.split('-')[1]).val(imagePath);
            };

            reader.readAsDataURL(input.prop('files')[0]);
        }
    });
});

//slugify text
function slugify(ele, focus) {
    if (focus){
        ele.value = sanitize(ele.value);
    }
    else {
        var start = ele.selectionStart;
        var end = ele.selectionEnd;

        delay(function() {
            var lengthBefore = ele.value.length;
            ele.value = sanitize(ele.value);
            var lengthAfter = ele.value.length;
            var removed = lengthBefore-lengthAfter;

            ele.setSelectionRange(start-removed, end-removed);
        }, 2000);
    }
}

var timer = 0;

function delay(callback, ms) {
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
}

//sanitize input
function sanitize(ele) {
    return ele.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

//image plugin
function updateImage(ele) {
    var imagePath = ele.data('file');

    var previewEle = $('#' + preview);
    previewEle.css('background-image','url(' + imagePath + ')');
    previewEle.css('width','100%');
    previewEle.css('height','100%');
    previewEle.css('background-size','cover');
    previewEle.css('background-position','center');

    $('#' + preview.split('-')[1]).val(imagePath);
    $('#gallery').modal('hide');
}

function removeImage(input) {
    var preview = $(input).attr('preview');
    $('#' + preview).css('background-image', 'url("/img/system/image-empty.png")');
    $('#' + preview.split('-')[1]).val('');
}

function deleteImage(input) {
}