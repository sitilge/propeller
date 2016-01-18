<script src="/js/sortable.js"></script>
<script src="/js/hideseek.js"></script>
<script src="/js/sweetalert.js"></script>

<link rel="stylesheet" href="/css/sweetalert.css">

<h1 class="page-header">
    <span><?php echo !empty($data[$table]['name']) ? $data[$table]['name'] : $table; ?></span>
    <?php if (!empty($data[$table]['insert'])) : ?>
        <a href="<?php echo $router->admin($table, 'add'); ?>" class="btn btn-success pull-right">Add new</a>
    <?php endif; ?>
</h1>
<?php if (!empty($data[$table]['rows'])) : ?>
    <div class="form-group">
        <input type="text" id="search" class="form-control" name="search" placeholder="Search..." data-list=".searchable" autocomplete="off" />
    </div>

    <table class="table table-striped">
        <thead>
            <tr class="head">
                <?php if (!empty($data[$table]['order'])) : ?>
                    <th></th>
                <?php endif; ?>
                <?php foreach ($data[$table]['columns'] as $columnName => $column) : ?>
                    <?php if (empty($column['display']) || (!empty($column['display'])) && $column['display'] != 'edit') : ?>
                        <th><?php echo (!empty($column['name']) ? $column['name'] : $columnName); ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="sortable searchable">
            <?php foreach ($data[$table]['rows'] as $row) : ?>
                <tr data-id="<?php echo $row['id']; ?>">
                    <?php if (!empty($data[$table]['order'])) : ?>
                        <td class='sortable-handle'><div class="glyphicon glyphicon-tasks"></div></td>
                    <?php endif; ?>
                    <?php foreach ($data[$table]['columns'] as $columnName => $column) : ?>
                        <?php if (empty($column['display']) || (!empty($column['display'])) && $column['display'] != 'edit') : ?>
                            <?php if (!empty($data[$table]['rowsJoin'][$columnName])) : ?>
                                <td><?php echo (!empty($data[$table]['rowsJoin'][$columnName][$row[$columnName]]) ? $data[$table]['rowsJoin'][$columnName][$row[$columnName]] : $row[$columnName]); ?></td>
                            <?php else : ?>
                                <td><?php echo $row[$columnName]; ?></td>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>           
                    <td class="text-right">
                        <a class="edit-row-button glyphicon glyphicon-pencil" href="<?php echo $router->admin($table, 'edit', $row['id']); ?>"></a>
                        <a class="remove-row-button glyphicon glyphicon-remove" link="<?php echo $router->admin($table, 'remove', $row['id']); ?>"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="alert alert-danger hidden no-results">No results found</div>
<?php else : ?>
    <div class="alert alert-danger">No results found</div>
<?php endif; ?>