<link rel="stylesheet" href="<?php echo $url->admin(); ?>css/dist/plugins/text/text.css">
<script src="<?php echo $url->admin(); ?>js/dist/plugins/text/text.js"></script>
<?php $attributes = isset($structure[$table]['columns'][$column]['attributes']) ? implode(' ', array_map(function($key, $value) {return $key.' = "'.$value.'"';}, array_keys($structure[$table]['columns'][$column]['attributes']), array_values($structure[$table]['columns'][$column]['attributes']))) : null; ?>

<div id="<?php echo $column; ?>" class="summernote">
    <?php echo $data; ?>
</div>
<textarea id="summernote-<?php echo $column; ?>" class="hidden" name="<?php echo $column; ?>" <?php echo $attributes; ?>>
    <?php echo $data; ?>
</textarea>