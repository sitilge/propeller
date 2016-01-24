<?php

return [
    'handler' => '', //session driver; array('database', 'file')
    'table' => 'sessions', //session database table; string
    'name' => 'puce', //session cookie name; string
    'expire' => 60 * 60 * 24, //session cookie expire (since now); integer
    'path' => '/', //session cookie path; string
    'domain' => '', //session cookie domain; string
    'secure' => false, //session cookie secure; array(true, false)
    'httponly' => true //session cookie httponly; array(true, false)
];