<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Settings extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
	/**
	 * ADMIN, core_settings, core_security
	 */
	
	// Получение списка настроек
	public function get() {
        $result = Lib_Db::query("  	SELECT * 
									FROM `".$this->table."`
									ORDER BY `sort`");
        return $result;
    }
	
	// Проверка наличия записей по категории
	public function value_by_category($data) {
        $result = Lib_Db::query("  	SELECT `id`
									FROM `".$this->table."`
									WHERE `category` = '".$data['category']."'");
        return $result;
    }
	
	// Проверка существования записи
	public function check($data) {
        $result = Lib_Db::query("  	SELECT `id` 
									FROM `".$this->table."`
									WHERE `id` = '".$data['id']."'");
        return $result;
    }
	
	// ID по ключу и категории
	public function get_id_by_name($data) {
        $result = Lib_Db::query("  	SELECT `id`
									FROM `".$this->table."`
									WHERE `category` = '".$data['category']."'
									AND `name` = '".$data['name']."'");
        return $result[0]['id'];
    }
	
	// Настройки категории
	public function get_by_category($data) {
        $result = Lib_Db::query("  	SELECT *
									FROM `".$this->table."`
									WHERE `category` = '".$data['category']."'
									ORDER BY `sort`");
        return $result;
    }
	
	// Последняя сортировка
	public function last_sort() {
        $result = Lib_Db::query("  	SELECT `sort`
									FROM `".$this->table."`
									ORDER BY `sort` DESC
									LIMIT 1");
        return $result[0]['sort'];
    }
}