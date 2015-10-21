<script src="/js/sortable.js"></script>
<script src="/js/hideseek.js"></script>

<h1 class="page-header">
    <span><?php echo $data['name']; ?></span>
    <?php if (!empty($data['insert'])) : ?>
        <a href="<?php echo $router->url('admin', array($table, 'add', '')); ?>" class="btn btn-success pull-right">Add new</a>
    <?php endif; ?>
</h1>
<?php if (!empty($data['rows'])) : ?>
    <div class="form-group">
        <input type="text" id="search" class="form-control" name="search" placeholder="Search..." data-list=".searchable" autocomplete="off" />
    </div>

    <table class="table table-striped">
        <thead>
            <tr class="head">
                <?php if (!empty($data['sort'])) : ?>
                    <th></th>
                <?php endif; ?>
                <?php foreach ($data['columns'] as $columnName => $column) : ?>
                    <?php if (empty($column['display']) || (!empty($column['display'])) && $column['display'] != 'edit') : ?>
                        <th><?php echo (!empty($column['name']) ? $column['name'] : $columnName); ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="sortable searchable">
            <?php foreach ($data['rows'] as $row) : ?>
                <tr class='' data-id="<?php echo $row['id']; ?>">
                    <?php if (!empty($data['sort'])) : ?>
                        <td class='sortable-handle'><div class="glyphicon glyphicon-tasks"></div></td>
                    <?php endif; ?> 
                    <?php foreach ($data['columns'] as $columnName => $column) : ?>
                        <?php if (empty($column['display']) || (!empty($column['display'])) && $column['display'] != 'edit') : ?>
                            <?php if (!empty($data['rowsJoin'][$columnName])) : ?>
                                <td><?php echo (!empty($data['rowsJoin'][$columnName][$row[$columnName]]) ? $data['rowsJoin'][$columnName][$row[$columnName]] : $row[$columnName]); ?></td>
                            <?php else : ?>
                                <td><?php echo $row[$columnName]; ?></td>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>           
                    <td class="text-right">
                        <a href="<?php echo $router->url('admin', array('table' => $table, 'action' => 'edit', 'row_id' => $row['id'])); ?>" class="glyphicon glyphicon-pencil"></a>
                        <a href="<?php echo $router->url('admin', array('table' => $table, 'action' => 'remove', 'row_id' => $row['id'])); ?>" class="glyphicon glyphicon-remove"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="alert alert-danger hidden no-results">No results found</div>
<?php else : ?>
    <div class="alert alert-danger">No results found</div>
<?php endif; ?>