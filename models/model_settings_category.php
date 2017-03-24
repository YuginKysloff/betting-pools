<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Settings_Category extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
	/**
	 * ADMIN, core_settings, core_security
	 */
	
	// Список категорий настроек
	public function get() {
        $result = Lib_Db::query("  	SELECT * 
									FROM `".$this->table."`
									ORDER BY `sort`");
        return $result;
    }
	
	// Проверка существования ключа в данной категории
	public function check_key($data) {
        $result = Lib_Db::query("  	SELECT `id` 
									FROM `".$this->table."`
									WHERE `name` = '".$data['name']."'");
        return $result;
    }
	
	// Проверка существования записи
	public function check($data) {
        $result = Lib_Db::query("  	SELECT `id` 
									FROM `".$this->table."`
									WHERE `id` = '".$data['id']."'");
        return $result;
    }
	
	// Проверка существования подкатегорий
	public function subcat($data){
        $result = Lib_Db::query("  	SELECT `id` 
									FROM `".$this->table."`
									WHERE `parent_id` = '".$data['parent_id']."'");
        return $result;
    }
}