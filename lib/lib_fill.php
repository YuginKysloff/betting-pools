<?php
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Lib_Fill {

	// Пополнение
    public function fill($data) {
	 	
		switch($data['payment']) {
			case 'advcash':
				$result['valute'] = str_replace('rub', 'rur', $data['valute']);
		        $result['valute'] = strtoupper($result['valute']);
		        $result['amount'] = Lib_Main::clear_num($data['amount'], 2);
		        $result['comment'] = $data['comment'];
		        $result['order_id'] = $data['order_id'];
				
		        $ac_account_email = $data['config']['payments']['advcash']['wallet'];
		        $ac_sci_name = $data['config']['payments']['advcash']['sci_name'];
		        
				$arHash = array(
					$ac_account_email,
					$ac_sci_name,
					$result['amount'],
					$result['valute'],
					$data['config']['payments']['advcash']['sci_pass'],
					$result['order_id']
				);
				$sign = strtoupper(hash('sha256', implode(':', $arHash)));
		        $result['form'] = '
		            <form method="POST" action="https://wallet.advcash.com/sci/" id="pay_form">
                        <input type="hidden" name="ac_account_email" value="'.$ac_account_email.'">
                        <input type="hidden" name="ac_sci_name" value="'.$ac_sci_name.'">
                        <input type="hidden" name="ac_amount" value="'.$result['amount'].'">
                        <input type="hidden" name="ac_currency" value="'.$result['valute'].'">
                        <input type="hidden" name="ac_order_id" value="'.$result['order_id'].'">
                        <input type="hidden" name="ac_sign" value="'.$sign.'">
                        <input type="hidden" name="ac_comments" value="'.$result['comment'].'">
                    </form>
                ';
				return $result;
		    break;
			case 'freekassa':
		        $result['amount'] = Lib_Main::clear_num($data['amount'], 2);
		        $result['order_id'] = $data['order_id'];
		        
		        $result['form'] = '
		            <form method="GET" action="http://www.free-kassa.ru/merchant/cash.php" id="pay_form">
						<input type="hidden" name="m" value="'.$data['config']['payments']['freekassa']['merchant_id'].'">
						<input type="hidden" name="oa" value="'.$result['amount'].'">
						<input type="hidden" name="s" value="'.md5($data['config']['payments']['freekassa']['merchant_id'].":".$result['amount'].":".$data['config']['payments']['freekassa']['secret_word'].":".$result['order_id']).'">
						<input type="hidden" name="o" value="'.$result['order_id'].'">
					</form>
                ';
				return $result;
		    break;
			case 'interkassa':
		        $result['amount'] = Lib_Main::clear_num($data['amount'], 2);
		        $result['comment'] = $data['comment'];
		        $result['order_id'] = $data['order_id'];
				$result['valute'] = strtoupper($data['valute']);
				
				$lk['ik_am'] = $result['amount'];
				$lk['ik_co_id'] = $data['config']['payments']['interkassa']['sci_id'];
				$lk['ik_pm_no'] = $result['order_id'];
				$lk['ik_cur'] = $result['valute'];
				$lk['ik_desc'] = $result['comment'];
			
				ksort($lk, SORT_STRING);
				array_push($lk, $data['config']['payments']['interkassa']['sci_key']);
				$signString = implode(':', $lk);
				$sign = base64_encode(md5($signString, true));

		        $result['form'] = '
		            <form name="payment" method="post" action="https://sci.interkassa.com/" accept-charset="UTF-8" id="pay_form">
					    <input type="hidden" name="ik_co_id" value="'.$lk['ik_co_id'].'">
					    <input type="hidden" name="ik_pm_no" value="'.$lk['ik_pm_no'].'">
					    <input type="hidden" name="ik_am" value="'.$lk['ik_am'].'">
					    <input type="hidden" name="ik_cur" value="'.$lk['ik_cur'].'">
					    <input type="hidden" name="ik_sign" value="'.$sign.'">
					    <input type="hidden" name="ik_desc" value="'.$lk['ik_desc'].'">
				    </form>
                ';
				return $result;
		    break;
			case 'payeer':
		        $result['valute'] = strtoupper($data['valute']);
		        $result['amount'] = Lib_Main::clear_num($data['amount'], 2);
		        $result['comment'] = $data['comment'];
		        $result['order_id'] = $data['order_id'];
				
				$m_shop = $data['config']['payments']['payeer']['shop_id'];
				$m_desc = base64_encode($result['comment']);
				$m_key = $data['config']['payments']['payeer']['shop_key'];
				$arHash = array(
					$m_shop,
					$result['order_id'],
					$result['amount'],
					$result['valute'],
					$m_desc,
					$m_key
				);
				$sign = strtoupper(hash('sha256', implode(':', $arHash)));
				$result['form'] = '
					<form method="GET" action="//payeer.com/merchant/" id="pay_form">
						<input type="hidden" name="m_shop" value="'.$m_shop.'">
						<input type="hidden" name="m_orderid" value="'.$result['order_id'].'">
						<input type="hidden" name="m_amount" value="'.$result['amount'].'">
						<input type="hidden" name="m_curr" value="'.$result['valute'].'">
						<input type="hidden" name="m_desc" value="'.$m_desc.'">
						<input type="hidden" name="m_sign" value="'.$sign.'">
					</form>
				';
				return $result;
			break;
			case 'perfectmoney':
				$result['valute'] = strtoupper($data['valute']);
		        $result['amount'] = Lib_Main::clear_num($data['amount'], 2);
		        $result['comment'] = $data['comment'];
		        $result['order_id'] = $data['order_id'];
				
				$result['form'] = '
					<form action="https://perfectmoney.is/api/step1.asp" method="POST" id="pay_form">
						<input type="hidden" name="PAYEE_ACCOUNT" value="'.$data['config']['payments']['perfectmoney']['wallet'].'">
						<input type="hidden" name="PAYEE_NAME" value="'.$result['comment'].'">
						<input type="hidden" name="PAYMENT_ID" value="'.$result['order_id'].'"><BR>
						<input type="hidden" name="PAYMENT_AMOUNT" value="'.$result['amount'].'"><BR>
						<input type="hidden" name="PAYMENT_UNITS" value="'.$result['valute'].'">
						<input type="hidden" name="STATUS_URL" value="'.$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/status/perfectmoney">
						<input type="hidden" name="PAYMENT_URL" value="'.$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/fill/success">
						<input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
						<input type="hidden" name="NOPAYMENT_URL" value="'.$data['config']['site']['protocol'].'://'.$data['config']['site']['domain'].'/fill/fail">
						<input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
						<input type="hidden" name="SUGGESTED_MEMO" value="">
						<input type="hidden" name="BAGGAGE_FIELDS" value="">
					</form>
				';
				return $result;
			break;
			case 'qiwi':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ FREE-KASSA
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
		        $result['order_id'] = $data['order_id'];
		        
		        $result['form'] = '
		            <form method="GET" action="http://www.free-kassa.ru/merchant/cash.php" id="pay_form">
						<input type="hidden" name="m" value="'.$data['config']['payments']['freekassa']['merchant_id'].'">
						<input type="hidden" name="oa" value="'.$result['amount'].'">
						<input type="hidden" name="s" value="'.md5($data['config']['payments']['freekassa']['merchant_id'].":".$result['amount'].":".$data['config']['payments']['freekassa']['secret_word'].":".$result['order_id']).'">
						<input type="hidden" name="o" value="'.$result['order_id'].'">
						<input type="hidden" name="i" value="63">
						<input type="hidden" name="em" value="'.$data['user']['email'].'">
					</form>
                ';
				return $result;
			break;
			case 'yandexmoney':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ FREE-KASSA
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
		        $result['order_id'] = $data['order_id'];
		        
		        $result['form'] = '
		            <form method="GET" action="http://www.free-kassa.ru/merchant/cash.php" id="pay_form">
						<input type="hidden" name="m" value="'.$data['config']['payments']['freekassa']['merchant_id'].'">
						<input type="hidden" name="oa" value="'.$result['amount'].'">
						<input type="hidden" name="s" value="'.md5($data['config']['payments']['freekassa']['merchant_id'].":".$result['amount'].":".$data['config']['payments']['freekassa']['secret_word'].":".$result['order_id']).'">
						<input type="hidden" name="o" value="'.$result['order_id'].'">
						<input type="hidden" name="i" value="45">
						<input type="hidden" name="em" value="'.$data['user']['email'].'">
					</form>
                ';
				return $result;
			break;
			default:
				throw new Exception();
			break;
		}
	}
	
	// Проверка запроса
	public function check($data) {

		// Определение переменных
		$result['ip'] = Lib_Main::get_ip();
		$result['status'] = 'fail'; 
		if(!is_array($data['config']['payments'][$data['payment']])) {
			$result['error'] = 'Payment system is not configured';
			return $result;
		}
		if(is_array($data['config']['payments'][$data['payment']]['ip'])) {
			if(!in_array($result['ip'], $data['config']['payments'][$data['payment']]['ip'])) {
				$result['error'] = 'A request from third-party IP in the array';
				return $result;
			}
		} else {
			if($result['ip'] != $data['config']['payments'][$data['payment']]['ip']) {
				$result['error'] = 'A request from third-party IP'.$data['config']['payments'][$data['payment']]['ip'];
				return $result;
			}
		}

		switch($data['payment']) {
			case 'advcash':
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
						$data['config']['payments']['advcash']['sci_pass']
					);
					$sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
					if(strtoupper($_POST['ac_hash']) == $sign_hash) {
						$result['order_id'] = Lib_Main::abs_num(Lib_Main::clear_num($_POST['ac_order_id'], 0));
						$valute = strtolower($_POST['ac_merchant_currency']);
						$result['valute'] = str_replace('rur', 'rub', $valute);
						$result['amount'] = $_POST['ac_amount'];
						$result['status'] = 'success';
						$result['success'] = $_POST['ac_order_id'].'|success';
					} else {
						$result['error'] = $_POST['ac_order_id'].'|error';
					}
				} else {
					$result['error'] = $_POST['ac_order_id'].'|error';
				}
				return $result;
			break;
			case 'freekassa':
                $sign = md5($data['config']['payments']['freekassa']['merchant_id'].":".$_REQUEST['AMOUNT'].":".$data['config']['payments']['freekassa']['secret_word2'].":".$_REQUEST['MERCHANT_ORDER_ID']);
                if($sign != $_REQUEST['SIGN']) {
                    $result['error'] = 'wrong sign';
					return $result;
                }
                $result['order_id'] = Lib_Main::abs_num(Lib_Main::clear_num($_REQUEST['MERCHANT_ORDER_ID'] ?? 0, 0));
                $result['valute'] = 'rub';
                $result['amount'] = $_REQUEST['AMOUNT'];
				$result['status'] = 'success';
				$result['success'] = 'YES';
				return $result;
            break;
			case 'interkassa':
				if($_POST['ik_co_id'] != $data['config']['payments']['interkassa']['sci_id']) {
					$result['error'] = 'empty sci id';
					return $result;
				}
				foreach($_POST as $key => $value) {
					if(!preg_match('/ik_/', $key)) continue;
					$ik[$key] = $value;
				}
				unset($ik['ik_sign']);
				ksort($ik, SORT_STRING);
				$ik_key = ($_POST['ik_pw_via'] == 'test_interkassa_test_xts') ? $data['config']['payments']['interkassa']['sci_test_key'] : $data['config']['payments']['interkassa']['sci_key'];
				array_push($ik, $ik_key);
				$signString = implode(':', $ik);
				$sign = base64_encode(md5($signString, true));
				if($sign === $_POST['ik_sign'] && $ik['ik_inv_st'] == 'success') {
					$result['order_id'] = Lib_Main::abs_num(Lib_Main::clear_num($_POST['ik_pm_no'], 0));
					$result['valute'] = strtolower($_POST['ik_cur'] ?? 'rub');
					$result['amount'] = $_POST['ik_am'];
					$result['status'] = 'success';
					$result['success'] = 'YES';
					return $result;
				}
				$result['error'] = 'wrong sign';
				return $result;
		    break;
			case 'payeer':
				if(isset($_POST['m_operation_id']) && isset($_POST['m_sign'])) {
					$m_key = $data['config']['payments']['payeer']['shop_key'];
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
						$result['order_id'] = Lib_Main::abs_num(Lib_Main::clear_num($_POST['m_orderid'], 0));
						$result['valute'] = strtolower($_POST['m_curr']);
						$result['amount'] = $_POST['m_amount'];
						$result['status'] = 'success';
						$result['success'] = $_POST['m_orderid'].'|success';
					} else {
						$result['error'] = $_POST['m_orderid'].'|error';
					}
				} else {
					$result['error'] = $_POST['m_orderid'].'|error';
				}
				return $result;
			break;
			case 'perfectmoney':
				$altHash = strtoupper(md5($data['config']['payments']['perfectmoney']['alt_hash']));
				define('ALTERNATE_PHRASE_HASH', $altHash);
				$string=
				$_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.
				$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.
				$_POST['PAYMENT_BATCH_NUM'].':'.
				$_POST['PAYER_ACCOUNT'].':'.ALTERNATE_PHRASE_HASH.':'.
				$_POST['TIMESTAMPGMT'];
				$hash=strtoupper(md5($string));
				if($hash == $_POST['V2_HASH']) {
					$result['order_id'] = Lib_Main::abs_num(Lib_Main::clear_num($_POST['PAYMENT_ID'], 0));
					$result['valute'] = strtolower($_POST['PAYMENT_UNITS']);
					$result['amount'] = $_POST['PAYMENT_AMOUNT'];
					$result['status'] = 'success';
				} else {
					$result['error'] = 'hacking attempt!';
				}
				return $result;
			break;
			default:
				$result['error'] = 'hacking attempt!';
				return $result;
			break;
		}
	}
}