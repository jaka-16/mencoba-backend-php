<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('response_success'))
{
    function response_success($message, $data = null){
        $response = [
            "code" => 200,
            "message" => $message
        ];
        if(!empty($data)){
            $response["data"] = $data;
        }
        return $response;
    }
}

if ( ! function_exists('response_error'))
{
    function response_error($code, $message){
        return [
            "code" => $code,
            "message" => $message
        ];
    }
}
