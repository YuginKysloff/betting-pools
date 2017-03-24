<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Controller_Deposits {

    public function __construct($data) {
        $this->data = $data;

        // Закрытый раздел
        if(!$this->data['user']) exit(Lib_Main::rew_page('login'));

        // Подключение моделей
        require_once(DIR_ROOT.'/models/model_deposits.php');
        require_once(DIR_ROOT.'/models/model_log.php');
        require_once(DIR_ROOT.'/models/model_pools.php');
        require_once(DIR_ROOT.'/models/model_users.php');

        // Определение объектов
        $this->obj['model_deposits'] = new Model_Deposits();
        $this->obj['model_log'] = new Model_Log();
        $this->obj['model_pools'] = new Model_Pools();
        $this->obj['model_users'] = new Model_Users();

    }

    // Метод по умолчанию
    public function index() {

        // Получение общей информации по депозитам
        $this->data['result']['deposits_summary'] = $this->obj['model_deposits']->get_sum_by_user(['user_id' => $this->data['user']['id']]);

        // Формирование результатов
        $this->data['result']['deposits_summary']['rest'] = Lib_Main::beauty_number($this->data['result']['deposits_summary']['amount'] - $this->data['result']['deposits_summary']['payout']);
        $this->data['result']['deposits_summary']['amount'] = Lib_Main::beauty_number($this->data['result']['deposits_summary']['amount']);
        $this->data['result']['deposits_summary']['accrued'] = Lib_Main::beauty_number($this->data['result']['deposits_summary']['accrued']);

        // Вывод результатов
        $page = Core_View::load('deposits', $this->data);
        return $page;
    }
    
    // Список депозитов
    public function get_list() {

        // Число депозитов на странице
        $per_page = 10;

        // Общее количество депозитов
        $count = $this->obj['model_deposits']->user_count(['user_id' => $this->data['user']['id']]);
        if($count) {

            // Начальная страница депозитов
            $start_row = Lib_Main::clear_num($this->data['route']['param']['p'] ?? 0, 0);
            if($start_row < 0 || $start_row >= $count) $start_row = 0;

            // Список депозитов
            $this->data['result']['list'] = $this->obj['model_deposits']->user_list(['user_id' => $this->data['user']['id'], 'start_row' => $start_row, 'per_page' => $per_page]);
            if($this->data['result']['list']) {

                // Формирование результатов
                foreach($this->data['result']['list'] as &$val) {

                    // Получение процентов уровня
                    foreach($this->data['config']['marketing']['levels'] as $item) {
                        if($val['pool_amount'] >= $item['min'] && $val['pool_amount'] <= $item['max']) {
                            $val['percent'] = $item['percent'];
                            break;
                        }
                    }

                    // Если депозит не пустой инициализация таймера
                    if($val['amount'] > $val['payout']) {
                        $timer = $val['next'] - time();
                        if($timer > 0) {
                            $val['timer']['hour'] = (int)($timer / 3600);
                            $val['timer']['minute'] = (int)($timer / 60 % 60);
                            $val['timer']['second'] = (int)($timer % 60);
                        }
                    }

                    // Вычисление активной суммы депозита
                    $val['rest'] = Lib_Main::beauty_number($val['amount'] - $val['payout']);

                    // Общая сумма депозита
                    $val['amount'] = Lib_Main::beauty_number($val['amount']);

                    // Сумма начисленной прибыли
                    $val['accrued'] = Lib_Main::beauty_number($val['accrued']);

                    // Вычисление процента комиссии на вывод
                    $val['commission_percent'] = $this->data['config']['marketing']['commission'] - floor((time() - $val['datetime']) / 24 / 60 / 60);
                    if($val['commission_percent'] < 0) $val['commission_percent'] = 0;

                    // Общая сумма пула
                    $val['pool_amount'] = Lib_Main::beauty_number($val['pool_amount']);

                    // Дата создания депозита
                    $val['datetime'] = date($this->data['config']['formats']['datetime'], $val['datetime'] + ($this->data['user']['timezone'] * 60));

                    // Дата следующей выплаты
                    $val['next'] = date($this->data['config']['formats']['datetime'], $val['next'] + ($this->data['user']['timezone'] * 60));
                }

                // Пагинация
                if($count > $per_page) $this->data['result']['pagination'] = Lib_Main::pagination_ajax($count, $per_page, 4, $start_row, '/deposits/get_list', 'list', '#deposits__list');
            }
        }

        // Вывод результатов
        $response['deposits__list'] = Core_View::load('deposits_list', $this->data);
        return $response;
    }
    
    // Выплата средств
    public function payout() {

        // Проверка входящих данных
        if(!isset($_POST['amount']) || !isset($this->data['route']['param']['ident'])) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }

        // Получение валюты
        $valute = 'usd';

        // Получение и обработка суммы
        $amount = Lib_Main::abs_num(Lib_Main::clear_num($_POST['amount'] ?? 0, 2));

        // Получение и обработка id депозита
        $id = Lib_Main::clear_num($this->data['route']['param']['ident'] ?? 0, 0);

        // Проверка наличия депозита
        if(!$deposit = $this->obj['model_deposits']->get_by_id(['id' => $id])) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }

        // Проверка возможности вывода
        if($deposit['amount'] == $deposit['payout']) {
            Core_Security::write_warning(['category' => 1, 'user_id' => $this->data['user']['id'], 'text' => '[m-core--security|fail-request] : '.$this->data['user']['login'].' &#8594 '.__FILE__.' ('.__LINE__.')']);
            $response['handler'] = Lib_Main::rew_page('errors/data');
            return $response;
        }
        
        // Проверка суммы выплаты
        if(!$amount) {
            $message['error'][] = 'Введите сумму';
            $response['deposits__form'.$id.'_error'] = Core_View::message($message);
            return $response;
        }

        // Проверка наличия средств на депозите
        if($amount > ($deposit['amount'] - $deposit['payout'])) {
            $message['error'][] = 'Недостаточно средств на депозите';
            $response['deposits__form'.$id.'_error'] = Core_View::message($message);
            return $response;
        }

        // Проверка на минимальную сумму
		if($deposit['amount'] >= 1 && $amount < 1) {
			$message['error'][] = 'Минимум <span class="g-currency-'.$valute.'">1</span>';
            $response['deposits__form'.$id.'_error'] = Core_View::message($message);
            return $response;
		}

        // Вычисление суммы списания
        $percent = $this->data['config']['marketing']['commission'] - floor((time() - $deposit['datetime']) / 24 / 60 / 60);
		if($percent < 0) $percent = 0;
		$commission = ($percent > 0) ? $amount * ($percent / 100) : 0;
        $payout = $amount - $commission;

        // Прловерка наличия средств
		if($payout < 0.01) {
			$message['error'][] = 'Сумма ниже минимальной';
            $response['deposits__form'.$id.'_error'] = Core_View::message($message);
            return $response;
		}
		
		// Списание суммы с пула
        $this->obj['model_pools']->update(['set' => ['amount' => Lib_Main::clear_num($deposit['pool_amount'] - $amount)], 'where' => $deposit['pool_id']]);

        // Списание суммы с депозита
        $this->obj['model_deposits']->update(['set' => ['payout' => Lib_Main::clear_num($deposit['payout'] + $amount), 'commission' => Lib_Main::clear_num($deposit['commission'] + $commission)], 'where' => $deposit['id']]);

        // Зачисление суммы на баланс пользователя
        $this->obj['model_users']->update(['set' => [$valute => Lib_Main::clear_num($this->data['user'][$valute] + $payout)], 'where' => $this->data['user']['id']]);
        
        // Запись в лог
        $this->obj['model_log']->insert(['user_id' => $deposit['user_id'], 'text' => '[m-deposits|payout] #'.$deposit['id'].' : <span class="g-currency-'.$valute.'">'.Lib_Main::beauty_number($payout).'</span>, [m-deposits|commission] <span class="g-currency-'.$valute.'">'.Lib_Main::beauty_number($commission).'</span> ('.$percent.'%)', 'control' => $valute.'='.($this->data['user'][$valute] + $payout), 'datetime' => time()]);

        // Вывод результатов
        $response = $this->get_list();
        $response['balance__'.$valute] = Lib_Main::beauty_number($this->data['user'][$valute] + $payout);
        return $response;
    }
}