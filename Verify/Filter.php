<?php

//https://github.com/shukean/php-verify-input

namespace Verify;

class Filter{

    public static function vfSet($value, $value_vf){
        foreach (explode(',', $value) as $row){
            if (!self::$value_vf($row)) {
                return false;
            }
        }
        return true;
    }

    public static function vfJson($value, $assoc=true){
        $ret = json_decode($value, $assoc);
        if (!$ret || json_last_error()){
            return false;
        }else{
            return true;
        }
    }

    public static function vfArray($value, $value_vf=null){
        if(is_array($value)){
            if ($value_vf){
                foreach($value as $row){
                    if(!self::$value_vf($row)){
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    public static function vfTinyInt($value, $unsigned=true){
        return $unsigned ? self::vfNumeric($value, 0, 255) : self::vfNumeric($value, -128, 127);
    }

    public static function vfSmallInt($value, $unsigned=true){
        return $unsigned ? self::vfNumeric($value, 0, 65535) : self::vfNumeric($value, -32768, 32767);
    }

    public static function vfMediumInt($value, $unsigned=true){
        return $unsigned ? self::vfNumeric($value, 0, 16777215) : self::vfNumeric($value, -8388608, -8388607);
    }

    public static function vfInt($value, $unsigned=true){
        return $unsigned ? self::vfNumeric($value, 0, 4294967295) : self::vfNumeric($value, -2147483648, -2147483647);
    }

    public static function vfId($value){
        return self::vfNumeric($value, 1, PHP_INI_MAX);
    }

    public static function vfNumeric($vlaue, $min, $max){
        return is_numeric($vlaue) && $vlaue >= $min && $vlaue <= $max;
    }

    public static function vfLocation($value){
        return preg_match('/^\-?\d+(\.\d{1,})?$/', $value);
    }

	//only support utf8
	public static function vfString($value, $min=0, $max=65536){
		$value_len = 0;
		if (function_exists('mb_strlen')){
            $value_len = mb_strlen($value, 'utf-8');
        }else{
            $value_len = count(preg_split("//u", $value, -1, PREG_SPLIT_NO_EMPTY));
        }
		return $value_len >= $min && $value_len <= $max;
	}

    public static function vfEmpty($value){
        return empty($value);
    }

    public static function vfNoEmpty($value){
        return !self::vfEmpty($value);
    }

    public static function vfMobile($value){
        return preg_match('/^1(3|4|5|7|8)[0-9]{9}$/', $value);
    }

	public static function vfPhone($value, $verify_short=false){
        if (preg_match('/^0[3-9]\d{2}\d{7,8}$/', $value)){
            return true;
        }elseif (preg_match('/^0(10|2\d)\d{7,8}$/', $value)){
            return true;
        }elseif (preg_match('/^[48]00\d{7}$/', $value)){
            return true;
        }elseif (preg_match('/^0085[23]\d{8}$/', $value)){    //HK    //Macau
            return true;
        }elseif (preg_match('/^00886\d{7,8}$/', $value)){    //TW
            return true;
        }else{
            if ($verify_short) {
                if (preg_match('/^1[012]\d{1,3}$/', $value)) {
                    return true;
                }
                if (preg_match('/^9[56]\d{3,}$/', $value)) {
                    return true;
                }
            }
            return false;
        }
    }

	public static function vfEmail($value, $min_len=6){
        return strlen($value) >= $min_len && preg_match('/^[\w\-\.]+@[\w\-\.]+(\.[\w\-]+)+$/', $value);
    }

	public static function vfUrl($value){
        return preg_match('/^https?:\/\/\w+(\.\w+){1,}/', $value);
    }

	public static function vfDateStr($value){
        return strtotime($value);
    }

    public static function vfTimestamp($value){
        return date('Y-m-d H:i:s', $value) !== false;
    }

	public static function vfBool($value){
        return $value == 1 || $value == 0;
    }

    public static function vfRMB($value, $unsigned=false){
        return $unsigned ? preg_match('/^\d+(\.\d{0,2})?$/', $value) : preg_match('/^\-?\d+(\.\d{0,2})?$/', $value);
    }

}

