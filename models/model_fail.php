<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Fail extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
	// Получение количества неудачных попыток
    public function get_count($data) {
		$result = Lib_Db::query("  	SELECT COUNT(`id`) AS cnt 
									FROM `".$this->table."`
									WHERE `value`='".$data['value']."' AND ('".time()."' - `datetime` < 900)");
        return $result[0]['cnt'];
    }
}