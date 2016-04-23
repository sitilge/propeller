<?php

return [
    'expire' => time() + 60 * 60 * 24, //cookie expire (since epoch); integer
    'path' => '/', //cookie path; string
    'domain' => '', //cookie domain; string
    'secure' => false, //cookie secure; array(true, false)
    'httponly' => true //cookie httponly; array(true, false)
];
