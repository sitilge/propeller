# Abimo

Abimo is a lightweight HTTP PHP framework / boilerplate. The reason behind this was to reinvent the wheel (or at least try to). Why?

- To learn, to grow - there are a lot modern web frameworks out there, what thrills me more in general is the conceptual ideas, low level architecture, web internals that they provide.

- To keep my hands dirty and mind busy - you enjoyed playing in the sandbox, didn't you? The same applies here - use, modify, destroy. Have fun!

- To refactor - older projects/applications required to be updated using decent web practices.

# Demo

See a simple landing site here: http://sitilge.id.lv

# Features

- Painless configuration
- DBAL (PDO)
- HTTP request & response handling
- Custom session handling (file, database, memcached)
- Templating
- Error & exception handling

# Installation

Install [composer] on your system and run

```sh
composer create-project sitilge/abimo=dev-master
```

# Webservers

## Apache

Abimo comes with predefined ```public/.htaccess``` file to provide user-friendly URL's by avoiding the ```index.php``` URL postfix.

```
RewriteEngine On
RewriteRule ^(.*)/$ /$1 [L,R=301]
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

## Nginx

If you are using Nginx (our preferred choice) the following directive will provide the functionality

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

# Configuration

All configuration files are located under ```app/Config``` directory. Each option is documented, available values are given in comments right next to the option. You can also edit freely the existing files or add new ones to fit your project needs.

# Basic usage

Let us suppose you would like to make a simple landing site for your favourite quote in the world "Hello, world!". The ```index.php``` file initializes the ```\App\Misc\Router``` object that is responsible for the whole routing process. The only preset package that Abimo uses by default (and yes, you can remove it) is a  superb router [FastRoute]. Firstly, create some new routes

```
$collector->addRoute('GET', '/[{greeting}]', [new \App\Controllers\MainController(), 'main']);
```

Create the controller ```\App\Controllers\MainController``` and the method ```main```

```
<?php

namespace App\Controllers;

class MainController
{
    public function main($greeting = null)
    {
        $factory = new \Abimo\Factory();

        $template = $factory->template();

        //$greetingModel = new \App\Models\GreetingModel();

        echo $template
            ->file('Posts/PostSingle')
            ->set('text', 'Hello, world!')
            ->set('greeting', $greeting)
            ->render();
    }
}
```

Breaking it down, every class you create should be under ```App``` namespace since it automatically loaded using composer autoloader. ```\Abimo\Factory``` is a class that should be used to factory a new instance of the required class, an object. If you feel brave enough, just override them by passing custom instances (those under ```Abimo``` namespace) to the factory method. Initialize new models (if needed) under ```App/Models``` to keep the code clean. Actually, we initialized a new ```\Abimo\Template``` object there, set the file to render to ```Greeting.php``` under ```App/Views/Main``` and passed ```text``` and ```greeting``` variables to the template where the former one holds the slug. Create the template file

```
<?php echo "$text $greeting"; ?>
```

Assume that your domain is ```http://mydomain.lv```, if we make a request to ```http://mydomain.lv/Smile!``` the HTML response body would be

```
Hello, world! Smile!
```

Voila! You made your first landing site.

[composer]: <https://getcomposer.org/download/>
[FastRoute]: <https://github.com/nikic/FastRoute>