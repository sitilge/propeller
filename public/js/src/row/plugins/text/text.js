$(function () {
    //edit text
    var summernote = $('.summernote');

    if (summernote.length) {
        summernote.summernote({
            height: 200,
            toolbar: [
                ['paragraph', ['style', 'ul', 'ol', 'paragraph', 'height']],
                ['font', ['fontname', 'fontsize', 'color', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['insert', ['link', 'picture', 'video', 'table']],
                ['misc', ['fullscreen', 'undo', 'redo']]
            ],
            callbacks: {
                onChange: function () {
                    var code = $(this).summernote('code');
                    var textarea = $('#summernote-' + $(this).attr('id'));
                    textarea.val(code);
                }
            }
        });
    }
});