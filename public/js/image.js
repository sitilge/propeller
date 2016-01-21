var preview = "";

function previewImage(input){
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var preview = $(input).attr('preview');
            var previewEle = $('#'+preview);
            previewEle.css('background-image',"url('"+ e.target.result+"')");
            previewEle.css('width',"100%");
            previewEle.css('height',"100%");
            previewEle.css('background-size',"cover");
            previewEle.css('background-position',"center");

            var imageExportPath = dir + input.value;
            $('#'+preview.split('-')[1]).val(imageExportPath);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function uploadImage(button){
    var fullimageLink = button.attr('file');
    var imageLink = '/img/'+fullimageLink.split('/img/')[1];
    var previewEle = $('#'+preview);
    previewEle.css('background-image',"url("+ imageLink+")");
    previewEle.css('width',"100%");
    previewEle.css('height',"100%");
    previewEle.css('background-size',"cover");
    previewEle.css('background-position',"center");

    var fileName = imageLink.split('/');
    fileName = fileName[fileName.length-1];

    var imageExportPath = dir+fileName;

    $('#'+preview.split('-')[1]).val(imageExportPath);
    $('#gallery').modal('hide');
}
$('#gallery-button').on('click',function(){
    preview = $(this).parent().parent().children(':first-child').find('.image').attr('id');
});

$(document).on('change', '.btn-file :file', function() {
  var input = $(this),
      numFiles = input.get(0).files ? input.get(0).files.length : 1,
      label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

    previewImage(this);
    input.trigger('fileselect', [numFiles, label]);
});

$(document).ready( function() {
    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        
        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;
        if( input.length ) {
            input.val(log);
        }
    });
});

function removeImage(input){
    var preview = $(input).attr('preview');
    $('#'+preview).css('background-image', 'url("https://placeholdit.imgix.net/~text?txtsize=29&bg=eeeeee&txtclr=000000&txt=Image&w=196&h=196&txttrack=0")');
    $('#'+preview.split('-')[1]).val('');
}

function deleteImage(){

}