var previewId = "";

function previewImage(input){
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var previewId = $(input).attr('previewId');
            var previewIdEle = $('#'+previewId);
            previewIdEle.css('background-image',"url('"+ e.target.result+"')");
            previewIdEle.css('width',"100%");
            previewIdEle.css('height',"100%");
            previewIdEle.css('background-size',"cover");
            previewIdEle.css('background-position',"center");

            var imageExportPath = dir + input.value;
            $('#'+previewId.split('-')[1]).val(imageExportPath);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function uploadImage(button){
    var fullimageLink = button.attr('file');
    var imageLink = '/img/'+fullimageLink.split('/img/')[1];
    var previewIdEle = $('#'+previewId);
    previewIdEle.css('background-image',"url("+ imageLink+")");
    previewIdEle.css('width',"100%");
    previewIdEle.css('height',"100%");
    previewIdEle.css('background-size',"cover");
    previewIdEle.css('background-position',"center");

    var fileName = imageLink.split('/');
    fileName = fileName[fileName.length-1];

    var imageExportPath = dir+fileName;

    $('#'+previewId.split('-')[1]).val(imageExportPath);
    $('#gallery').modal('hide');
}
$('#gallery-button').on('click',function(){
    previewId = $(this).parent().parent().children(':first-child').find('.image').attr('id');
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
    var previewId = $(input).attr('previewId');
    $('#'+previewId).css('background-image', 'url("https://placeholdit.imgix.net/~text?txtsize=29&bg=eeeeee&txtclr=000000&txt=Image&w=196&h=196&txttrack=0")');
    $('#'+previewId.split('-')[1]).val('');
}

function deleteImage(){

}