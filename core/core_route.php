<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Core_Route extends Core_Controller {
	
	static function init() {

		// Роутинг
		$url = [
			'profile' => 'account',
			'referrals' => 'refsys',
			'withdrawals' => 'payout',
		];
		
		$config = Config::instance()->config;
		
		// Создаем объект главного контроллера
		$controller = new Core_Controller();

		$request = $_SERVER['REQUEST_URI']; // Адресная строка

		// Крон
		if(preg_match("/cron\/([-a-z]+)/", $request, $matches)) {
			$controller->cron($matches[1]);
		}

		// Админка
		if(preg_match("/".$config['site']['admin']."\/([-a-z0-9]+)\/([-a-z0-9_]+)\/(.*+)/", $request, $matches)) {
			$controller->main(['admin' => true, 'controller' => $matches[1], 'method' => $matches[2], 'param' => $matches[3]]);
		}
		if(preg_match("/".$config['site']['admin']."\/([-a-z]+)\/([0-9]+)/", $request, $matches)) {
			$controller->main(['admin' => true, 'controller' => $matches[1], 'param' => 'p/'.$matches[2]]);
		}
		if(preg_match("/".$config['site']['admin']."\/([-a-z0-9]+)\/([-a-z0-9_]+)/", $request, $matches)) {
			$controller->main(['admin' => true, 'controller' => $matches[1], 'method' => $matches[2]]);
		}
		if(preg_match("/".$config['site']['admin']."\/([-a-z0-9]+)\/(.*+)/", $request, $matches)) {
			$controller->main(['admin' => true, 'controller' => $matches[1], 'param' => $matches[2]]);
		}
		if(preg_match("/".$config['site']['admin']."\/([-a-z0-9]+)/", $request, $matches)) {
			$controller->main(['admin' => true, 'controller' => $matches[1]]);
		}
		if(preg_match("/".$config['site']['admin']."/", $request, $matches)) {
			$controller->main(['admin' => true, 'controller' => 'index']);
		}
	
		// Запись посещения
		if($config['site']['statistics']) {
			if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				$path = DIR_ROOT.'/txt/statistics/'.date("Y-m-d").'.txt';
				if(!file_exists($path)) {
					if(!is_dir(DIR_ROOT.'/txt/statistics')) {
						mkdir(DIR_ROOT.'/txt/statistics');
						file_put_contents(DIR_ROOT.'/txt/statistics/index.html', '');
					}
					file_put_contents($path, '', LOCK_EX);
				}
				file_put_contents($path, time().'::'.($_COOKIE['PHPSESSID'] ?? '').'::'.Lib_Main::get_ip().'::'.($_SERVER['HTTP_USER_AGENT'] ?? '').'::'.($_SERVER['HTTP_REFERER'] ?? '').'::http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].PHP_EOL.file_get_contents($path), LOCK_EX);
			}
		}

		// Сайт
		if(preg_match("/news\/([0-9]+)/", $request, $matches)) {
			$controller->main(['controller' => 'news', 'method' => 'index', 'param' => 'id/'.$matches[1]]);
		}
		if(preg_match("/errors\/([-a-z0-9]+)/", $request, $matches)) {
			$controller->errors($matches[1]);
		}
		if(preg_match("/status\/([a-zA-Z]+)/", $request, $matches)) {
			$controller->main(['controller' => 'status', 'method' => 'transfer', 'param' => 'payment/'.$matches[1]]);
		}
		if(isset($url)) {
			if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				foreach($url as $key => $val) {
					if(preg_match("/(\b".$val."\b)/", $request)) {
						$controller->errors('404');
					}
					$request = str_replace($key, $val, $request);
				}
			}
		}
		if(preg_match("/fill\/success/", $request, $matches)) {
			$controller->main(['controller' => 'deposits', 'method' => 'index', 'param' => 'status/success']);
		}
		if(preg_match("/fill\/fail/", $request, $matches)) {
			$controller->main(['controller' => 'deposits', 'method' => 'index', 'param' => 'status/fail']);
		}
		if(preg_match("/lost\/([0-9]+)\/([a-zA-Z0-9]+)/", $request, $matches)) {
			$controller->main(['controller' => 'users', 'method' => 'lost_pass_act', 'link' => $matches[1], 'key' => $matches[2]]);
		}
		if(preg_match("/confirm\/([0-9]+)\/([a-zA-Z0-9]+)/", $request, $matches)) {
			$controller->main(['controller' => 'dashboard', 'method' => 'confirm_email_act', 'link' => $matches[1], 'key' => $matches[2]]);
		}
		if(preg_match("/p=([0-9]+)/", $request, $matches)) {
			$controller->main(['controller' => 'index', 'sponsor' => $matches[1]]);
		}
		if(preg_match("/p=([a-zA-Z0-9]+)/", $request, $matches)) {
			$controller->main(['controller' => 'index', 'sponsor' => $matches[1]]);
		}
		if(preg_match("/([-a-z0-9]+)\/([-a-z0-9_]+)\/(.*+)/", $request, $matches)) {
			$controller->main(['controller' => $matches[1], 'method' => $matches[2], 'param' => $matches[3]]);
		}
		if(preg_match("/([-a-z]+)\/([0-9]+)/", $request, $matches)) {
			$controller->main(['controller' => $matches[1], 'param' => 'p/'.$matches[2]]);
		}
		if(preg_match("/([-a-z0-9]+)\/([-a-z0-9_]+)/", $request, $matches)) {
			$controller->main(['controller' => $matches[1], 'method' => $matches[2]]);
		}
		if(preg_match("/([-a-z0-9]+)\/(.*+)/", $request, $matches)) {
			$controller->main(['controller' => $matches[1], 'param' => $matches[2]]);
		}
		if(preg_match("/([-a-z0-9]+)/", $request, $matches)) {
			$controller->main(['controller' => $matches[1]]);
		}
		$controller->main(['controller' => 'index']);
	}
}