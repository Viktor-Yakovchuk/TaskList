<?php
namespace TaskListApp\Controllers;

final class EditTaskController extends Controller
{
    public function run()
    {
        if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin'])
            throw new \TaskListApp\Exceptions\NotAuthorizedException(NOTAUTHORIZED);

        $taskParameters = filter_input_array(INPUT_POST, [
            'text' => FILTER_UNSAFE_RAW,
            'status' => FILTER_UNSAFE_RAW,
        ]);

        if ($taskParameters['status'] === 'true')
            $taskParameters['status'] = 1;
        else
            $taskParameters['status'] = 0;

        $view = new \TaskListApp\JsonView();
        $response = [];

        try {
            $taskList = new \TaskListApp\TaskList();
            $task = $taskList->getTask(filter_input(INPUT_POST, 'id', FILTER_UNSAFE_RAW));
            $task->fromArray($taskParameters);
            $task->commit();

            $response = [
                'status' => 'ok',
                //'debug' => \DBConnection::get()->getStats()
            ];
        } catch(\Exception $e) {
            $response = [
                'status' => 'error',
                'details' => $e->getMessage()
            ];
        } finally {
            $view->view($response);
        }
    }
}