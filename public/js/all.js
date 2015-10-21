//search
$(function () {
	if (!$('.searchable').length) {
		return;
	}

	$('#search').hideseek({
		highlight: false,
		nodata: 'No results found',
		list: '.searchable',
		element: 'tr'
	});
});

//sort
$(function () {
	if (!$('.sortable').length) {
		return;
	}
	
	//disabling onclick redirect behavior on sortable handle
	$('.sortable-handle').on('click', function() {
		return false;
	});

	Sortable.create($('.sortable').get(0), {
		handle: '.sortable-handle',
		animation: 150,
		onEnd: function (evt) {
			var order = []; 
			$('.sortable > tr').each(function(){
				var id = $(this).data('id');
				order.push(id);
			});
			sortUpdate(order);
		}
	});
	
	function sortUpdate(sequence){
		var url = document.location.href;
		
		$.ajax({
			type: "POST",
			url: url,
			data: {
				sort: sequence
			}
		});
	}
});

//textedit
$(function () {
	if (!$('.summernote').length) {
		return;
	}
	
	$('.summernote').summernote({
		height: 200
	});
});

//slugify
function slugify(ele) {
	ele.value = ele.value.toString().toLowerCase()
		.replace(/\s+/g, '-')           // Replace spaces with -
		.replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		.replace(/\-\-+/g, '-')         // Replace multiple - with single -
		.replace(/^-+/, '')             // Trim - from start of text
		.replace(/-+$/, '');            // Trim - from end of text
}
