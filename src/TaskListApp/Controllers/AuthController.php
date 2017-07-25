<?php
namespace TaskListApp\Controllers;

final class AuthController extends Controller
{
    public function run()
    {
        $loginParameters = filter_input_array(INPUT_POST, [
            'login' => FILTER_UNSAFE_RAW,
            'password' => FILTER_UNSAFE_RAW,
        ]);

        $view = new \TaskListApp\JsonView();
        $response = [
            'status' => AUTHFAILURE,
            'details' => DETAILSAUTHFAILURE
        ];

        if ($loginParameters['login'] === 'admin' && $loginParameters['password'] === '123') {
            $_SESSION['isAdmin'] = true;
            $response['status'] = 'ok';
        }

        $view->view($response);
    }
}