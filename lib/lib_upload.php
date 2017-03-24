<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Lib_Upload {
	
	// Загрузка изображения
    public static function image($data) {
		
		/**
		 * $data['image'] - Объект изображения (обязательно + обязательно агрументы в массиве : 'name' => Имя, 'size' => Размер (вес), 'type' => Тип, 'tmp_name' => Путь)
		 * $data['name'] - Имя изображения (обязательно)
		 * $data['path'] - Путь к папке с файлами (обязательно)
		 * $data['max_size'] - Максимальный размер изображения
		 * $data['width'] - Ширина изображения
		 * $data['height'] - Высота изображения
		 * $data['ext'] - Расширение сохраняемого файла (определяется автоматически)
		 */
		 
		// Настройки по умолчанию
		$width = 200; // Ширина изображения (в пикселях)
		$height = 200; // Высота изображения (в пикселях)
		$min_size = 20000; // Минимальный размер изображения (в байтах)
		$max_size = 1048576; // Максимальный размер изображения (в байтах)
		
		if(!isset($data['image']) || empty($data['image']) || !isset($data['name']) || empty($data['name']) || !isset($data['path']) || empty($data['path'])) return 'data-empty';
		
		// Переопределение настроек
		if(isset($data['max_size'])) $max_size = $data['max_size'];
		if(isset($data['width'])) $width = $data['width'];
		if(isset($data['height'])) $height = $data['height'];
		
		// Проверка на существование POST данных
		if(!isset($_POST) || $_SERVER['REQUEST_METHOD'] != 'POST') return 'post-empty';

		// Переопределение и проверка данных
		$result['real_name'] = $data['image']['name'] ?? '';
		$result['size'] = $data['image']['size'] ?? '';
		if(!strlen($result['real_name'])) return 'file-empty';
		
		// Проверка типа файла
		if(!in_array($data['image']['type'], ['image/gif', 'image/png', 'image/jpeg'])) return 'error-type';

		// Определение расширения
		$result['ext'] = self::extension($result['real_name']);
		
		// Проверка расширения
		if(!preg_match('/[.](JPG)|(jpg)|(gif)|(GIF)|(png)|(PNG)|(jpeg)|(JPEG)$/', $result['ext'])) return 'not-extension';
		
		// Проверка размера изображения
		if($result['size'] < $min_size) return 'small-size';
		if($result['size'] > $max_size) return 'big-size';
		
		// Имя оригинального файла
		$actual_image_name = $data['name'].'_'.time().'_temp.'.$result['ext'];
		$tmp = $data['image']['tmp_name'];
		
		// Перемещение загруженного изображения
		if(!move_uploaded_file($tmp, $data['path'].$actual_image_name)) return 'moving-error';
		
		// Создание нового изображения
		if(preg_match('/[.](GIF)|(gif)$/', $actual_image_name)) $im = @imagecreatefromgif($data['path'].$actual_image_name);
		if(preg_match('/[.](PNG)|(png)$/', $actual_image_name)) $im = @imagecreatefrompng($data['path'].$actual_image_name);
		if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/', $actual_image_name)) $im = @imagecreatefromjpeg($data['path'].$actual_image_name);
		
		// Проверка на ошибку
		if(!$im) return 'does-not-match';

		// Получение ширины и высоты изображения
		$w_src = imagesx($im);
        $h_src = imagesy($im);
				
		// Создание нового полноцветного изображения
		$dest = imagecreatetruecolor($width, $height);
						
		// Создание заполненного прямоугольника
		imagefilledrectangle($dest, 0, 0, $width, $height, imagecolorallocate($dest, 255, 255, 255));
						
		// Копирование и замена размеров части изображения с пересэмплированием
		if($w_src > $h_src) imagecopyresampled($dest, $im, 0, 0, round((max($w_src, $h_src) - min($w_src, $h_src)) / 2), 0, $width, $height, min($w_src, $h_src), min($w_src, $h_src));
		if($w_src < $h_src) imagecopyresampled($dest, $im, 0, 0, 0, 0, $width, $height, min($w_src, $h_src), min($w_src, $h_src));
		if($w_src == $h_src) imagecopyresampled($dest, $im, 0, 0, 0, 0, $width, $height, $w_src, $w_src);
		
		// Расширение сохраняемого файла
		if(isset($data['ext'])) $result['ext'] = $data['ext'];
		
		// Сохранение изображения
		switch(strtolower($result['ext'])) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($dest, $data['path'].$data['name'].'.jpg');
			break;
			case 'png':
				imagepng($dest, $data['path'].$data['name'].'.png');
			break;
			case 'gif':
				imagegif($dest, $data['path'].$data['name'].'.gif');
			break;
			default:
				return 'error-extension';
			break;
		}
		
		// Удаление оригинала изображения
		unlink($data['path'].$actual_image_name);
		
		$result['avatar'] = $data['name'].'.'.$result['ext'];
		return $result;
    }
	
	// Определение расширения
	public static function extension($str) {
		$i = strrpos($str,".");
		if(!$i) return '';
		$l = strlen($str) - $i;
		$ext = substr($str, ($i + 1), $l);
		return $ext;
	}
	
	// Изменение размера изображения
	public static function imageresize($data) {
		
		/**
		 * $data['infile'] - Входной файл
		 * $data['outfile'] - Выходной файл
		 * $data['width'] - Ширина нового изображения
		 * $data['height'] - Высота нового изображения
		 * $data['ext'] - Расширение сохраняемого файла
		 * $data['quality'] - Качество нового изображения (необязательно)
		 */
		 
		$quality = $data['quality'] ?? 75;
	
		switch(strtolower($data['ext'])) {
			case 'jpg':
			case 'jpeg':
				$orig = @imagecreatefromjpeg($data['infile']);
				if(!$orig) return 'does-not-match';
				$new = imagecreatetruecolor($data['width'], $data['height']);
				imagecopyresampled($new, $orig, 0, 0, 0, 0, $data['width'], $data['height'], imagesx($orig), imagesy($orig));
				imagejpeg($new, $data['outfile'], $quality);
				imagedestroy($orig);
				imagedestroy($new);
			break;
			case 'png':
				$orig = @imagecreatefrompng($data['infile']);
				if(!$orig) return 'does-not-match';
				$new = imagecreatetruecolor($data['width'], $data['height']);
				imagecopyresampled($new, $orig, 0, 0, 0, 0, $data['width'], $data['height'], imagesx($orig), imagesy($orig));
				imagepng($new, $data['outfile'], $quality);
				imagedestroy($orig);
				imagedestroy($new);
			break;
			default:
				return 'error-extension';
			break;
		}
		$result['status'] = 'success';
		return $result;
	}
}