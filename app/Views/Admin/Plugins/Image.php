<script>
    var path = "<?php echo $imageDomain.'/'.$imageDir.'/'.$table.'/'; ?>";
</script>
<div class="row images">
    <div class="size-large col-md-1">
        <div id="preview-<?php echo $column;?>" class="image size-large"
             style="
                <?php echo (!empty($data[$table]['rows'][$id][$column])
                    ? "background-image:url('".$data[$table]['rows'][$id][$column]."')"
                    : "background-image:url('/img/system/image-empty.png')"); ?>;"
                >
        </div>
    </div>
    <div id="image-buttons" class="col-md-1">
        <span id="upload-button" class="size btn-file">
            <span class="glyphicon glyphicon-upload"></span>
            <input type="file" name="image" title="Upload an image" data-preview="<?php echo $column;?>">
        </span>
        <span id="gallery-button" class="size" title="Choose an image from the gallery" data-toggle="modal" data-target="#gallery">
            <span class="glyphicon glyphicon-th-large"></span>
        </span>
        <span id="remove-button" class="size" title="Remove the current image" onclick="removeImage($(this))" data-preview="<?php echo $column;?>">
            <span class="glyphicon glyphicon-remove-sign"></span>
        </span>
        <span id="delete-button" class="size" title="Delete the current image" onclick="deleteImage($(this))">
            <span class="glyphicon glyphicon-trash"></span>
        </span>
    </div>
</div>
<div id="gallery" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" type="button" aria-hidden="true" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body image-container">
                <?php foreach ($structure as $dir => $files) : ?>
                    <?php foreach ($files as $file) : ?>
                        <div class="size-image image col-md-1" style="background-image: url('<?php echo $imageDomain.$file; ?>')" onclick="updateImage($(this))" data-file="<?php echo $imageDomain.$file; ?>" data-preview="<?php echo $column;?>"></div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
