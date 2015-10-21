<?php

return array(
    'driver' => 'database', //session driver; array('database', 'memcached', 'redis')
    'table' => 'sessions', //session database table; string
    'expire' => 600 //session expire time starting from now; integer
);