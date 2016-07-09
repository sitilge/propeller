<link rel="stylesheet" href="<?php echo $url->main(); ?>css/dist/table.css">
<script src="<?php echo $url->main(); ?>js/dist/table.js"></script>
<h1 class="page-header">
    <span><?php echo $query->getTableMap()->getPhpName(); ?></span>
    <?php if (!empty($query->getPropellerTableCreate())) : ?>
        <div class="pull-right">
            <a class="create-row-button btn btn-success" data-url="<?php echo $url->main($query->getTableMap()->getName()); ?>">Create</a>
        </div>
    <?php endif; ?>
</h1>
<?php if (!empty($rows->count())) : ?>
    <div class="form-group">
        <input id="search" class="form-control" name="search" placeholder="Search..." autocomplete="off" data-list=".searchable"/>
    </div>
    <table class="table table-striped">
        <thead>
            <tr class="head">
                <?php $columns = $query->getTableMap()->getColumns(); ?>
                <?php foreach ($columns as $column) : ?>
                    <?php if (empty($query->getPropellerTableColumnShow($column->getName()))) : ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <th><?php echo $column->getPhpName(); ?></th>
                <?php endforeach; ?>
                <?php if (!empty($query->getPropellerTableUpdate()) || !empty($query->getPropellerTableDelete())) : ?>
                    <th class="text-right">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="searchable">
            <?php foreach ($rows as $index => $row) : ?>
                <?php $key = is_array($row->getPrimaryKey()) ? implode('-', $row->getPrimaryKey()) : $row->getPrimaryKey(); ?>
                <tr data-id="<?php echo $key; ?>">
                    <?php foreach ($columns as $column) : ?>
                        <?php if (empty($query->getPropellerTableColumnShow($column->getName()))) : ?>
                            <?php continue; ?>
                        <?php endif; ?>
                        <td class="update-row" data-url="<?php echo $url->main($query->getTableMap()->getName(), $key); ?>">
                            <?php if ($row->getByName($column->getPhpName()) instanceof DateTime) : ?>
                                <?php echo htmlentities($row->getByName($column->getPhpName())->format('Y-m-d H:i:s'), ENT_QUOTES); ?>
                            <?php else : ?>
                                <?php echo htmlentities($row->getByName($column->getPhpName()), ENT_QUOTES); ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <?php if (!empty($query->getPropellerTableUpdate()) || !empty($query->getPropellerTableDelete())) : ?>
                        <td class="text-right">
                            <?php if (!empty($query->getPropellerTableUpdate())) : ?>
                                <a class="update-row-button glyphicon glyphicon-pencil" data-url="<?php echo $url->main($query->getTableMap()->getName(), $key); ?>"></a>
                            <?php endif; ?>
                            <?php if (!empty($query->getPropellerTableDelete())) : ?>
                                <a class="delete-row-button glyphicon glyphicon-remove" data-url="<?php echo $url->main($query->getTableMap()->getName(), $key); ?>" data-id="<?php echo $key; ?>"></a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="alert alert-danger hidden no-results">No results found</div>
<?php else : ?>
    <div class="alert alert-danger">No results found</div>
<?php endif; ?>