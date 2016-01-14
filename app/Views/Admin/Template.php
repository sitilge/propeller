<!DOCTYPE html>
<html>
    <head>
        <title>Puce</title>
        <meta charset="utf-8">
        <meta name="author" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        
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
                <div class="navbar-collapse collapse" id="navbar">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="<?php echo $router->logout(); ?>">
                                <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
                                <span class="sr-only">Logout</span>
                                Logout
                            </a>
                        </li>
                    </ul>
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