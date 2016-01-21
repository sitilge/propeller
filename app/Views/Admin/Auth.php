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

        <style>
            body {
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #eee;
            }

            .form-auth {
                max-width: 330px;
                padding: 15px;
                margin: 0 auto;
            }
            .form-auth .form-auth-heading,
            .form-auth {
                margin-bottom: 10px;
            }
            .form-auth {
                font-weight: normal;
            }
            .form-auth .form-control {
                position: relative;
                height: auto;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                padding: 10px;
                font-size: 16px;
            }
            .form-auth .form-control:focus {
                z-index: 2;
            }
            .form-auth input[type="email"] {
                margin-bottom: -1px;
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 0;
            }
            .form-auth input[type="password"] {
                margin-bottom: 10px;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <form class="form-auth" action="" method="post">
                <h2 class="form-auth-heading">Puce auth</h2>
                <label class="sr-only" for="email">Email address</label>
                <input id="email" class="form-control" name="email" type="email" autofocus="" required="" placeholder="Email address" />
                <label class="sr-only" for="password">Password</label>
                <input id="password" class="form-control" name="password" type="password" required="" placeholder="Password" />
                <button type="submit" class="btn btn-lg btn-primary btn-block">Sign in</button>
            </form>
        </div>
    </body>
</html>