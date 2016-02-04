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
        var ele = $(ele.target);

        window.location.href = ele.data('url');
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

	//image plguin
    $('#gallery-button').on('click',function() {
        preview = $(this).parent().parent().children(':first-child').find('.image').attr('id');
    });

    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;
        if( input.length ) {
            input.val(log);
        }
    });

    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        previewImage(this);
        input.trigger('fileselect', [numFiles, label]);
    });
});

//slugify text
function slugify(ele, focus) {
    if (focus){
        ele.value = ele.value.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');
    }
    else {
        var start = ele.selectionStart;
        var end = ele.selectionEnd;

        delay(function() {
            var lengthBefore = ele.value.length;

            ele.value = ele.value.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text

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

//image plguin
function uploadImage(button) {
    var imageLink = '/img/' + button.attr('file').split('/img/')[1];
    var previewEle = $('#'+preview);

    previewEle.css('background-image','url(' + imageLink + ')');
    previewEle.css('width','100%');
    previewEle.css('height','100%');
    previewEle.css('background-size','cover');
    previewEle.css('background-position','center');

    $('#' + preview.split('-')[1]).val(imageLink);
    $('#gallery').modal('hide');
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (ele) {
            var preview = $(input).attr('preview');
            var previewEle = $('#' + preview);
            previewEle.css('background-image','url("' + ele.target.result + '")');
            previewEle.css('width','100%');
            previewEle.css('height','100%');
            previewEle.css('background-size','cover');
            previewEle.css('background-position','center');

            var imageExportPath = dir + input.value;
            $('#' + preview.split('-')[1]).val(imageExportPath);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(input) {
    var preview = $(input).attr('preview');
    $('#' + preview).css('background-image', 'url("/img/system/image-empty.png")');
    $('#' + preview.split('-')[1]).val('');
}

function deleteImage() {
}