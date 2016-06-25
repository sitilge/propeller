<?php

return [
    [
        //TODO - http://restcookbook.com
        //TODO - http://www.restapitutorial.com/lessons/httpmethods.html
        // POST - create, GET - read, PUT - update, DELETE - delete
        ['POST', 'GET', 'PUT', 'DELETE'],
        '/[{table}[/{key}]]',
        [new \Propeller\Controllers\FrontController(), 'init']
    ]
];
