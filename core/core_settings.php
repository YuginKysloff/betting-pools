<?
/**
 * @package    SFERA.Site
 *
 * @copyright  Copyright (c) 2016-2018 SFERA, Inc. All rights reserved.
 */
 
defined('SW_CONSTANT') or die;

class Core_Settings {
	
	public function __construct() {
		
		// Подключение моделей
        require_once(DIR_ROOT.'/models/model_settings.php');
        require_once(DIR_ROOT.'/models/model_settings_category.php');

        // Определение объектов
        $this->obj['model_settings'] = new Model_Settings();
        $this->obj['model_settings_category'] = new Model_Settings_Category();
	}
	
	// Формирование дерева настроек для сохранения
	public function get_tree() {
		$result['category'] = $this->obj['model_settings_category']->get();
		$result['values'] = $this->obj['model_settings']->get();
		
		if(!$result['category'] || !$result['values']) return false;
		
		foreach($result['category'] as $category) {
			$cats[$category['parent_id']][] = $category;
			$result['categories_name'][$category['id']] = $category['name'];
		}
		
		$tree = $this->buid_tree($cats, $cats[0]);
		$this->fill_tree($tree, $result);

		return $tree;
	}
	
	// Построение дерева
	public function buid_tree(&$array, $parent) {
		$tree = array();
		foreach($parent as $pid => $row) {
			$tmp = null;
			if(isset($array[$row['id']])) {
				$tmp = $this->buid_tree($array, $array[$row['id']]);
			}
			$tree[$row['name']] = $tmp;
		}
		return $tree;
	}
	
	// Заполнение дерева
	public function fill_tree(&$tree, &$result) {
		foreach ($tree as $key => &$item) {
			if (is_array($item)) {
				$this->fill_tree($item, $result);
			}
			foreach($result['values'] as $value) {
				if($key == $result['categories_name'][$value['category']]) {
					if(!isset($item[$value['name']])) {
						$item[$value['name']] = $value['value'];
					} else {
						if(is_array($item[$value['name']])) {
							$item[$value['name']][] = $value['value'];
						} else {
							$tmp = $item[$value['name']];
							$item[$value['name']] = array();
							$item[$value['name']][] = $tmp;
							$item[$value['name']][] = $value['value'];
						}
					}
				}
			}
		}
	}
	
	// Обновление файла JSON
	public function upd_json() {
		file_put_contents(DIR_ROOT.'/json/config.json', json_encode($this->get_tree()), LOCK_EX);
	}
}