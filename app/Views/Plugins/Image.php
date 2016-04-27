<link rel="stylesheet" href="<?php echo $url->admin(); ?>css/dist/plugins/image/image.css">
<script src="<?php echo $url->admin(); ?>js/dist/plugins/image/image.js"></script>
<script>
    var path = "<?php echo $imageDomain.$baseUrl.'/'.$imageDir.'/'.$table.'/'; ?>";
</script>
<?php $attributes = isset($structure[$table]['columns'][$column]['attributes']) ? implode(' ', array_map(function($key, $value) {return $key.' = "'.$value.'"';}, array_keys($structure[$table]['columns'][$column]['attributes']), array_values($structure[$table]['columns'][$column]['attributes']))) : null; ?>
<input id="<?php echo $column; ?>" class="form-control hidden" name="<?php echo $column; ?>" value="<?php echo htmlentities($data, ENT_QUOTES); ?>" <?php echo $attributes; ?>/>
<div class="row images">
    <div class="size-large col-md-1">
        <div id="preview-<?php echo $column;?>" class="image size-large"
             style="
                <?php echo (!empty($data)
                    ? "background-image:url('".$data."')"
                    : "background-image:url('".$baseUrl."/img/system/image-empty.png')"); ?>;"
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
                <?php foreach ($directories as $directory => $files) : ?>
                    <?php foreach ($files as $file) : ?>
                        <div class="size-image image col-md-1" style="background-image: url('<?php echo $imageDomain.$baseUrl.$file; ?>')" onclick="updateImage($(this))" data-file="<?php echo $imageDomain.$baseUrl.$file; ?>" data-preview="<?php echo $column;?>"></div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>