<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Wallets extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}

	// Проверка существования кошелька в базе
	public function check_wallet($data) {
		$result = Lib_Db::query("   SELECT `user_id` 
   									FROM `".$this->table."`
				                    WHERE `wallet` = '".$data['wallet']."'");
		return $result[0];
	}
	
	/**
	 * ADMIN
	 */
	
	// Кошелек по пользователю и ЭПС
    public function wallet($data) {
        $result = Lib_Db::query("   SELECT `wallet` 
									FROM `".$this->table."`
				                    WHERE `user_id`='".$data['user_id']."'
						            AND `payment`='".$data['payment']."'");
        return $result[0]['wallet'];
    }
	
	// Все кошельки пользователя
    public function get($data) {
        $result = Lib_Db::query("   SELECT *
									FROM `".$this->table."`
				                    WHERE `user_id`='".$data['user_id']."'");
        return $result;
    }
}