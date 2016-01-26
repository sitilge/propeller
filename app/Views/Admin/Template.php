<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" lang="en">
    <head>
        <title>curdle</title>
        <meta charset="utf-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta content="curdle" property="og:title">
        <meta content="" property="og:image">
        <meta content="A simple database administration tool for simple CRUD operations." property="og:description">
        <meta content="https://github.com/sitilge/curdle" property="og:url">
        <link rel="stylesheet" href="/css/all.css">
        <script src="/js/all.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?php echo $router->admin(); ?>" class="navbar-brand">curdle</a>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <?php foreach ($menu as $table => $item) : ?>
                        <li class="<?php echo ($segment === $table ? "active" : ""); ?>"><a href="<?php echo $router->admin($table); ?>"><?php echo !empty($item['name']) ? $item['name'] : $table; ?></a></li>
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