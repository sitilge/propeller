$(function () {
	//remove rows
    $('.remove-row-button').on('click', function() {
    	var link = $(this).attr('link');
    	var elem = $(this);
		swal({
            title: "Are you sure?",
            text: "The row will be deleted permanently.",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Delete",
            showCancelButton: true,
            cancelButtonText: "Cancel",
            closeOnConfirm: true
		},
		function(){
		  	$.ajax({
				type: "POST",
				url: link,
				data: {
	                id: elem.parent().parent().attr('data-id')
	            },
	            success: function(response) {
	                if (response == 1) {
	                    elem.parent().parent().remove();
	                }
	            }
			});
		});
	});

    //search
	if (!$('.searchable').length) {
		return;
	}

	$('#search').hideseek({
		highlight: false,
		nodata: 'No results found',
		list: '.searchable',
		element: 'tr'
	});

	//sort
	var sortable = $('.sortable');

	if (!sortable.length) {
		return;
	}

	//disabling onclick redirect behavior on sortable handle
	$('.sortable-handle').on('click', function() {
		return false;
	});

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
			type: "POST",
			url: document.location.href,
			data: {
				order: order
			}
		});
	}

	//textedit
	var summernote = $('.summernote');

	if (!summernote.length) {
		return;
	}

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
});

//slugify
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