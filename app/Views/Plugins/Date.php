<link rel="stylesheet" href="<?php echo $url->admin(); ?>css/dist/plugins/date/date.css">
<script src="<?php echo $url->admin(); ?>js/dist/plugins/date/date.js"></script>
<?php $attributes = isset($structure[$table]['columns'][$column]['attributes']) ? implode(' ', array_map(function($key, $value) {return $key.' = "'.$value.'"';}, array_keys($structure[$table]['columns'][$column]['attributes']), array_values($structure[$table]['columns'][$column]['attributes']))) : null; ?>
<div class="input-group">
    <input id="<?php echo $column; ?>" class="form-control datetimepicker" name="<?php echo $column; ?>" value="<?php echo htmlentities($data, ENT_QUOTES); ?>" <?php echo $attributes; ?> data-format="YYYY-MM-DD"/>
    <span class="input-group-addon">
        <span class="glyphicon glyphicon-calendar"></span>
    </span>
</div>