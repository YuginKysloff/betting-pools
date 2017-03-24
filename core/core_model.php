<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Core_Model {
	
	protected function __construct() {
		Lib_Db::instance();
        $this->tables = Lib_Db::instance()->tables;
	}
	
	// Обновление записей
	public function update($data){

		// Проверка на существование таблицы
		if(!isset($this->tables[substr($this->table, strlen(DB_PREF))])) return 'The specified table does not exist';
		
		// Проверка на существование значения
		if(!isset($data['set'])) return 'Missing value';
		
		// Проверка на существование условия
		if(!isset($data['where'])) return 'Missing condition';
		
		// Формирование значений
		$set = '';
		foreach($data['set'] as $key => $val){
			$set .= "`".$key."`='".$val."',";
		}
		$set = rtrim($set, ',');
		
		// Формирование условия
		if(is_array($data['where'])) {
			$where = '';
			foreach($data['where'] as $val){
				$where .= "`".$val[0]."`".$val[1]."'".$val[2]."' AND ";
			}
			$where = rtrim($where, ' AND ');
		} else {
			$where = "`id`='".$data['where']."'";
		}
		$result = Lib_Db::query("	UPDATE ".$this->table."
									SET ".$set."
									WHERE ".$where);
        return $result;
	}
	
	// Добавление записи
	public function insert($data){
		
		// Проверка на существование таблицы
		if(!isset($this->tables[substr($this->table, strlen(DB_PREF))])) return 'The specified table does not exist';
		
		// Проверка на существование данных
		if(!isset($data)) return 'Missing condition';
		
		// Формирование значений
		if(!is_array($data)) return 'Data is not a array';
		$column = $values = '';
		foreach($data as $key => $val){
			$column .= "`".$key."`,";
			$values .= "'".$val."',";
		}
		$column = rtrim($column, ',');
		$values = rtrim($values, ',');
		$result = Lib_Db::query("	INSERT INTO ".$this->table." (".$column.") 
									VALUES (".$values.")");
        return $result;
	}
	
	// Удаление записей
	public function delete($data){
		
		// Проверка на существование таблицы
		if(!isset($this->tables[substr($this->table, strlen(DB_PREF))])) return 'The specified table does not exist';
		
		// Проверка на существование условия
		if(!isset($data['where'])) return 'Missing condition';
		
		// Формирование условия
		if(is_array($data['where'])) {
			$where = '';
			foreach($data['where'] as $val){
				$where .= "`".$val[0]."`".$val[1]."'".$val[2]."' AND ";
			}
			$where = rtrim($where, ' AND ');
		} else {
			$where = "`id`='".$data['where']."'";
		}
		$result = Lib_Db::query("	DELETE FROM ".$this->table."
									WHERE ".$where);
        return $result;
	}
}