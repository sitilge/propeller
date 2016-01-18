<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css" />
<link rel="stylesheet" href="/css/summernote.min.css">

<script src="/js/summernote.min.js"></script>

<form action="" method="post" enctype="multipart/form-data">
    <?php foreach ($data[$table]['columns'] as $columnName => $column) : ?>
        <div class="form-group">
            <label for="<?php echo $columnName; ?>"><?php echo (isset($column['name']) ? $column['name'] : $columnName); ?></label>
            <?php if (!empty($data[$table]['rowsJoin'][$columnName])) : ?>
                <select id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" >
                    <?php foreach ($data[$table]['rowsJoin'][$columnName] as $rowJoinId => $rowJoinValue) : ?>
                        <?php if (isset($data[$table]['rows'][$id][$columnName]) && $rowJoinId == $data[$table]['rows'][$id][$columnName]) : ?>
                            <option value="<?php echo $rowJoinId; ?>" selected><?php echo $rowJoinValue; ?></option>
                        <?php elseif (!empty($column['readonly'])) : ?>
                            <option value="<?php echo $rowJoinId; ?>" disabled><?php echo $rowJoinValue; ?></option>
                        <?php else : ?>
                            <option value="<?php echo $rowJoinId; ?>"><?php echo $rowJoinValue; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php elseif (!empty($column['type'])) : ?>
                <?php if ($column['type'] === 'image') : ?>
                    <input class="form-control" id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : ''); ?>" <?php echo (!empty($column['readonly']) ? 'readonly' : ''); ?> type="hidden"/>
                    <?php echo $data[$table]['plugins'][$columnName]; ?>
                <?php elseif ($column['type'] === 'maxlength') : ?>
                    <textarea id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" class="form-control excerpt" maxlength="255"><?php echo (isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : ''); ?></textarea>
                <?php elseif ($column['type'] === 'editor') : ?>
                    <div id="<?php echo $columnName; ?>" class="summernote"><?php echo (isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : ''); ?></div>
                    <textarea id="summernote-<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" hidden><?php echo (isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : ''); ?></textarea>
                <?php elseif ($column['type'] === 'price') : ?>
                    <div class="input-group">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
                        <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : ''); ?>" <?php echo (!empty($column['readonly']) ? 'readonly' : ''); ?>/>
                        <div class="input-group-addon">.00</div>
                    </div>
                <?php elseif ($column['type'] === 'slug') : ?>
                    <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : ''); ?>" onkeyup="slugify(this);" />
                <?php else : ?>
                    <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : ''); ?>" <?php echo (!empty($column['readonly']) ? 'readonly' : ''); ?>/>
                <?php endif; ?>
            <?php else : ?>
                <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo (isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : ''); ?>" <?php echo (!empty($column['readonly']) ? 'readonly' : ''); ?>/>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-success">Submit</button>
</form>