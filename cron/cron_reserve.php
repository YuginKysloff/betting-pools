<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

// Установка максимального времени выполнения скрипта (в секундах)
$max_execution_time = ini_get('max_execution_time') ?? 0;

// Подключение к базе
Lib_Db::instance();

// Получение таблиц
$result = Lib_Db::tables();
if(!$result) return;
foreach($result as $val) {
	$tables[] = $val;
}

//Цикл по всем таблицам и формирование данных
$return = '';
foreach($tables as $table) {
	
	// Формирование данных
	$result = Lib_Db::query("SELECT * FROM `".$table."`");

	// Запрос на удаление таблицы
	$return .= 'DROP TABLE IF EXISTS `'.$table.'`;';
	
	// Запрос на создание таблицы
	$create = Lib_Db::query("SHOW CREATE TABLE `".$table."`");
	$return .= "\n\n".$create[0]["Create Table"].";\n\n";

	// Формирование запросов на вставку записей
	if($result) {
		// Блокировка доступа к таблице
		$return .= "LOCK TABLES `".$table."` WRITE;\n\n";
		
		// Запрос на вставку записей
		$return .= "INSERT INTO `".$table."` VALUES";
		$i = 0;
		foreach($result as $val) {
			++$i;
			$return .= "(";
			$values = '';
			foreach($val as $value) {
				$value = addslashes($value);
				$value = str_replace("\n","\\n",$value);
				$values .= "'".$value."',";
			}
			$values = rtrim($values, ',');
			$return .= $values."),";
			if($i > 100000) {
				$i = 0;
				$return = rtrim($return, ',');
				$return .= ";\n";
				$return .= "INSERT INTO `".$table."` VALUES";
			}
		}
		$return = rtrim($return, ',');
		$return .= ";\n\n";
		
		// Разблокировка доступа к таблице
		$return .= "UNLOCK TABLES;";
		$return .= "\n\n\n";
	}
}

// Сохранение в файл
file_put_contents(DIR_ROOT.'/backup/'.date("Y-m-d", time()).'-'.DB_NAME.'-'.time().'.sql', $return);