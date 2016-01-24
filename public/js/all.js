$(function () {
	//remove rows
    $('.remove-row-button').on('click', function(ele) {
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
		function(){
		  	$.ajax({
				type: 'POST',
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				url:  ele.data('url'),
				data: {
	                id: row.data('id')
	            },
                //dataType: 'json',
	            success: function(response) {
	                if (response == 1) {
                        row.remove();
	                }
	            }
			});
		});
	});

    //edit rows
    $('.edit-row').click(function(ele) {
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

				$('.sortable > tr').each(function(){
					var id = $(this).data('id');
					order.push(id);
				});

				sortUpdate(order);
			}
		});

		function sortUpdate(order){
			$.ajax({
				type: 'POST',
                contentType: "application/x-www-form-urlencoded; charset=utf-8",
				url: document.location.href,
				data: {
					order: order
				}
			});
		}
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
});

//slugify input
function slugify(ele) {
    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    delay(function(){
        ele.value = ele.value.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }, 500);
}