<?php
require_once 'app/Router.php';
require_once 'app/MySQL.php';
require_once 'app/table/Product.php';
require_once 'app/table/Repo.php';
require_once 'app/table/DVD.php';
require_once 'app/table/Furniture.php';
require_once 'app/table/Book.php';

use app\MySQL;
use app\Router;
use app\table\Repo;

MySQL::setInstance(new MySQL());
Repo::setInstance(new Repo());

http_response_code(Router::route());