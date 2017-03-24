<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Black_Login extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
	/**
	 * ADMIN
	 */
	
	// Проверка нахождения логина в списке
    public function check($data) {
        $result = Lib_Db::query("	SELECT * FROM `".$this->table."`
									WHERE `login`='".$data['login']."'");
        return $result[0];
    }
	
	// Получение записей
    public function get() {
        $result = Lib_Db::query("   SELECT * FROM `".$this->table."`
									ORDER BY `id` DESC");
        return $result;
    }
}