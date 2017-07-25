<?php
namespace TaskListApp\Controllers;

final class AddTaskController extends Controller
{
    public function run()
    {
        $taskParameters = filter_input_array(INPUT_POST, [
            'username' => FILTER_UNSAFE_RAW,
            'email' => FILTER_UNSAFE_RAW,
            'text' => FILTER_UNSAFE_RAW,
            //'picture' => FILTER_UNSAFE_RAW,
        ]);

        //$taskParameters['picture'] = '';

        $view = new \TaskListApp\JsonView();
        $response = [];

        try {
            if (!isset($_FILES['picture']) || $_FILES['picture']['error'] != UPLOAD_ERR_OK)
                throw new \TaskListApp\Exceptions\PictureUploadFailException(PICTUREUPLOADFAIL);

            $tmpName = APPDIR . '/uploads/' . md5(date(DATE_ATOM) . $_FILES['picture']['tmp_name']);
            \TaskListApp\Functions::createThumbnail($_FILES['picture']['tmp_name'], $tmpName, MAXIMAGEHIEGHT, MAXIMAGEWIDTH);
            move_uploaded_file($_FILES['picture']['tmp_name'], TRASH);

            $task = new \TaskListApp\Task($taskParameters);
            $taskList = new \TaskListApp\TaskList();
            $taskList->addTask($task);

            $pictureFilename = APPDIR . '/uploads/' . $task->getId() . '.jpg';
            rename($tmpName, $pictureFilename);

            $response = [
                'status' => 'ok',
                'id' => $task->getId()
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