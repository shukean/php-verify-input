<?php

//https://github.com/shukean/php-verify-input

namespace Verify;

define('INPUT_GET', 1);
define('INPUT_POST', 2);
define('INPUT_COOKIE', 4);
define('INPUT_YAF_PARAMS', 8);
define('INPUT_REQUEST', 3);

class Get{

    public static function value($key, $vf_func, $need=true, $invalid_msg=null, array $args=[], $type=INPUT_REQUEST){
        $value = null;
        while ($type && $value === null) {
            if ($type & INPUT_GET){
                $value = array_key_exists($key, $_GET) ? $_GET[$key] : $value;
                $type ^= INPUT_GET;
                continue;
            }
            if ($type & INPUT_POST){
                $value = array_key_exists($key, $_POST) ? $_GET[$key] : $value;
                $type ^= INPUT_POST;
                continue;
            }
            if ($type & INPUT_YAF_PARAMS){
                $value = \Yaf\Application::app()->getDispatcher()->getRequest()->getParam($key, null);
                $type ^= INPUT_YAF_PARAMS;
                continue;
            }
            if ($type & INPUT_COOKIE){
                $value = array_key_exists($key, $_COOKIE) ? $_COOKIE[$key] : $value;
                $type ^= INPUT_COOKIE;
                continue;
            }
            break;
        }
        if ($need && $value === null){
            throw new \Exception($invalid_msg ? $invalid_msg : 'Invalid arguments'.$key);
        }
        if (!$need && $value === null){
            return null;
        }
        array_unshift($args, $value);
        $vf_ret = call_user_func_array([__NAMESPACE__.'\Filter', $vf_func], $args);
        if (!$vf_ret){
            throw new \Exception($invalid_msg ? $invalid_msg : 'Invalid arguments'.$key);
        }
        return $value;
    }

    public static function values(array $keys){
        $ret_arr = [];
        foreach($keys as $key => $get){
            switch(count($get)){
                case 6:
                    $value = self::value($get[0], $get[1], $get[2], $get[3], $get[4], $get[5]);
                    break;
                case 5:
                    $value = self::value($get[0], $get[1], $get[2], $get[3], $get[4]);
                    break;
                case 4:
                    $value = self::value($get[0], $get[1], $get[2], $get[3]);
                    break;
                case 3:
                    $value = self::value($get[0], $get[1], $get[2]);
                    break;
                case 2:
                    $value = self::value($get[0], $get[1]);
                    break;
                default:
                    throw new \Exception("Invalid arguments num: line $key lt 2");
                    break;
            }
            $_key = is_numeric($key) ? $get[0] : $key;
            $ret_arr[$_key] = $value;
        }
        return $ret_arr;
    }
}
