<link rel="stylesheet" href="/css/font-awesome.css" />
<link rel="stylesheet" href="/css/summernote.min.css">

<script src="/js/summernote.min.js"></script>

<form action="" method="post" enctype="multipart/form-data">
    <?php foreach ($data[$table]['columns'] as $columnName => $column) : ?>
        <?php $value = isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : null; ?>
        <?php $disabled = !empty($column['disabled']) ? 'disabled' : ''; ?>
        <div class="form-group">
            <label for="<?php echo $columnName; ?>"><?php echo (isset($column['name']) ? $column['name'] : $columnName); ?></label>
            <?php if (!empty($data[$table]['rowsJoin'][$columnName])) : ?>
                <select id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" >
                    <option value="" <?php echo $disabled; ?>></option>
                    <?php foreach ($data[$table]['rowsJoin'][$columnName] as $rowJoinId => $rowJoinValue) : ?>
                        <?php if (isset($value) && $rowJoinId == $value) : ?>
                            <option value="<?php echo $rowJoinId; ?>" <?php echo $disabled; ?> selected><?php echo $rowJoinValue; ?></option>
                        <?php else : ?>
                            <option value="<?php echo $rowJoinId; ?>" <?php echo $disabled; ?>><?php echo $rowJoinValue; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php elseif (!empty($column['type'])) : ?>
                <?php if ($column['type'] === 'image') : ?>
                    <input class="form-control" id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo $value; ?>" <?php echo $disabled; ?> type="hidden"/>
                    <?php echo $data[$table]['plugins'][$columnName]; ?>
                <?php elseif ($column['type'] === 'length') : ?>
                    <textarea id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" class="form-control excerpt" maxlength="255"><?php echo $value; ?></textarea>
                <?php elseif ($column['type'] === 'text') : ?>
                    <div id="<?php echo $columnName; ?>" class="summernote"><?php echo $value; ?></div>
                    <textarea id="summernote-<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" hidden><?php echo $value; ?></textarea>
                <?php elseif ($column['type'] === 'price') : ?>
                    <div class="input-group">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-eur" aria-hidden="true"></span></div>
                        <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo $value; ?>" <?php echo $disabled; ?>/>
                    </div>
                <?php elseif ($column['type'] === 'slug') : ?>
                    <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo $value; ?>" <?php echo $disabled; ?> onkeyup="slugify(this);" />
                <?php else : ?>
                    <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo $value; ?>" <?php echo $disabled; ?>/>
                <?php endif; ?>
            <?php else : ?>
                <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo $value; ?>" <?php echo $disabled; ?>/>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-success">Submit</button>
</form>