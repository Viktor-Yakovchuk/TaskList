<?php
include 'autoload.php';
include 'const.php';

set_exception_handler(function($e) {
    $view = new \TaskListApp\JsonView();
    $view->view([
        'status' => 'error',
        'details' => $e->getMessage()
    ]);
});

DBConnection::get(array(
    'host' => '',
    'user' => '',
    'pass'    => '',
    'db'      => '',
    'charset' => ''
), true);

$defaultController = '\\TaskListApp\\Controllers\\TasksController';
$controller = $defaultController;

if (isset($_GET['controller']))
{
    $controller = '\\TaskListApp\\Controllers\\' . $_GET['controller'];

    if (!class_exists($controller) /*&& ...*/)
        throw new \TaskListApp\Exceptions\ControllerNotFoundException(CONTROLLERNOTFOUND);
        //$controller = $defaultController;
}

session_start();

$app = new $controller();
$app->run();
?> 
