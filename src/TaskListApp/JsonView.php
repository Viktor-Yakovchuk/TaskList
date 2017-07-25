<?php
namespace TaskListApp;

final class JsonView
{
    public function view($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data);
    }
}