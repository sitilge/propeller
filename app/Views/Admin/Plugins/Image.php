<div class="row images">
    <span class='size-large col-md-1'>
        <div id="preview-<?php echo $column;?>" class="image size-large"
            style="<?php echo (isset($data[$table]['rows'][$id][$column]) && !empty($data[$table]['rows'][$id][$column]) ? "background-image:url('".$data[$table]['rows'][$id][$column]."')" : "background-image:url('https://placeholdit.imgix.net/~text?txtsize=29&bg=eeeeee&txtclr=000000&txt=Image&w=196&h=196&txttrack=0')"); ?>;"
        ></div>
    </span>
    <div id="image-buttons" class="col-md-1">
        <span id="upload-button" class='size  btn-file ' title="Upload a new image"><span class="glyphicon glyphicon-upload"></span> <input type="file" name="image" preview="preview-<?php echo $column;?>"></span>
        <span class="size " id="gallery-button" data-toggle="modal" data-target="#gallery" title="Choose an image from gallery">
            <span class="glyphicon glyphicon-th-large"></span>
        </span>
        <span id="remove-button" class="size " onclick="removeImage($(this))" title="Remove the current image" preview="preview-<?php echo $column;?>"><span class="glyphicon glyphicon-remove-sign"></span></span>
        <span id="delete-button" class="size " onclick="deleteImage()" title="Delete the current image"><span class="glyphicon glyphicon-trash"></span></span>
    </div>
</div>

<div id="gallery" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-hidden="true" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body image-container">
                <?php foreach ($structure as $dir => $files) : ?>
                    <?php foreach ($files as $file) : ?>
                        <div class="size2 image col-md-1" style="background-image: url('<?php echo $file; ?>')" file="<?php echo $file; ?>" onclick="uploadImage($(this))"></div>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<script>
    var dir = "<?php echo '/'.$imageDir.'/'.$table.'/'; ?>";
</script>
<script src="/js/image.js"></script>
