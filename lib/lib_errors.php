<?php
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

// Обработчик общих ошибок
function errorHandler($errno, $errstr, $errfile, $errline) {
	
    if (!(error_reporting() & $errno)) {

        // Этот код ошибки не включен в error_reporting
        return;
    }
 	$errorType = array (
        E_ERROR          	=> 'FATAL ERROR',
        E_WARNING        	=> 'WARNING',
        E_PARSE          	=> 'PARSING ERROR',
        E_NOTICE         	=> 'NOTICE',
        E_CORE_ERROR     	=> 'CORE ERROR',
        E_CORE_WARNING   	=> 'CORE WARNING',
        E_COMPILE_ERROR  	=> 'COMPILE ERROR',
        E_COMPILE_WARNING	=> 'COMPILE WARNING',
        E_USER_ERROR        => 'USER ERROR',
        E_USER_WARNING      => 'USER WARNING',
        E_USER_NOTICE       => 'USER NOTICE',
        E_STRICT        	=> 'STRICT NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR'
    );
	
	$time = time();
 	$err = '['.date('Y-m-d H:i:s', $time).' | '.Lib_Main::get_ip().'] ';
    if (array_key_exists($errno, $errorType)) {
        $err .= $errorType[$errno];
    } else {
		$err .= 'UNKNOW ERROR';
	}
 	$err .= ': '.$errstr.' => '.$errfile.' (line: '.$errline.')'.PHP_EOL;

	// Проверка существования дирректории
	if(!is_dir(DIR_ROOT.'/txt')) {
		mkdir(DIR_ROOT.'/txt');
		file_put_contents(DIR_ROOT.'/txt/index.html', '');
	}
	if(!is_dir(DIR_ROOT.'/txt/crons')) {
		mkdir(DIR_ROOT.'/txt/crons');
		file_put_contents(DIR_ROOT.'/txt/crons/index.html', '');
	}
	if(!is_dir(DIR_ROOT.'/txt/emails')) {
		mkdir(DIR_ROOT.'/txt/emails');
		file_put_contents(DIR_ROOT.'/txt/emails/index.html', '');
	}
	if(!is_dir(DIR_ROOT.'/txt/errors')) {
		mkdir(DIR_ROOT.'/txt/errors');
		file_put_contents(DIR_ROOT.'/txt/errors/index.html', '');
	}
	if(!is_dir(DIR_ROOT.'/txt/statistics')) {
		mkdir(DIR_ROOT.'/txt/statistics');
		file_put_contents(DIR_ROOT.'/txt/statistics/index.html', '');
	}
	
	$filename = date('Y-m-d', $time);
	$file = DIR_ROOT.'/txt/errors/'.$filename.'.txt';
	if(!file_exists($file)) {
		file_put_contents($file, $err, LOCK_EX);
	} else {
		file_put_contents($file, $err.file_get_contents($file), LOCK_EX);
	}
	if ($errno != E_NOTICE && $errno != E_USER_NOTICE) {
		exit(Lib_Main::rew_page('errors/data'));
	}
}

// Обработчик fatal error.
function shutdownHandler() {
	if (is_array($e = error_get_last())) {
		$code = isset($e['type']) ? $e['type'] : 0;
		$msg = isset($e['message']) ? $e['message'] : '';
		$file = isset($e['file']) ? $e['file'] : '';
		$line = isset($e['line']) ? $e['line'] : '';
		if ($code > 0) {
			errorHandler($code, $msg, $file, $line);
		}
	}
}
?>