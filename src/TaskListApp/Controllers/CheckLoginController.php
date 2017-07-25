<?php
namespace TaskListApp\Controllers;

final class CheckLoginController extends Controller
{
    public function run()
    {
        $view = new \TaskListApp\JsonView();
        $view->view([
            'authorized' => (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'])
        ]);
    }
}