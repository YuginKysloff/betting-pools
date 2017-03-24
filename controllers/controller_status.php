<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Status {

	public function __construct($data) {
		$this->data = $data;

		// Подключение моделей
		require_once(DIR_ROOT.'/models/model_fill.php');
		require_once(DIR_ROOT.'/models/model_log.php');
		require_once(DIR_ROOT.'/models/model_users.php');

		// Определение объектов
		$this->obj['model_fill'] = new Model_Fill();
		$this->obj['model_log'] = new Model_Log();
		$this->obj['model_users'] = new Model_Users();
	}

	// Зачисление средств
	public function transfer() {

		// Определение переменных
		$ip = Lib_Main::get_ip();
		$payment = Lib_Main::clear_str($this->data['route']['param']['payment']);
		if(!is_array($this->data['config']['payments'][$payment])) {
			die("hacking attempt!");
		}
		if(is_array($this->data['config']['payments'][$payment]['ip_arr'])) {
			if(!in_array($ip, $this->data['config']['payments'][$payment]['ip_arr'])) {
				die("hacking attempt!");
			}
		} else {
			if($ip != $this->data['config']['payments'][$payment]['ip_arr']) {
				die("hacking attempt!");
			}
		}

		$fin = '';
		$err = '';

		switch($payment) {
			case 'advcash':
				$fin = $_POST['ac_order_id'].'|success';
				$err = $_POST['ac_order_id'].'|error';
				if(isset($_POST['ac_transfer']) && isset($_POST['ac_hash'])) {
					$arHash = array(
					    $_POST['ac_transfer'],
						$_POST['ac_start_date'],
						$_POST['ac_sci_name'],
						$_POST['ac_src_wallet'],
						$_POST['ac_dest_wallet'],
						$_POST['ac_order_id'],
						$_POST['ac_amount'],
						$_POST['ac_merchant_currency'],
						$this->data['config']['payments']['advcash']['sci_pass']
					);
					$sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
					if(strtoupper($_POST['ac_hash']) == $sign_hash) {
						$order_id = Lib_Main::clear_num(abs($_POST['ac_order_id']), 0);
						$valute = strtolower($_POST['ac_merchant_currency']);
						$valute = str_replace('rur', 'rub', $valute);
						$amount = $_POST['ac_merchant_amount'];
					} else {
						die($err);
					}
				} else {
					die($err);
				}
			break;
			case 'payeer':
				$fin = $_POST['m_orderid'].'|success';
				$err = $_POST['m_orderid'].'|error';
				if(isset($_POST['m_operation_id']) && isset($_POST['m_sign'])) {
					$m_key = $this->data['config']['payments']['payeer']['shop_key'];
					$arHash = array($_POST['m_operation_id'],
							$_POST['m_operation_ps'],
							$_POST['m_operation_date'],
							$_POST['m_operation_pay_date'],
							$_POST['m_shop'],
							$_POST['m_orderid'],
							$_POST['m_amount'],
							$_POST['m_curr'],
							$_POST['m_desc'],
							$_POST['m_status'],
							$m_key);
					$sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
					if($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success') {
						$order_id = Lib_Main::clear_num(abs($_POST['m_orderid']), 0);
						$valute = strtolower($_POST['m_curr']);
						$amount = $_POST['m_amount'];
					} else {
						die($err);
					}
				} else {
					die($err);
				}
			break;
			case 'pm':
				$altHash = strtoupper(md5($this->data['config']['payments']['pm']['alt_hash']));
				define('ALTERNATE_PHRASE_HASH', $altHash);
				$string=
				$_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.
				$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.
				$_POST['PAYMENT_BATCH_NUM'].':'.
				$_POST['PAYER_ACCOUNT'].':'.ALTERNATE_PHRASE_HASH.':'.
				$_POST['TIMESTAMPGMT'];
				$hash=strtoupper(md5($string));
				if($hash == $_POST['V2_HASH']) {
					$order_id = Lib_Main::clear_num(abs($_POST['PAYMENT_ID']), 0);
					$valute = strtolower($_POST['PAYMENT_UNITS']);
					$amount = $_POST['PAYMENT_AMOUNT'];
				} else {
					die("hacking attempt!");
				}
			break;
			default:
				throw new Exception('hacking attempt!');
			break;
		}

		// Расчёты и начисления
		$amount = Lib_Main::clear_num($amount, 2);
		$fill = $this->obj['model_fill']->get_by_id(['id' => $order_id]);
		if(!$fill || $fill['valute'] !== $valute || Lib_Main::clear_num($fill['amount'], 2) !== $amount || $fill['payment'] !== $payment || $fill['status'] !== '0') {
			die($err);
		}

		// Завершение пополения
		$this->obj['model_fill']->update(['set' => ['status' => 1, 'sort' => time()], 'where' => $fill['id']]);

		// Получение пользователя
		$user = $this->obj['model_users']->get_by_id(['id' => $fill['user_id']]);

		// Зачисление на баланс пользователю
		$this->obj['model_users']->update(['set' => [$valute => Lib_Main::clear_num($user[$valute] + $amount)], 'where' => $user['id']]);

		// Запись в лог о пополнении
		$this->obj['model_log']->insert(['user_id' => $user['id'], 'text' => '[m-fill|filling-balance] : <span class="g-currency-'.$valute.'">'.Lib_Main::beauty_number($amount).'</span>', 'control' => $valute.'='.Lib_Main::clear_num($user[$valute] + $amount), 'datetime' => time()]);

		// Зачисление реферального вознаграждения
		if($user['sponsor_id'] != 0) {
			Controller_Users::refsys(['user' => $user, 'valute' => $valute, 'amount' => $amount, 'payment' => $payment, 'site_name' => $this->data['config']['site']['name']]);
		}
		die($fin);
	}
}
