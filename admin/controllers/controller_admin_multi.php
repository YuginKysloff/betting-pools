<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Admin_Multi {

    public function __construct($data) {
        $this->data = $data;

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_fill.php');
        require_once(DIR_ROOT.'/models/model_multi.php');
        require_once(DIR_ROOT.'/models/model_payout.php');
        require_once(DIR_ROOT.'/models/model_users.php');

        // Определение объектов
        $this->obj['model_fill'] = new Model_Fill();
        $this->obj['model_multi'] = new Model_Multi();
        $this->obj['model_payout'] = new Model_Payout();
        $this->obj['model_users'] = new Model_Users();
    }

    public function index() {

        // Формирование вида
        $page = Core_View::load('multi', $this->data);
        return $page;
    }

    public function get_list() {

        $this->data['result']['archive'] = Lib_Main::clear_num($this->data['route']['param']['archive'] ?? 0, 0);
        if($this->data['result']['archive'] < 0 || $this->data['result']['archive'] > 1) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.($this->data['user']['login'] ?? '#empty').' &#8594 '.__FILE__.' ('.__LINE__.')', 'datetime' => time()]);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }

        $count = $this->obj['model_multi']->cnt(['archive' => $this->data['result']['archive']]);

		if($count > 0) {
			$per_page = 2;
			$start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
			if($start_row < 0 || $start_row >= $count) $start_row = 0;

			// Получение first'ов
			$this->data['result']['first'] = $this->obj['model_multi']->get_first(['archive' => $this->data['result']['archive'], 'start_row' => $start_row, 'per_page' => $per_page]);
			if($this->data['result']['first']) {
				foreach($this->data['result']['first'] as &$val) {
					$val['usd'] = Lib_Main::beauty_number($val['usd']);
					$val['sponsor'] = $val['sponsor'] ?? 'admin';
					$val['datetime'] = date($this->data['config']['formats']['admin_datetime'], $val['datetime'] + ($this->data['user']['timezone'] * 60));
					$val['activity'] = date($this->data['config']['formats']['admin_datetime'], $val['activity'] + ($this->data['user']['timezone'] * 60));
					$val['refs'] = $this->obj['model_users']->ref_count(['sponsor_id' => $val['first']]);
					foreach($this->data['config']['valutes'] as $item => $key) {
						$val[$item.'_fill'] = 0;
						$val[$item.'_payout'] = 0;
					}
					$fills = $this->obj['model_fill']->user_all(['user_id' => $val['first']]);
					if($fills) {
						foreach($fills as $fill_val) {
							$val[$fill_val['valute'].'_fill'] = Lib_Main::beauty_number($fill_val['amount'] ?? 0);
						}
					}
					$payout = $this->obj['model_payout']->user_all(['user_id' => $val['first']]);
					if($payout) {
						foreach($payout as $payout_val) {
							$val[$payout_val['valute'].'_payout'] = Lib_Main::beauty_number($payout_val['amount'] ?? 0);
						}
					}

					// Получение second'ов
					$val['second'] = $this->obj['model_multi']->get_second(['archive' => $this->data['result']['archive'], 'first' => $val['first']]);
					if($val['second']) {
						foreach($val['second'] as &$second_val) {
							$second_val['usd'] = Lib_Main::beauty_number($second_val['usd']);
							$second_val['sponsor'] = $second_val['sponsor'] ?? 'admin';
							$second_val['datetime'] = date($this->data['config']['formats']['admin_datetime'], $second_val['datetime'] + ($this->data['user']['timezone'] * 60));
							$second_val['activity'] = date($this->data['config']['formats']['admin_datetime'], $second_val['activity'] + ($this->data['user']['timezone'] * 60));
							$second_val['refs'] = $this->obj['model_users']->ref_count(['sponsor_id' => $second_val['id']]);
							foreach($this->data['config']['valutes'] as $item => $key) {
								$second_val[$item.'_fill'] = 0;
								$second_val[$item.'_payout'] = 0;
							}
							$fills = $this->obj['model_fill']->user_all(['user_id' => $second_val['id']]);
							if($fills) {
								foreach($fills as $fill_val) {
									$second_val[$fill_val['valute'].'_fill'] = Lib_Main::beauty_number($fill_val['amount'] ?? 0);
								}
							}
							$payout = $this->obj['model_payout']->user_all(['user_id' => $second_val['id']]);
							if($payout) {
								foreach($payout as $payout_val) {
									$second_val[$payout_val['valute'].'_payout'] = Lib_Main::beauty_number($payout_val['amount'] ?? 0);
								}
							}
						}
					}
				}
				$this->data['result']['pagination'] = ($count > $per_page) ? Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/'.$this->data['config']['site']['admin'].'/multi/get_list/archive/'.$this->data['result']['archive'], 'get_list', '#multi__list') : null;
			}
		}
        $response['multi__list'] = Core_View::load('multi_list', $this->data);
        return $response;
    }

    // Добавление в архив
    public function action() {

		// Получение и проверка входящих данных
        if(!isset($this->data['route']['param']['user_id']) || !isset($this->data['route']['param']['type']) || !isset($this->data['route']['param']['result']) || !isset($this->data['route']['param']['action'])) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-fill|wrong-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }
        $user_id = Lib_Main::clear_num($this->data['route']['param']['user_id'] ?? 0, 0);
        $type = Lib_Main::clear_str($this->data['route']['param']['type'] ?? '');
        $action = Lib_Main::clear_str($this->data['route']['param']['action'] ?? '');
		$result = Lib_Main::clear_str(urldecode($this->data['route']['param']['result'] ?? ''));

        if($type !== 'first' && $type !== 'second') {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-fill|wrong-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }
		if($action !== 'archive' && $action !== 'delete') {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-fill|wrong-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}
		if(empty($result)) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Получение списка для архива или удаления
		$multi = ($type == 'first') ? $this->obj['model_multi']->check_first(['user_id' => $user_id, 'type' => $type]) :  $this->obj['model_multi']->check_second(['id' => $user_id]);
		if(!$multi) {
			Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-fill|wrong-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
			$response['handler'] = Lib_Main::rew_page('errors/data');
			return $response;
		}

		// Проверка наличия прав пользователя на изменение
		if($this->data['user']['access'] <= $multi['access']) {
			$message['error'][] = 'Ошибка';
			$response[$result] = Core_View::message($message);
			$response['handler'] = '<script>messager("error", "Нет прав");</script>';
			return $response;
		}

		if($action == 'archive') {
			// Обновление значения архива в базе
			($type == 'first') ? $this->obj['model_multi']->update(['set' => ['archive' => '1'], 'where' => [[$type, '=', $user_id], ['archive', '=', '0']]]) :  $this->obj['model_multi']->update(['set' => ['archive' => '1'], 'where' => $user_id]);
			$response['handler'] = '<script>$("#multi__'.$type.'_'.$user_id.', .multi__'.$type.'_'.$user_id.'").fadeOut(500);</script>';
		} else {
			($type == 'first') ? $this->obj['model_multi']->delete(['where' => [[$type, '=', $user_id]]]) : $this->obj['model_multi']->delete(['where' => $user_id]);
			$response['handler'] = '<script>$("#multi__'.$type.'_'.$user_id.', .multi__'.$type.'_'.$user_id.'").fadeOut(500);</script>';
		}
		return $response;
    }
}