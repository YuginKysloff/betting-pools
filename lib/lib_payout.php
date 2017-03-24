<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Lib_Payout {
	
	// Выплата
    public function payout($data) {
		
		$result['status'] = 'fail';
		
		switch($data['payment']) {
			case 'advcash':
				require_once(DIR_ROOT.'/lib/payments/MerchantWebService.php');
				$merchantWebService = new MerchantWebService();
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = str_replace('rub', 'rur', $data['valute']);
				$result['valute'] = strtoupper($result['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];

				$arg0 = new authDTO();
				$arg0->apiName = $data['config']['payments']['advcash']['api_name'];
				$arg0->accountEmail = $data['config']['payments']['advcash']['wallet'];
				$arg0->authenticationToken = $merchantWebService->getAuthenticationToken($data['config']['payments']['advcash']['api_pass']);

				$arg1 = new sendMoneyRequest();
				$arg1->amount = $result['amount'];
				$arg1->currency = $result['valute'];
				$arg1->email = $result['wallet'];
				//$arg1->walletId = "U000000000000";
				$arg1->note = $result['comment'];
				$arg1->savePaymentTemplate = false;

				$validationSendMoney = new validationSendMoney();
				$validationSendMoney->arg0 = $arg0;
				$validationSendMoney->arg1 = $arg1;

				$sendMoney = new sendMoney();
				$sendMoney->arg0 = $arg0;
				$sendMoney->arg1 = $arg1;

				try {
					$merchantWebService->validationSendMoney($validationSendMoney);
					$sendMoneyResponse = $merchantWebService->sendMoney($sendMoney);
					$result['status'] = 'success';
					return $result;
				} catch (Exception $e) {
				    $result['error'] = "ERROR MESSAGE => ".$e->getMessage();
				    $result['error'] .= $e->getTraceAsString();
					return $result;
				}
			break;
			case 'beeline':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ PAYEER
				require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$accountNumber = $data['config']['payments']['payeer']['wallet'];
				$apiId = $data['config']['payments']['payeer']['api_id'];
				$apiKey = $data['config']['payments']['payeer']['api_key'];
				$payeer = new CPayeerLib($accountNumber, $apiId, $apiKey);
				if($payeer->isAuth()) {
					$initOutput = $payeer->initOutput(array(
						'ps' => $data['config']['payments']['beeline']['wallet'],
						//'sumIn' => 1,
						'curIn' => $result['valute'],
						'sumOut' => $result['amount'],
						'curOut' => $result['valute'],
						'param_ACCOUNT_NUMBER' => $result['wallet']
					));

					if($initOutput) {
						$historyId = $payeer->output();
						if($historyId > 0) {
							$result['status'] = 'success';
							return $result;
						} else {
							ob_start();
							echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
							$result['error'] = ob_get_contents();
							ob_end_clean();
							return $result;
						}
					} else {
						ob_start();
						echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
						$result['error'] = ob_get_contents();
						ob_end_clean();
						return $result;
					}
				} else {
					ob_start();
					echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					$result['error'] = ob_get_contents();
					ob_end_clean();
					return $result;
				}
			break;
			case 'freekassa':
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://wallet.free-kassa.ru/api_v1.php');
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array('wallet_id' => $data['config']['payments']['freekassa']['wallet'], 'purse' => $result['wallet'], 'amount' => $result['amount'], 'sign' => md5($data['config']['payments']['freekassa']['wallet'].$result['amount'].$result['wallet'].$data['config']['payments']['freekassa']['api_key']), 'action'=> 'transfer'));
				$res = trim(curl_exec($ch));
				$c_errors = curl_error($ch);
				curl_close($ch);
				
				if(preg_match("/\"status\":\"info\"/i",$res)) {
					$result['status'] = 'success';
					return $result;
				} else {
					$result['error'] = $c_errors;
					return $result;
				}
			break;
			case 'megafon':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ PAYEER
				require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$accountNumber = $data['config']['payments']['payeer']['wallet'];
				$apiId = $data['config']['payments']['payeer']['api_id'];
				$apiKey = $data['config']['payments']['payeer']['api_key'];
				$payeer = new CPayeerLib($accountNumber, $apiId, $apiKey);
				if($payeer->isAuth()) {
					$initOutput = $payeer->initOutput(array(
						'ps' => $data['config']['payments']['megafon']['wallet'],
						//'sumIn' => 1,
						'curIn' => $result['valute'],
						'sumOut' => $result['amount'],
						'curOut' => $result['valute'],
						'param_ACCOUNT_NUMBER' => $result['wallet']
					));

					if($initOutput) {
						$historyId = $payeer->output();
						if($historyId > 0) {
							$result['status'] = 'success';
							return $result;
						} else {
							ob_start();
							echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
							$result['error'] = ob_get_contents();
							ob_end_clean();
							return $result;
						}
					} else {
						ob_start();
						echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
						$result['error'] = ob_get_contents();
						ob_end_clean();
						return $result;
					}
				} else {
					ob_start();
					echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					$result['error'] = ob_get_contents();
					ob_end_clean();
					return $result;
				}
			break;
			case 'mtc':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ PAYEER
				require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$accountNumber = $data['config']['payments']['payeer']['wallet'];
				$apiId = $data['config']['payments']['payeer']['api_id'];
				$apiKey = $data['config']['payments']['payeer']['api_key'];
				$payeer = new CPayeerLib($accountNumber, $apiId, $apiKey);
				if($payeer->isAuth()) {
					$initOutput = $payeer->initOutput(array(
						'ps' => $data['config']['payments']['mtc']['wallet'],
						//'sumIn' => 1,
						'curIn' => $result['valute'],
						'sumOut' => $result['amount'],
						'curOut' => $result['valute'],
						'param_ACCOUNT_NUMBER' => $result['wallet']
					));

					if($initOutput) {
						$historyId = $payeer->output();
						if($historyId > 0) {
							$result['status'] = 'success';
							return $result;
						} else {
							ob_start();
							echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
							$result['error'] = ob_get_contents();
							ob_end_clean();
							return $result;
						}
					} else {
						ob_start();
						echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
						$result['error'] = ob_get_contents();
						ob_end_clean();
						return $result;
					}
				} else {
					ob_start();
					echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					$result['error'] = ob_get_contents();
					ob_end_clean();
					return $result;
				}
			break;
			case 'okpay':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ PAYEER
				require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$accountNumber = $data['config']['payments']['payeer']['wallet'];
				$apiId = $data['config']['payments']['payeer']['api_id'];
				$apiKey = $data['config']['payments']['payeer']['api_key'];
				$payeer = new CPayeerLib($accountNumber, $apiId, $apiKey);
				if($payeer->isAuth()) {
					$initOutput = $payeer->initOutput(array(
						'ps' => $data['config']['payments']['okpay']['wallet'],
						//'sumIn' => 1,
						'curIn' => $result['valute'],
						'sumOut' => $result['amount'],
						'curOut' => $result['valute'],
						'param_ACCOUNT_NUMBER' => $result['wallet']
					));

					if($initOutput) {
						$historyId = $payeer->output();
						if($historyId > 0) {
							$result['status'] = 'success';
							return $result;
						} else {
							ob_start();
							echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
							$result['error'] = ob_get_contents();
							ob_end_clean();
							return $result;
						}
					} else {
						ob_start();
						echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
						$result['error'] = ob_get_contents();
						ob_end_clean();
						return $result;
					}
				} else {
					ob_start();
					echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					$result['error'] = ob_get_contents();
					ob_end_clean();
					return $result;
				}
			break;
			case 'payeer':
				require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$accountNumber = $data['config']['payments']['payeer']['wallet'];
				$apiId = $data['config']['payments']['payeer']['api_id'];
				$apiKey = $data['config']['payments']['payeer']['api_key'];
				$payeer = new CPayeerLib($accountNumber, $apiId, $apiKey);
				if ($payeer->isAuth()) {
					$arTransfer = $payeer->transfer(array(
						'curIn' => $result['valute'],
						'sum' => $result['amount'],
						'curOut' => $result['valute'],
						'to' => $result['wallet'],
						//'to' => '79788901271', 
						//'to' => 'P9923490',
						'comment' => $result['comment'],
						//'anonim' => 'Y',
						//'protect' => 'Y',
						//'protectPeriod' => '3',
						//'protectCode' => '12345',
					));
					if (!empty($arTransfer['historyId'])) {
						$result['status'] = 'success';
						return $result;
					}
					else {
						ob_start();
                		echo '<pre>'.print_r($arTransfer["errors"], true).'</pre>';
                		$result['error'] = ob_get_contents();
                		ob_end_clean();
						return $result;
					}
				}
				else {
				    ob_start();
					echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					$result['error'] = ob_get_contents();
					ob_end_clean();
					return $result;
				}
			break;
			case 'perfectmoney':
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['payment_id'] = $data['payment_id'];
				
				$f = @fopen('https://perfectmoney.is/acct/confirm.asp?AccountID='.$data['config']['payments']['pm']['account_id'].'&PassPhrase='.$data['config']['payments']['pm']['pass_phrase'].'&Payer_Account='.$data['config']['payments']['pm']['wallet'].'&Payee_Account='.$result['wallet'].'&Amount='.$result['amount'].'&PAY_IN='.$result['amount'].'&PAYMENT_ID='.$result['payment_id'].'', 'rb');
					
				if($f === false){
					$result['error'] = 'Error openning url';
					return $result;
				}

				// getting data
				$out = array(); $out = "";
				while(!feof($f)) $out .= fgets($f);

				fclose($f);

				// searching for hidden fields
				if(!preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $out, $res, PREG_SET_ORDER)){
					$result['error'] = 'Ivalid output';
					return $result;
				}

				foreach($res as $item){
					$key=$item[1];
					$ar[$key]=$item[2];
				}

				if(isset($ar['ERROR'])) {
					$result['error'] = $ar['ERROR'];
					return $result;
				}

				$result['status'] = 'success';
				return $result;
			break;
			case 'tele2':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ PAYEER
				require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$accountNumber = $data['config']['payments']['payeer']['wallet'];
				$apiId = $data['config']['payments']['payeer']['api_id'];
				$apiKey = $data['config']['payments']['payeer']['api_key'];
				$payeer = new CPayeerLib($accountNumber, $apiId, $apiKey);
				if($payeer->isAuth()) {
					$initOutput = $payeer->initOutput(array(
						'ps' => $data['config']['payments']['tele2']['wallet'],
						//'sumIn' => 1,
						'curIn' => $result['valute'],
						'sumOut' => $result['amount'],
						'curOut' => $result['valute'],
						'param_ACCOUNT_NUMBER' => $result['wallet']
					));

					if($initOutput) {
						$historyId = $payeer->output();
						if($historyId > 0) {
							$result['status'] = 'success';
							return $result;
						} else {
							ob_start();
							echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
							$result['error'] = ob_get_contents();
							ob_end_clean();
							return $result;
						}
					} else {
						ob_start();
						echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
						$result['error'] = ob_get_contents();
						ob_end_clean();
						return $result;
					}
				} else {
					ob_start();
					echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					$result['error'] = ob_get_contents();
					ob_end_clean();
					return $result;
				}
			break;
			case 'qiwi':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ PAYEER
				require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$accountNumber = $data['config']['payments']['payeer']['wallet'];
				$apiId = $data['config']['payments']['payeer']['api_id'];
				$apiKey = $data['config']['payments']['payeer']['api_key'];
				$payeer = new CPayeerLib($accountNumber, $apiId, $apiKey);
				if($payeer->isAuth()) {
					$initOutput = $payeer->initOutput(array(
						'ps' => $data['config']['payments']['qiwi']['wallet'],
						//'sumIn' => 1,
						'curIn' => $result['valute'],
						'sumOut' => $result['amount'],
						'curOut' => $result['valute'],
						'param_ACCOUNT_NUMBER' => $result['wallet']
					));

					if($initOutput) {
						$historyId = $payeer->output();
						if($historyId > 0) {
							$result['status'] = 'success';
							return $result;
						} else {
							ob_start();
							echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
							$result['error'] = ob_get_contents();
							ob_end_clean();
							return $result;
						}
					} else {
						ob_start();
						echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
						$result['error'] = ob_get_contents();
						ob_end_clean();
						return $result;
					}
				} else {
					ob_start();
					echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					$result['error'] = ob_get_contents();
					ob_end_clean();
					return $result;
				}
			break;
			case 'yandexmoney':
				
				// ИСПОЛЬЗУЕТСЯ ШЛЮЗ PAYEER
				require_once(DIR_ROOT.'/lib/payments/Cpayeer.php');
				
				$result['amount'] = Lib_Main::clear_num($data['amount'], 2);
				$result['valute'] = strtoupper($data['valute']);
				$result['wallet'] = $data['wallet'];
				$result['comment'] = $data['comment'];
				
				$accountNumber = $data['config']['payments']['payeer']['wallet'];
				$apiId = $data['config']['payments']['payeer']['api_id'];
				$apiKey = $data['config']['payments']['payeer']['api_key'];
				$payeer = new CPayeerLib($accountNumber, $apiId, $apiKey);
				if($payeer->isAuth()) {
					$initOutput = $payeer->initOutput(array(
						'ps' => $data['config']['payments']['yandexmoney']['wallet'],
						//'sumIn' => 1,
						'curIn' => $result['valute'],
						'sumOut' => $result['amount'],
						'curOut' => $result['valute'],
						'param_ACCOUNT_NUMBER' => $result['wallet']
					));

					if($initOutput) {
						$historyId = $payeer->output();
						if($historyId > 0) {
							$result['status'] = 'success';
							return $result;
						} else {
							ob_start();
							echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
							$result['error'] = ob_get_contents();
							ob_end_clean();
							return $result;
						}
					} else {
						ob_start();
						echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
						$result['error'] = ob_get_contents();
						ob_end_clean();
						return $result;
					}
				} else {
					ob_start();
					echo '<pre>'.print_r($payeer->getErrors(), true).'</pre>';
					$result['error'] = ob_get_contents();
					ob_end_clean();
					return $result;
				}
			break;
			default:
				throw new Exception('error');
			break;
		}
	}
}