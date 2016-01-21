<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" lang="en">
    <head>
        <title>Puce</title>
        <meta charset="utf-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta content="Puce" property="og:title">
        <meta content="" property="og:image">
        <meta content="" property="og:description">
        <meta content="" property="og:url">
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/all.css">
        
        <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
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
                    <a href="<?php echo $router->admin(); ?>" class="navbar-brand">Puce</a>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <?php foreach ($menu as $table => $item) : ?>
                        <li class="<?php echo ($segment === $table ? "active" : ""); ?>"><a href="<?php echo $router->admin($table); ?>"><?php echo $item['name']; ?></a></li>
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