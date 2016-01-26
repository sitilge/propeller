<h1 class="page-header">
    <span><?php echo !empty($data[$table]['name']) ? $data[$table]['name'] : $table; ?></span>
    <?php if (!empty($data[$table]['insert'])) : ?>
        <div class="pull-right">
            <a class="add-row-button btn btn-success" href="<?php echo $router->admin($table, 'add'); ?>">Create</a>
        </div>
    <?php endif; ?>
</h1>
<?php if (!empty($data[$table]['rows'])) : ?>
    <div class="form-group">
        <input id="search" class="form-control" name="search" placeholder="Search..." data-list=".searchable" autocomplete="off" />
    </div>
    <table class="table table-striped">
        <thead>
            <tr class="head">
                <?php if (!empty($data[$table]['order'])) : ?>
                    <th></th>
                <?php endif; ?>
                <?php foreach ($data[$table]['columns'] as $columnName => $column) : ?>
                    <?php if (!empty($column['display']) && $column['display'] !== 'edit') : ?>
                        <th><?php echo (!empty($column['name']) ? $column['name'] : $columnName); ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="sortable searchable">
            <?php foreach ($data[$table]['rows'] as $row) : ?>
                <tr data-id="<?php echo $row[$data[$table]['key']]; ?>">
                    <?php if (!empty($data[$table]['order'])) : ?>
                        <td class='sortable-handle'><div class="glyphicon glyphicon-tasks"></div></td>
                    <?php endif; ?>
                    <?php foreach ($data[$table]['columns'] as $columnName => $column) : ?>
                        <?php if (!empty($column['display']) && $column['display'] !== 'edit') : ?>
                            <td class="edit-row" data-url="<?php echo $router->admin($table, 'edit', $row[$data[$table]['key']]); ?>">
                                <?php if (!empty($data[$table]['rowsJoin'][$columnName])) : ?>
                                    <?php echo (!empty($data[$table]['rowsJoin'][$columnName][$row[$columnName]]) ? $data[$table]['rowsJoin'][$columnName][$row[$columnName]] : $row[$columnName]); ?>
                                <?php else : ?>
                                    <?php echo $row[$columnName]; ?>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td class="text-right">
                        <a class="edit-row-button glyphicon glyphicon-pencil" href="<?php echo $router->admin($table, 'edit', $row[$data[$table]['key']]); ?>"></a>
                        <a class="remove-row-button glyphicon glyphicon-remove" data-url="<?php echo $router->admin($table, 'remove', $row[$data[$table]['key']]); ?>" data-id="<?php echo $row[$data[$table]['key']]; ?>"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="alert alert-danger hidden no-results">No results found</div>
<?php else : ?>
    <div class="alert alert-danger">No results found</div>
<?php endif; ?>