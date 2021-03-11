<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;

class UsersController extends Controller
{
    public $user_object;

    public function __construct() {
        $this->user_object = new user();
    }

    public function signup() {
        header("Content-Type: application/json");

        $result = [];

        if (isset($_POST['user_name']) && isset($_POST['user_password']))
        {
            $sign_up_data = [
                "user_name" => sanitize($_POST['user_name']),
                "user_password" => sanitize($_POST['user_password'])
            ]; 
            $result_object = $this->user_object->signup($sign_up_data);

            $result = $result_object->alert;

            header("HTTP/1.1 {$result_object->alert['http_code']}", true, $result_object->alert['http_code']);
        }
        else
        {
            header('HTTP/1.1 400 Bad request', true, 400);
            $result = [
                "error" => "user_name & user_password are required"
            ];
        }
        
        
        echo json_encode($result);

        die();
    }

    public function login() {
        header("Content-Type: application/json");

        $result = [];
        
        if(isset($_POST['user_name']) && isset($_POST['user_password']))
        {
            $login_data = [
                "user_name" => \sanitize($_POST['user_name']),
                "user_password" => \sanitize($_POST['user_password'])
            ];
            $result_object = $this->user_object->login($login_data);

            $result = $result_object->result;
        }
        else{
            $result = [
                "result" => [],
                "http_code" => 400
            ];
        }

        \display_result($result);

        die();
    }
}
