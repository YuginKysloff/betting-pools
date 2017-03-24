<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Lib_Main {
    
    // Очистка строки
    public static function clear_str($str) {
        $str = str_replace("`", "", $str);
        $str = trim(strip_tags($str));
        $str = stripslashes($str);
        $str = htmlspecialchars($str, ENT_QUOTES);
        return $str;
    }
	
	// Очистка текста
    public static function clear_text($str) {
        $str = str_replace("`", "", $str);
        $str = stripslashes($str);
        $str = htmlspecialchars($str, ENT_QUOTES);
        return $str;
    }

    // Красивые числа
    public static function beauty_number($number = 0, $col = 2) {
		$negative = substr($number, 0, 1) == '-' ? '-' : '';
        $number = str_replace(',', '.', $number);
		$number = preg_replace('/[^0-9.]+/', '', $number);
		if(preg_match("/\./i", $number)) {
			list($int, $dec) = explode('.', $number);
			$dec = rtrim(mb_substr($dec, 0, $col),"0");
		}
		else {
			$int = $number;
			$dec = 0;
		}
		if($int == '' || $int == '-') $int = 0;
		$int = number_format($int,0,'',' ');
		$number = $dec > 0 && $dec !== "" && $col > 0 ? $int.','.$dec : $int;
		return $negative.$number;
    }
	
    // Обработка чисел
    public static function clear_num($number = 0, $col = 2) {
		$negative = substr($number, 0, 1) == '-' ? '-' : '';
		$number = str_replace(',', '.', $number);
		$number = preg_replace('/[^0-9.]+/', '', $number);
		if(preg_match("/\./i", $number)) {
			list($int, $dec) = explode('.', $number);
		}
		else {
			$int = $number;
			$dec = 0;
		}
		if($int == '' || $int == '-') $int = 0;
		$int = number_format($int,0,'','');
		$number = $col > 0 ? number_format($int.'.'.$dec,$col,'.','') : $int;
		return $negative.$number;
    }
	
	// Убирает все минусы (дефисы)
    public static function abs_num($number = 0) {
		$number = preg_replace('/[\-]+/', '', $number);
		return $number;
	}

    // Перенаправление на страницу
    public static function rew_page($page = '') {
        return '<script>location.href="/'.$page.'";</script>';
    }

    // Обновляем страницу
    public static function refresh() {
        return '<script>location.reload();</script>';
    }
	
	// Вывод страницы (убирает пробелы, комментарии)
	public static function view_page($str) {
        $str = preg_replace('/<!--(.*?)-->/', '', $str);
		$str = str_replace("\n", '', $str);
		$str = str_replace("\t", '', $str);
		$str = preg_replace("/\s{2,}/",' ',$str);
		return $str;
    }

    // Отправка письма
    public static function send_mail($data) {
        /**
         * Метод отправляет сообщение
         * @param string $to - e-mail, на который придет сообщение
         * @param <type> $to_name - Имя получателя
         * @param string $from - e-mail, с которого будет отправлено сообщение
         * @param <type> $from_name - Имя отправителя
         * @param string $subject - тема сообщения
         * @param <type> $message - сообщение
         * @return <type>
         */
        $headers = "From: =?utf-8?b?".base64_encode($data['from_name'])."?= <".$data['from'].">\r\n";
        $headers .= "To: =?utf-8?b?".base64_encode($data['to_name'])."?= <".$data['to'].">\r\n";
        $headers .= "Subject: =?utf-8?b?".base64_encode($data['subject'])."?=\r\n";
        $headers .= "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "Content-Transfer-Encoding: 8bit \r\n";
        $headers .= "X-Mailer: ".$_SERVER['HTTP_HOST']." \r\n";

        return mail($data['to'], $data['subject'], $data['message'], $headers);
    }

	// Определение рода
    public static function plural_form($n, $form1, $form2, $form5) {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20)
            return $form5;
        if ($n1 > 1 && $n1 < 5)
            return $form2;
        if ($n1 == 1)
            return $form1;
        return $form5;
    }

	// Генерация пароля
	public static function generate_password($number) {
		$arr = array('a','b','c','d','e','f',
					 'g','h','i','j','k','l',
					 'm','n','o','p','r','s',
					 't','u','v','x','y','z',
					 'A','B','C','D','E','F',
					 'G','H','I','J','K','L',
					 'M','N','O','P','R','S',
					 'T','U','V','X','Y','Z',
					 '1','2','3','4','5','6',
					 '7','8','9','0');
        
		// Генерируем пароль
		$pass = '';
		for($i = 0; $i < $number; $i++) {
            
			// Вычисляем случайный индекс массива
			$index = rand(0, count($arr) - 1);
			$pass .= $arr[$index];
		}
		return $pass;
	}
	
	// Генерация цифр
	public static function generate_num($number) {
		$arr = array('1','2','3','4','5','6','7','8','9','0');
        
		// Генерируем цифры
		$pass = '';
		for($i = 0; $i < $number; $i++) {
            
			// Вычисляем случайный индекс массива
			$index = rand(0, count($arr) - 1);
			$pass .= $arr[$index];
		}
		return $pass;
	}
	
	// Возвращает  ip
    public static function get_ip() {
        if(isset($_SERVER['HTTP_X_REAL_IP']))
            return $_SERVER['HTTP_X_REAL_IP'];
        return $_SERVER['REMOTE_ADDR'];
    }
	
	// Вывод результата массива из пути
	public static function get_value_array($array, $path){
		$path_arr = explode(" ",$path);
		foreach($path_arr as $p)
		{
			if(!isset($array[$p])) return;
			$array = $array[$p];
		}
		return $array;
	}

	// Замена разметки
	public static function markup($text, $data = null) {

		// Ключи локализации
		if(preg_match_all("/\[m-(.*?)\|(.*?)\]/", $text, $temp)) {
			$string = '';
			foreach($temp[1] as $key => $item) {
				$string[$key] = $data['lang'][$item][$temp[2][$key]] ?? '';
			}
			$text = str_replace($temp[0], $string, $text);
		}
		return $text;
	}

    // Пагинация
    public static function pagination($total, $per_page, $num_links, $start_row, $action = '') {
        $num_pages = ceil($total / $per_page); // Общее число страниц
        if ($num_pages == 1)
            return '';
        $cur_page = $start_row; // Количество элементов на страницы
        if ($cur_page > $total) {
            $cur_page = ($num_pages - 1) * $per_page;
        }
        $output = '';
        $cur_page = floor(($cur_page / $per_page) + 1); // Номер текущей страницы
        $start = (($cur_page - $num_links) > 0) ? $cur_page - $num_links : 0; // Номер стартовой страницы выводимой в пейджинге
        $end = (($cur_page + $num_links) < $num_pages) ? $cur_page + $num_links : $num_pages; // Номер последней страницы выводимой в пейджинге
        $output .= $cur_page > ($num_links + 1) ? '<a href="/'.$action.'" class="pagination__button pagination__button--first pagination__button--enable"></a>' : '<span class="pagination__button pagination__button--first"></span>'; // Формируем ссылку на первую страницу

        // Формируем ссылку на предыдущую страницу
        if ($cur_page != 1) {
            $i = $start_row - $per_page;
            if ($i <= 0)
                $i = 0;
            $output .= '<a href="/'.$action.'/'.$i.'" class="pagination__button pagination__button--prev pagination__button--enable"></a>';
        }
        else {
            $output .='<span class="pagination__button pagination__button--prev"></span>';
        }

        // Формируем список страниц с учетом стартовой и последней страницы   >
        for ($loop = $start; $loop <= $end; $loop++) {
            $i = ($loop * $per_page) - $per_page;

            if ($i >= 0) {
                if ($cur_page == $loop) {

                    // Текущая страница
                    $output .= '<span class="pagination__button pagination__button--active">'.$loop.'</span>';
                } else {

                    $n = ($i == 0) ? '' : $i;

                    $output .= '<a href="/'.$action.'/'.$n.'" class="pagination__button pagination__button--enable">'.$loop.'</a>';
                }
            }
        }

        // Формируем ссылку на следующую страницу
        $output .= $cur_page < $num_pages ? '<a href="/'.$action.'/'.($cur_page * $per_page).'" class="pagination__button pagination__button--next pagination__button--enable"></a>' : '<span class="pagination__button pagination__button--next"></span>';

        // Формируем ссылку на последнюю страницу
        if (($cur_page + $num_links) < $num_pages) {
            $i = (($num_pages * $per_page) - $per_page);
            $output .= '<a href="/'.$action.'/'.$i.'" class="pagination__button pagination__button--last pagination__button--enable"></a>';
        } else {
            $output .='<span class="pagination__button pagination__button--last"></span>';
        }
        return '<div class="pagination">'.$output.'</div>';
    }

    // Пагинация Ajax
    public static function pagination_ajax($total, $per_page, $num_links, $start_row, $action = '', $ident = '', $before = '', $form = '') {
        $num_pages = ceil($total / $per_page); // Общее число страниц
        if ($num_pages == 1)
            return '';
        $cur_page = $start_row; // Количество элементов на страницы
        if ($cur_page > $total) {
            $cur_page = ($num_pages - 1) * $per_page;
        }
        $cur_page = floor(($cur_page / $per_page) + 1); // Номер текущей страницы
        $start = (($cur_page - $num_links) > 0) ? $cur_page - $num_links : 0; // Номер стартовой страницы выводимой в пейджинге
        $end = (($cur_page + $num_links) < $num_pages) ? $cur_page + $num_links : $num_pages; // Номер последней страницы выводимой в пейджинге

        // Формируем ссылку на первую страницу
        $output = '';
        $output .= $cur_page > ($num_links + 1) ? '<span onclick="process(\''.$action.'\',\''.$ident.'\',\''.$before.'\',\''.$form.'\')" class="pagination__button pagination__button--first pagination__button--enable"></span>' : '<span class="pagination__button pagination__button--first"></span>';

        // Формируем ссылку на предыдущую страницу
        if ($cur_page != 1) {
            $i = $start_row - $per_page;
            if ($i <= 0)
                $i = 0;
            $output .= '<span onclick="process(\''.$action.'/p/'.$i.'\',\''.$ident.'\',\''.$before.'\',\''.$form.'\')" class="pagination__button pagination__button--prev pagination__button--enable"></span>';
        }
        else {
            $output .='<span class="pagination__button pagination__button--prev"></span>';
        }

        // Формируем список страниц с учетом стартовой и последней страницы   >
        for ($loop = $start; $loop <= $end; $loop++) {
            $i = ($loop * $per_page) - $per_page;

            if ($i >= 0) {
                if ($cur_page == $loop) {

                    // Текущая страница
                    $output .= '<span class="pagination__button pagination__button--active">'.$loop.'</span>';
                } else {

                    $n = ($i == 0) ? '' : $i;

                    $output .= '<span onclick="process(\''.$action.'/p/'.$n.'\',\''.$ident.'\',\''.$before.'\',\''.$form.'\')" class="pagination__button pagination__button--enable">'.$loop.'</span>';
                }
            }
        }
        $output .= $cur_page < $num_pages ? '<span onclick="process(\''.$action.'/p/'.($cur_page * $per_page).'\',\''.$ident.'\',\''.$before.'\',\''.$form.'\')" class="pagination__button pagination__button--next pagination__button--enable"></span>' : '<span class="pagination__button pagination__button--next"></span>'; // Формируем ссылку на следующую страницу

        // Формируем ссылку на последнюю страницу
        if (($cur_page + $num_links) < $num_pages) {
            $i = (($num_pages * $per_page) - $per_page);
            $output .= '<span onclick="process(\''.$action.'/p/'.$i.'\',\''.$ident.'\',\''.$before.'\',\''.$form.'\')" class="pagination__button pagination__button--last pagination__button--enable"></span>';
        } else {
            $output .='<span class="pagination__button pagination__button--last"></span>';
        }
        return '<div class="pagination">'.$output.'</div>';
    }
}