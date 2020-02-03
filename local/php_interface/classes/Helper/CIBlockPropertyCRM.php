<?php
/**
 * Copyright (c) 3/2/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CIBlockPropertyCRM {
	
	/**
	 * CIBlockPropertyCRM::GetUserTypeDescription()
	 * Метод возвращает массив описывающий поведение пользовательского свойства.
	 * 
	 * @return
	 */
	public static function GetUserTypeDescription(){

		return array(
			'PROPERTY_TYPE'        => 'S',
			'USER_TYPE'            => 'SelectCompanyCRM',
			'DESCRIPTION'          => 'Выбор компании из CRM',
			'GetAdminListViewHTML' => array('CIBlockPropertyCRM', 'GetTextVal'),
			'GetPublicViewHTML'    => array('CIBlockPropertyCRM', 'GetTextVal'),
			'GetPropertyFieldHtml' => array('CIBlockPropertyCRM', 'GetPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array('CIBlockPropertyCRM', 'GetPropertyFieldHtml'),
			'GetPublicEditHTML' 	=> array('CIBlockPropertyCRM', 'GetPropertyFieldHtml'),			
			'AddFilterFields'      => array('CIBlockPropertyCRM', 'AddFilterFields'),
			'GetPublicFilterHTML'  => array('CIBlockPropertyCRM', 'GetFilterHTML'),
			'GetAdminFilterHTML'   => array('CIBlockPropertyCRM', 'GetFilterHTML'),
			'ConvertToDB'          => array('CIBlockPropertyCRM', 'ConvertToDB'),
			'ConvertFromDB'        => array('CIBlockPropertyCRM', 'ConvertFromDB'),
			'GetSearchContent'     => array('CIBlockPropertyCRM', 'GetSearchContent'),
		);

	}//\\ GetUserTypeDescription

	/**
	 * CIBlockPropertyCRM::GetTextVal()
	 * Отображение в публичной части и адимистративной.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @param mixed $strHTMLControlName
	 * @return
	 */
	public static function GetTextVal($arrProperty, $arrValue, $strHTMLControlName){
		
		$arrListCRM = self::GetListCompanyCRM();
		
		if (isset($arrListCRM[intval($arrValue['VALUE'])])) 
			return $arrListCRM[intval($arrValue['VALUE'])];
		else 
			return 'Компания не выбрана';
		
	}//\\ GetTextVal

	/**
	 * CIBlockPropertyCRM::GetPropertyFieldHtml()
	 * Отображение в форме редактирования.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @param mixed $strHTMLControlName
	 * @return
	 */
	public static function GetPropertyFieldHtml($arrProperty, $arrValue, $strHTMLControlName){

		$arrListCRM = self::GetListCompanyCRM();
		
		//  if the field is multiple we have to force it to singular
		if(!array_key_exists('VALUE', $arrValue) && $arrProperty['MULTIPLE'] == 'Y')
			$arrValue = array_shift($arrValue);
			
		$strOptions = '<option value="" ></option>';
		foreach ($arrListCRM as $intID => $strTitle) {
			$strOptions .= '<option value="'.$intID.'" '.(intval($arrValue['VALUE']) == $intID ? 'selected="selected"' : '' ).'>'.$strTitle.'</option>';
		}//\\ foreach
		
		return '<select name="'.$strHTMLControlName['VALUE'].'">'.$strOptions.'</select>';

	}//\\ GetPropertyFieldHtml

	/**
	 * CIBlockPropertyCRM::GetFilterHTML()
	 * Выводит html для фильтра по свойству.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $strHTMLControlName
	 * @return
	 */
	public static function GetFilterHTML($arrProperty, $strHTMLControlName){
		
		$arrListCRM = self::GetListCompanyCRM();
		
		$strOptions = '<option value="" >(любой)</option>';
		foreach ($arrListCRM as $intID => $strTitle) {
			$strOptions .= '<option value="'.$intID.'" '.(intval($_REQUEST[$strHTMLControlName['VALUE']]) == $intID ? 'selected="selected"' : '' ).'>'.$strTitle.'</option>';
		}//\\ foreach
		
		return '<select name="'.$strHTMLControlName['VALUE'].'">'.$strOptions.'</select>';

	}//\\ GetFilterHTML

	/**
	 * CIBlockPropertyCRM::GetSearchContent()
	 * Индексация значений.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @param mixed $strHTMLControlName
	 * @return
	 */
	public static function GetSearchContent($arrProperty, $arrValue, $strHTMLControlName){
		
		$arrListCRM = self::GetListCompanyCRM();
		
		if (isset($arrListCRM[intval($arrValue['VALUE'])])) 
			return $arrListCRM[intval($arrValue['VALUE'])];
		else 
			return '';

	}//\\ GetSearchContent

	/**
	 * CIBlockPropertyCRM::ConvertToDB()
	 * Сохранение в БД.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @return
	 */
	public static function ConvertToDB($arrProperty, $arrValue){
		
		$arrValue['VALUE'] = intval($arrValue["VALUE"]);
		
		return $arrValue;
		
	}//\\ ConvertToDB


	/**
	 * CIBlockPropertyCRM::ConvertFromDB()
	 * Извлечение из БД.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @return
	 */
	public static function ConvertFromDB($arrProperty, $arrValue ){

		$arrValue['VALUE'] = intval($arrValue["VALUE"]);

		return $arrValue;

	}//\\ ConvertFromDB

	/**
	 * CIBlockPropertyCRM::GetLength()
	 * Проверка длинны значения.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @return
	 */
	public static function GetLength($arrProperty, $arrValue ){

		return strlen((string)$arrValue["VALUE"]);

	}//\\ GetLength
	
	/**
	 * CIBlockPropertyCRM::GetListCompanyCRM()
	 * Возвращает массив со списком компаний в CRM.
	 * 
	 * @return void
	 */
	private static function GetListCompanyCRM(){
		$arrList = array();
		if (CModule::IncludeModule('crm')){
			
			$objCache = new CPHPCache;
			
			if ($objCache->InitCache(3600, 'CIBlockPropertyCRM')) {

				$arrVars = $objCache->GetVars();
				$arrList = $arrVars['arrList'];

			} else {

				$objRes = CCrmCompany::GetList(array('DATE_CREATE' => 'DESC'), array(), array('ID', 'TITLE'));
				while($arrCompany = $objRes->Fetch()) {
					$arrList[$arrCompany['ID']] = $arrCompany['TITLE'];
				}//\\ while

			}//\\ if

			if($objCache->StartDataCache())
				$objCache->EndDataCache(array('arrList' => $arrList)); 
			
		}//\\ if
		
		return $arrList;
	}//\\ GetListCompanyCRM

}//\\ CIBlockPropertyCRM