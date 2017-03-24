<?php
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Lib_Validation {

	// Валидация логина
    public static function login($value) {
        if($value == '') return 'login-empty';
		if(preg_match("/[^(a-zA-Z)(0-9)]+/", trim($value))) return 'login-allowed';
		if(!preg_match("/[a-zA-Z]+/", $value)) return 'login-letters';
		if(mb_strlen($value, 'utf8') < 5 || mb_strlen($value, 'utf8') > 10) return 'login-size';
		if(Lib_Main::clear_str($value) !== $value) return 'login-illegal';
		return $value;
    }
	
	// Валидация пароля
    public static function password($value) {
		if($value == '') return 'password-empty';
		if(mb_strlen($value, 'utf8') < 5) return 'password-size';
		return $value;
    }
	
	// Валидация email
    public static function email($value) {
		if($value == '') return 'email-empty';
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) return 'email-invalid';
		if(mb_strlen($value, 'utf8') > 130) return 'email-size';
		return $value;
    }
	
	// Валидация ключа
    public static function key($value) {
		if($value == '') return 'key-empty';
		if(preg_match("/[^(a-z_)(0-9)]+/", trim($value))) return 'key-invalid';
		if(mb_strlen($value, 'utf8') > 50) return 'key-size';
		return $value;
    }
	
	// Валидация даты рождения
    public static function birthday($value) {
		if($value == '') return 'birthday-empty';
		if(date("d-m-Y", $value) == '01-01-1970') return 'birthday-invalid';
		if((time() - $value) < 567648000) return 'birthday-adult';
		return $value;
    }
	
	// Валидация IP
    public static function ip($value) {
		if($value == '') return 'ip-empty';
		if(!preg_match("#^([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})$#", $value)) return 'ip-invalid';
		return $value;
    }
}