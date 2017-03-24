<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */

defined('SW_CONSTANT') or die;

class Model_Referral_Income extends Core_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = DB_PREF.strtolower(substr(__CLASS__, 6));
	}
	
    // Получение общего дохода от рефералов
    public function income($data) {
        $result = Lib_Db::query("   SELECT `valute`, SUM(`amount`) AS amount
                                    FROM `".$this->table."`
                                    WHERE `sponsor_id` = '".$data['sponsor_id']."'
                                    GROUP BY `valute`");
        return  $result;
    }

	// Получение списка рефералов
	public function listing($data) {
		$result = Lib_Db::query("   SELECT *
                                    FROM `".$this->table."`
        		                    WHERE `sponsor_id` = '".$data['sponsor_id']."'
                                    LIMIT ".$data['start_row'].", ".$data['per_page']);
		return $result;
	}

    // Получение общего дохода от реферала по ID
    public function ref_income_by_id($data) {
        $result = Lib_Db::query("   SELECT `valute`, SUM(`amount`) AS amount
                                    FROM `".$this->table."`
                                    WHERE `sponsor_id` = '".$data['sponsor_id']."'
                                    AND `user_id` = '".$data['user_id']."'
                                    GROUP BY `valute`");
        return $result;
    }
}