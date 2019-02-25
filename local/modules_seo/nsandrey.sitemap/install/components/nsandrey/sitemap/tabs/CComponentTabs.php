<?
class CComponentTabs
{
	public $arTabs;
	
	public function CComponentTabs($show_all_title = '')
	{
		if($show_all_title != '')
			$this->arTabs = array(array('NAME' => (SITE_CHARSET == 'windows-1251' ? iconv('WINDOWS-1251', 'UTF-8', $show_all_title) : $show_all_title), 'GROUPS' => array()));
		else
			$this->arTabs = array();
	}
	
	public function addTab($tab_id, $tab_title, $group_ids = array())
	{
		$this->arTabs[$tab_id] = array('NAME' => (SITE_CHARSET == 'windows-1251' ? iconv('WINDOWS-1251', 'UTF-8', $tab_title) : $tab_title), 'GROUPS' => $group_ids);
	}
	
	public function addGroupToTab($tab_id, $group_id)
	{
		$this->arTabs[$tab_id]['GROUPS'][] = $group_id;
	}
	
	public function init(&$arParams)
	{
		global $APPLICATION;

		$arTabs = array();

		echo '<style>.bx-width100 > tbody > tr{display:none;}</style>';

		foreach($this->arTabs as $val)
			$arTabs[] = $val;

		$arParams['PARAMETERS']['COMPONENT_TABS'] = array(
			'NAME' => 'Tabs',
			'TYPE' => 'CUSTOM',
			'JS_FILE' => str_replace(rtrim($_SERVER['DOCUMENT_ROOT'], '/'), '', dirname(__FILE__)).'/tabs.js',
			'JS_EVENT' => 'OnLoadComponentTabs',
			'JS_DATA' => json_encode($arTabs),
			'PARENT' => 'BASE',
			'HIDDEN' => 'Y'
		);
	}
}
?>
