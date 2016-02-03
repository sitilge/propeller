<h1 class="page-header">
    <span><?php echo !empty($data[$table]['name']) ? $data[$table]['name'] : $table; ?></span>
    <?php if ($action === 'update') : ?>
        <div class="pull-right">
            <?php if (!empty($data[$table]['remove'])) : ?>
                <a class="remove-row-button btn btn-danger" data-url="<?php echo $url->admin($table, 'remove', $id); ?>" data-id="<?php echo $id; ?>">Delete</a>
            <?php endif; ?>
            <?php if (!empty($data[$table]['create'])) : ?>
                <a class="add-row-button btn btn-success" href="<?php echo $url->admin($table, 'create'); ?>" >Create</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</h1>
<form action="" method="post" enctype="multipart/form-data">
    <?php foreach ($data[$table]['columns'] as $columnName => $column) : ?>
        <?php $value = isset($data[$table]['rows'][$id][$columnName]) ? $data[$table]['rows'][$id][$columnName] : null; ?>
        <?php $disabled = !empty($column['disabled']) ? 'disabled' : null; ?>
        <?php $display = empty($column['view']) ? 'hidden' : null; ?>
        <div class="form-group <?php echo $display; ?>">
            <label for="<?php echo $columnName; ?>"><?php echo (isset($column['name']) ? $column['name'] : $columnName); ?></label>
            <?php if (!empty($data[$table]['rowsJoin'][$columnName])) : ?>
                <select id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]">
                    <option value="" <?php echo $disabled; ?>></option>
                    <?php foreach ($data[$table]['rowsJoin'][$columnName] as $rowJoinId => $rowJoinValue) : ?>
                        <?php if (isset($value) && $rowJoinId == $value) : ?>
                            <option value="<?php echo $rowJoinId; ?>" <?php echo $disabled; ?> selected><?php echo htmlentities($rowJoinValue, ENT_QUOTES); ?></option>
                        <?php else : ?>
                            <option value="<?php echo $rowJoinId; ?>" <?php echo $disabled; ?>><?php echo htmlentities($rowJoinValue, ENT_QUOTES); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php elseif (!empty($column['type'])) : ?>
                <?php if ($column['type'] === 'image') : ?>
                    <input class="form-control" id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo htmlentities($value, ENT_QUOTES); ?>" <?php echo $disabled; ?> type="hidden"/>
                    <?php echo $data[$table]['plugins'][$columnName]; ?>
                <?php elseif ($column['type'] === 'length') : ?>
                    <textarea id="<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" class="form-control excerpt" maxlength="255"><?php echo $value; ?></textarea>
                <?php elseif ($column['type'] === 'text') : ?>
                    <div id="<?php echo $columnName; ?>" class="summernote"><?php echo $value; ?></div>
                    <textarea id="summernote-<?php echo $columnName; ?>" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" hidden><?php echo $value; ?></textarea>
                <?php elseif ($column['type'] === 'price') : ?>
                    <div class="input-group">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-eur" aria-hidden="true"></span></div>
                        <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo htmlentities($value, ENT_QUOTES); ?>" <?php echo $disabled; ?>/>
                    </div>
                <?php elseif ($column['type'] === 'slug') : ?>
                    <input id="<?php echo $columnName; ?>" class="form-control slugify" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo htmlentities($value, ENT_QUOTES); ?>" <?php echo $disabled; ?>/>
                <?php else : ?>
                    <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo htmlentities($value, ENT_QUOTES); ?>" <?php echo $disabled; ?>/>
                <?php endif; ?>
            <?php else : ?>
                <input id="<?php echo $columnName; ?>" class="form-control" name="<?php echo $action; ?>[<?php echo $columnName; ?>]" value="<?php echo htmlentities($value, ENT_QUOTES); ?>" <?php echo $disabled; ?>/>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-primary">Update</button>
</form>