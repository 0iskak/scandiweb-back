<?php

namespace app;

use mysqli;

class MySQL extends mysqli
{
    private static MySQL $instance;

    public function __construct()
    {
        parent::__construct('localhost', 'scandiweb',
            'scandiweb', 'scandiweb');
    }


    public static function getInstance(): MySQL
    {
        return self::$instance;
    }

    public static function setInstance(MySQL $instance): void
    {
        self::$instance = $instance;
    }
}