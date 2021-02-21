<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    use HasFactory;

    public function signup($sign_up_data){
        $result_object = new \empty_class();
        $result_object->alert = [
            "alerts" => [],
            "code" => "server_error",
            "http_code" => 500
        ];
        $sign_up = true;

        $sign_up_data = [
            "user_name" => redundant_remover([
                "value" => sis($sign_up_data['user_name']),
                "type" => "string",
                "min_length" => 5
            ]),
            "user_password" => redundant_remover([
                "value" => sis($sign_up_data['user_password']),
                "type" => "string",
                "min_length" => 8
            ])
        ];

        if (strlen($sign_up_data['user_name']) < 1 )
        {
            $result_object->alert['alerts'][] = [
                "description" => "user_name should be at least 5 characters",
            ];
            $result_object->alert['http_code'] = 400;
            $result_object->alert['code'] = "bad_user_name";

            $sign_up = false;
        }

        if (strlen($sign_up_data['user_password']) < 1)
        {
            $result_object->alert['alerts'][] = [
                "description" => "user_password '{$sign_up_data['user_password']}' should be at least 8 characters",
            ];
            $result_object->alert['http_code'] = 400;
            $result_object->alert['code'] = "bad_user_password";
            
            $sign_up = false;
        }

        if ($sign_up && (!$this->is_user_name_taken($sign_up_data['user_name'])) )
        {
            $sign_up_data['user_password'] = encryptthis($sign_up_data['user_password']);

            $send = mysqli_query(\connect(), "INSERT INTO `users`(`name`,`password`) VALUES('{$sign_up_data['user_name']}','{$sign_up_data['user_password']}')");

            $result_object->alert['alerts'][] = [
                "description" => "user successfully added",
            ];
            $result_object->alert['http_code'] = 200;
            $result_object->alert['code'] = "success";
        }
        else if ($sign_up)
        {
            $result_object->alert['alerts'][] = [
                "description" => "this user name is already in use",
            ];
            $result_object->alert['http_code'] = 400;
            $result_object->alert['code'] = "user_name_taken";
        }

        return $result_object;
    }

    public function is_user_name_taken($user_name){
        $result = false;

        $select = mysqli_query(\connect(),"SELECT * FROM `users` WHERE `name` = '$user_name'");
        $number = mysqli_num_rows($select);

        if ($number > 0)
        {
            $result = true;
        }

        return $result;
    }

}
