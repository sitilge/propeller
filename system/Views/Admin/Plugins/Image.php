<link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<link rel="stylesheet" href="/css/bootstrap-image-gallery.css">

<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<script src="/js/bootstrap-image-gallery.js"></script>

 <style>
     .size {
         width: 100px;
         height: 100px;
         display: inline-block;
     }
     .image {
         background-size: cover;
         background-repeat: no-repeat;
         background-position: center;
         
     }
    .btn-file {
        position: relative;
        overflow: hidden;
    }
    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>

<?php if (!empty($structure)) : ?>

    <div class="row">
        <span class='size col-md-1'>Current</span>
        <span class='size col-md-1 btn btn-primary btn-file'>Upload <input type="file"></span>
        
        <script>
        $(document).on('change', '.btn-file :file', function() {
  var input = $(this),
      numFiles = input.get(0).files ? input.get(0).files.length : 1,
      label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
  input.trigger('fileselect', [numFiles, label]);
});

$(document).ready( function() {
    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        
        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;
        
        if( input.length ) {
            input.val(log);
        } else {
            if( log ) alert(log);
        }
        
    });
});
        </script>
        
    </div>
    <?php foreach ($structure as $dir => $files) : ?>
        <div class="row">
            <div class='size col-md-1'><?php echo $dir; ?></div>
            <?php foreach ($files as $file) : ?>
                <!--<div >-->
                    <a class="size image img-responsive col-md-1" style="background-image: url('<?php echo $file; ?>')" href="<?php echo $file; ?>" data-gallery>
                        <img class='hidden' src="<?php echo $file; ?>" alt="">
                    </a>
                <!--</div>-->
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <!-- The container for the modal slides -->
    <div class="slides"></div>
    <!-- Controls for the borderless lightbox -->
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
    <!-- The modal dialog, which will be used to wrap the lightbox content -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body next"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-left prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn btn-primary next">
                        Next
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>