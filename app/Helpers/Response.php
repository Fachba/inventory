<?php

namespace App\Helpers;

class Response
{
    public static function set($message = 'OK', $data = [], $success = true)
    {
        $return['data'] = $data;
        // $return['total'] = is_array($data) ? count($data) : (is_object($data) ? (!empty(get_object_vars($data)) ? 1 : 0) : 1);
        $return['success'] = $success;
        $return['message'] = $message;

        return $return;
    }

    public static function setError($error)
    {
        $return['success'] = false;
        $return['error'] = $error->getMessage();

        return $return;
    }
}
