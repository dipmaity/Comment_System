<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');


use Slim\Factory\AppFactory;


require __DIR__ . '/../vendor/autoload.php';
require __DIR__.'/../src/config/db.php';

$app = AppFactory::create();



require __DIR__.'/../src/routes/comments.php';
require __DIR__.'/../src/routes/blogs.php';
require __DIR__.'/../src/routes/users.php';
