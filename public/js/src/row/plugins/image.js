$(function () {
    //image plugin upload
    $(document).on('change', '.btn-file :file', function() {
        var input = $(this);

        if (input.prop('files') && input.prop('files')[0]) {
            var reader = new FileReader();

            reader.onload = function (ele) {
                var preview = $(input).data('preview');

                var previewEle = $('#preview-' + preview);
                previewEle.css('background-image','url("' + ele.target.result + '")');
                previewEle.css('width','100%');
                previewEle.css('height','100%');
                previewEle.css('background-size','cover');
                previewEle.css('background-position','center');

                var file = input.val().replace(/^C:\\fakepath\\/, '');
                var filename = sanitize(file.split('.').shift());
                var extension = sanitize(file.split('.').pop());

                $('#' + preview).val(path + filename + '.' + extension);
            };

            reader.readAsDataURL(input.prop('files')[0]);
        }
    });
});

//image plugin update
function updateImage(input) {
    var imagePath = input.data('file');

    var preview = input.data('preview');

    var previewEle = $('#preview-' + preview);
    previewEle.css('background-image','url(' + imagePath + ')');
    previewEle.css('width','100%');
    previewEle.css('height','100%');
    previewEle.css('background-size','cover');
    previewEle.css('background-position','center');

    $('#' + preview).val(imagePath);

    $('#gallery').modal('hide');
}

//image plugin remove
function removeImage(input) {
    var preview = input.data('preview');

    $('#preview-' + preview).css('background-image', 'url("/img/system/image-empty.png")');
    $('#' + preview).val('');
}

//image plugin delete
function deleteImage(input) {
}