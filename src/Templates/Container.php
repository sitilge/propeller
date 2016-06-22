<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" lang="en">
    <head>
        <title>Propeller</title>
        <meta charset="utf-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta content="Propeller" property="og:title">
        <meta content="" property="og:image">
        <meta content="" property="og:description">
        <meta content="https://github.com/sitilge/propeller" property="og:url">
        <link rel="stylesheet" href="<?php echo $url->main(); ?>css/dist/template.css">
        <script src="<?php echo $url->main(); ?>js/dist/template.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="glyphicon glyphicon-menu-hamburger"></span>
                    </button>
                    <a href="<?php echo $url->main(); ?>" class="navbar-brand">Propeller</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <?php foreach ($tables as $name => $phpName) : ?>
                            <li class="<?php echo $segment === $name ? 'active' : ''; ?>"><a href="<?php echo $url->main($name); ?>"><?php echo $phpName; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <?php foreach ($tables as $name => $phpName) : ?>
                        <li class="<?php echo $segment === $name ? 'active' : ''; ?>"><a href="<?php echo $url->main($name); ?>"><?php echo $phpName; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    </body>
</html>