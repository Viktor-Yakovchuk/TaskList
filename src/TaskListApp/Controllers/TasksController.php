<?php
namespace TaskListApp\Controllers;

final class TasksController extends Controller
{
    public function run()
    {
        $taskList = new \TaskListApp\TaskList();
        $view = new \TaskListApp\JsonView();
        $view->view($taskList->getAllTasks());
    }
} 
