<?php
/**
	 * getRegions() - get all regions
	 */
	function getRegions(){
		$res = \Bitrix\Sale\Location\LocationTable::getList(array(
		    'filter' => array('TYPE_CODE'=>'REGION','LANGUAGE_ID'=>'ru'),
		    'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE', 'LANGUAGE_ID'=>'NAME.LANGUAGE_ID')
		));
		$arrRegions = array();
		while($item = $res->fetch()){
		    $arrRegions[] = $item;
		}
		
		return $arrRegions;
	}
>
