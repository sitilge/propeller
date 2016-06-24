<link rel="stylesheet" href="<?php echo $url->main(); ?>css/dist/table.css">
<script src="<?php echo $url->main(); ?>js/dist/table.js"></script>
<h1 class="page-header">
    <span><?php echo $map->getPhpName(); ?></span>
    <?php if (!empty($query->getPropellerTableCreate())) : ?>
        <div class="pull-right">
            <a class="create-row-button btn btn-success" data-url="<?php echo $url->main($map->getName()); ?>">Create</a>
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
                <?php foreach ($columns as $column) : ?>
                    <?php if (!empty($query->getPropellerTableColumnsShow($column->getName()))) : ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <th><?php echo $column->getPhpName(); ?></th>
                <?php endforeach; ?>
                <?php if (!empty($query->tableUpdate) || !empty($query->tableDelete)) : ?>
                    <th class="text-right">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="searchable">
            <?php foreach ($rows as $index => $row) : ?>
                <tr data-id="<?php echo $keys[$index]; ?>">
                    <?php foreach ($columns as $column) : ?>
                        <?php if (!empty($query->getPropellerTableColumnsShow($column->getName()))) : ?>
                            <?php continue; ?>
                        <?php endif; ?>
                        <td class="update-row" data-url="<?php echo $url->main($map->getName(), $keys[$index]); ?>">
                            <?php if ($row->getByName($column->getPhpName()) instanceof DateTime) : ?>
                                <?php echo htmlentities($row->getByName($column->getPhpName())->format('Y-m-d H:i:s'), ENT_QUOTES); ?>
                            <?php else : ?>
                                <?php echo htmlentities($row->getByName($column->getPhpName()), ENT_QUOTES); ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <?php if (!empty($query->tableUpdate) || !empty($query->tableDelete)) : ?>
                        <td class="text-right">
                            <?php if (!empty($query->tableUpdate)) : ?>
                                <a class="update-row-button glyphicon glyphicon-pencil" data-url="<?php echo $url->main($map->getName(), $keys[$index]); ?>"></a>
                            <?php endif; ?>
                            <?php if (!empty($query->tableDelete)) : ?>
                                <a class="delete-row-button glyphicon glyphicon-remove" data-url="<?php echo $url->main($map->getName(), $keys[$index]); ?>" data-id="<?php echo $keys[$index]; ?>"></a>
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