<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css" />
<link rel="stylesheet" href="/css/summernote.min.css">

<script src="/js/summernote.min.js"></script>

<form action="" method="post" enctype="multipart/form-data">
    <?php foreach ($data['columns'] as $columnName => $column) : ?>
        <div class="form-group">
            <label for="<?php echo $columnName; ?>"><?php echo $column['name']; ?></label>
            <?php if (!empty($data['rowsJoin'][$columnName])) : ?>
                <select class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" id="<?php echo $columnName; ?>" <?php echo (!empty($column['readonly']) ? 'disabled' : ''); ?>>
                    <?php foreach ($data['rowsJoin'][$columnName] as $rowJoinId => $rowJoinValue) : ?>
                        <option value="<?php echo $rowJoinId; ?>" <?php echo (isset($row[$columnName]) && $rowJoinId == $row[$columnName]) ? 'selected' : ''; ?>>
                            <?php echo $rowJoinValue; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php elseif (!empty($column['type'])) : ?>
                <?php if ($column['type'] == 'file') : ?>
                    <input class="form-control" id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($row[$columnName]) ? $row[$columnName] : ''); ?>" <?php echo (!empty($column['readonly']) ? 'readonly' : ''); ?>/>
                    <?php echo $data['plugins']['image']; ?>
                <?php elseif ($column['type'] == 'maxlength') : ?>
                    <textarea id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" class="form-control excerpt" maxlength="255"><?php echo (isset($row[$columnName]) ? $row[$columnName] : ''); ?></textarea>
                <?php elseif ($column['type'] == 'editor') : ?>
                    <div class="summernote"><?php echo (isset($row[$columnName]) ? $row[$columnName] : ''); ?></div>
                    <textarea name="<?php echo $action; ?>[<?php echo $columnName; ?>]" id="<?php echo $columnName; ?>" hidden></textarea>
                <?php elseif ($column['type'] == 'price') : ?>
                    <div class="input-group">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                        <input class="form-control" id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($row[$columnName]) ? $row[$columnName] : ''); ?>" <?php echo (!empty($column['readonly']) ? 'readonly' : ''); ?>/>     
                        <div class="input-group-addon">.00</div>
                    </div>
                <?php elseif ($column['type'] == 'slug') : ?>
                    <input class="form-control" id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($row[$columnName]) ? $row[$columnName] : ''); ?>" onchange="slugify(this);" />
                <?php endif; ?>    
            <?php else : ?>
                <input class="form-control" id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($row[$columnName]) ? $row[$columnName] : ''); ?>" <?php echo (!empty($column['readonly']) ? 'readonly' : ''); ?>/>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-success">Submit</button>
</form>