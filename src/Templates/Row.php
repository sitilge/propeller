<link rel="stylesheet" href="<?php echo $url->main(); ?>css/dist/row.css">
<script src="<?php echo $url->main(); ?>js/dist/row.js"></script>
<h1 class="page-header">
    <span><?php echo $map->getPhpName(); ?></span>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
        <div class="pull-right">
            <a class="create-row-button btn btn-success" data-url="<?php echo $url->main($map->getName(), $key); ?>" data-id="<?php echo $key; ?>">Create</a>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET') : ?>
        <div class="pull-right">
            <?php if (!empty($query->getPropellerTableDelete())) : ?>
                <a class="delete-row-button btn btn-danger" data-url="<?php echo $url->main($map->getName(), $key); ?>" data-id="<?php echo $key; ?>">Delete</a>
            <?php endif; ?>
            <?php if (!empty($query->getPropellerTableUpdate())) : ?>
                <a class="update-row-button btn btn-primary" data-url="<?php echo $url->main($map->getName(), $key); ?>" data-id="<?php echo $key; ?>">Update</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</h1>
<form id="form" action="" method="post" enctype="multipart/form-data">
    <?php foreach ($columns as $column) : ?>
        <?php $attributes = ''; ?>
        <?php if (!empty($properties = $query->getPropellerRowColumnAttributes($column->getName()))) : ?>
            <?php foreach ($properties as $attribute => $value) : ?>
                <?php $attributes .= $attribute.' = "'.$value.'"'.' '; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="form-group">
            <label for="<?php echo $column->getName(); ?>"><?php echo $column->getPhpName(); ?></label>
            <?php if (!empty($row)) : ?>
                <?php if ($row->getByName($column->getPhpName()) instanceof DateTime) : ?>
                    <input id="<?php echo $column->getName(); ?>" class="form-control" name="<?php echo $column->getName(); ?>" value="<?php echo htmlentities($row->getByName($column->getPhpName())->format('Y-m-d H:i:s'), ENT_QUOTES); ?>" <?php echo $attributes; ?>/>
                <?php else : ?>
                    <input id="<?php echo $column->getName(); ?>" class="form-control" name="<?php echo $column->getName(); ?>" value="<?php echo htmlentities($row->getByName($column->getPhpName()), ENT_QUOTES); ?>" <?php echo $attributes; ?>/>
                <?php endif; ?>
            <?php else : ?>
                <input id="<?php echo $column->getName(); ?>" class="form-control" name="<?php echo $column->getName(); ?>" <?php echo $attributes; ?>/>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</form>