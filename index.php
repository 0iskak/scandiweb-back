<?php
require_once 'app/Router.php';
require_once 'app/MySQL.php';
require_once 'table/Repo.php';
require_once 'table/Product.php';

use app\MySQL;
use app\Router;

MySQL::setInstance(new MySQL());

Router::route();