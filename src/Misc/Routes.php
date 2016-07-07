<?php

$mainRoute = function () {
    return
    [
        //TODO - http://www.restapitutorial.com/lessons/httpmethods.html
        // POST - create, GET - read, PUT - update, DELETE - delete
        ['POST', 'GET', 'PUT', 'DELETE'],
        '/[{table}[/{key}]]',
        [new \Propeller\Controllers\FrontController(
            new \Propeller\Models\PersistenceModel(),
            new \Propeller\Models\OrmModel(),
            new \Propeller\Models\TemplateModel(),
            new \Propeller\Models\UrlModel(),
            new \Propeller\Controllers\MainController(),
            new \Propeller\Views\MainView()
        ), 'init'],
    ];
};

return [
    $mainRoute(),
];
