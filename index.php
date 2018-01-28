<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Model;
use Phalcon\Di;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Application;
use Phalcon\Http\Response;
use Phalcon\Mvc\Micro\Collection as MicroCol;

// Loader for models, controllers, etc.
$loader = new Loader();

$loader->registerDirs(
    array(
        __DIR__ . '/models/',
        __DIR__ . '/controllers/',
        __DIR__ . '/controllers/exceptions/'
    )
)->register();

// DependencyInjector instantiation
$di = new \Phalcon\DI\FactoryDefault();

// DB config
$di->set('db', function () {
    return new PdoMysql(
        array(
            "host" => "localhost",
            "username" => "root",
            "password" => "",
            "dbname" => "linkme",
            "options" => array( // utf8
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            )
        )
    );
});


$app = new Micro($di);

$users = new MicroCol();

$link = new MicroCol();
$token = new MicroCol();
$logs = new MicroCol();

$users->setHandler(new UserController());
$link->setHandler(new LinkController());
$token->setHandler(new TokenController());
$logs->setHandler(new LogController());

// Setting Route prefixes
$users->setPrefix('/user');
$link->setPrefix('/link');
$token->setPrefix('/token');
$logs->setPrefix('/logs');

// User routes
$users->post('/register', 'register');
$users->get('/{id}', 'getUserById');
$users->post('/login', 'login');
$users->post('/loginToken', 'loginWithToken');

// Link routes
$link->post('/create', 'create');
$link->get('/user/{id}', 'getAllLinksForUID');
$link->post('/find', 'getLinksByName');
$link->post('/delete', 'deleteLink');
$link->post('/edit', 'editLink');
// token routes
$token->post('/create', 'create');

//logs routes
$logs->get('/{id}', 'getLogsByUID');

// Mounting controllers
$app->mount($users);
$app->mount($link);
$app->mount($token);
$app->mount($logs);

$app->handle();
