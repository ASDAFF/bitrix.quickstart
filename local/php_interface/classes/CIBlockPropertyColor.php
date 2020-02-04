<?php

/**
 * Copyright (c) 3/2/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CIBlockPropertyColor {
	
	/**
	 * CIBlockPropertyColor::GetUserTypeDescription()
	 * Метод возвращает массив описывающий поведение пользовательского свойства.
	 * 
	 * @return
	 */
	public static function GetUserTypeDescription(){

		return array(
			'PROPERTY_TYPE'        		=> 'S',
			'USER_TYPE'            		=> 'SelectColor',
			'DESCRIPTION'          		=> 'Выбор цвета',
			'GetAdminListViewHTML' 		=> array('CIBlockPropertyColor', 'GetTextVal'),
			'GetPublicViewHTML'    		=> array('CIBlockPropertyColor', 'GetTextVal'),
			'GetPropertyFieldHtml' 		=> array('CIBlockPropertyColor', 'GetPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array('CIBlockPropertyColor', 'GetPropertyFieldHtml'),
			'GetPublicEditHTML' 		=> array('CIBlockPropertyColor', 'GetPropertyFieldHtml'),			
			'AddFilterFields'      		=> array('CIBlockPropertyColor', 'AddFilterFields'),
			'GetPublicFilterHTML'  		=> array('CIBlockPropertyColor', 'GetFilterHTML'),
			'GetAdminFilterHTML'   		=> array('CIBlockPropertyColor', 'GetFilterHTML'),
			'ConvertToDB'          		=> array('CIBlockPropertyColor', 'ConvertFromToDB'),
			'ConvertFromDB'        		=> array('CIBlockPropertyColor', 'ConvertFromToDB'),
			'GetSearchContent'     		=> array('CIBlockPropertyColor', 'GetSearchContent'),
		);

	}//\\ GetUserTypeDescription

	/**
	 * CIBlockPropertyColor::GetTextVal()
	 * Отображение в публичной части и адимистративной.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @param mixed $strHTMLControlName
	 * @return
	 */
	public static function GetTextVal($arrProperty, $arrValue, $strHTMLControlName){
		
		// Подключаем JS и CSS
		self::AddHeader();
		
		if (in_array(strlen($arrValue['VALUE']), array(3, 6))) return '<div><div class="customWidgetView" style="float:left;"><div class="colorSelectorView"><div style="background-color:#'.$arrValue['VALUE'].';"></div></div></div><div style="float:left;padding-top: 10px;padding-left: 5px;">#'.$arrValue['VALUE'].'</div><div>';
		else return '<div><div class="customWidgetView" style="float:left;"><div class="colorSelectorView"><div></div></div></div><div style="float:left;padding-top: 10px;padding-left: 5px;">Цвет не выбран</div><div>';
		
	}//\\ GetTextVal

	/**
	 * CIBlockPropertyColor::GetPropertyFieldHtml()
	 * Отображение в форме редактирования.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @param mixed $strHTMLControlName
	 * @return
	 */
	public static function GetPropertyFieldHtml($arrProperty, $arrValue, $strHTMLControlName){

		if (!array_key_exists('VALUE', $arrValue) && $arrProperty['MULTIPLE'] == 'Y')
			$arrValue = array_shift($arrValue);
		
		return self::GetHtmlForEdit($strHTMLControlName['VALUE'], $arrValue['VALUE']);

	}//\\ GetPropertyFieldHtml

	/**
	 * CIBlockPropertyColor::GetFilterHTML()
	 * Выводит html для фильтра по свойству.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $strHTMLControlName
	 * @return
	 */
	public static function GetFilterHTML($arrProperty, $strHTMLControlName){
		
		$strJS = '
	$(\'#filter_prop_color_'.$strHTMLControlName['VALUE'].'\').click(function(){
		if ($(this).is(\':checked\')) {
			$(\'input[name="'.$strHTMLControlName['VALUE'].'"]\').val(\'\').change();
		}
	});	
';
		$strHtml = '<div style="padding-left:8px;padding-right:15px;"><input type="checkbox" id="filter_prop_color_'.$strHTMLControlName['VALUE'].'" value="Y" /> любой цвет</div>'.self::GetHtmlForEdit($strHTMLControlName['VALUE'], '' , $strJS);
		
		return $strHtml;

	}//\\ GetFilterHTML

	/**
	 * CIBlockPropertyColor::GetSearchContent()
	 * Индексация значений.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @param mixed $strHTMLControlName
	 * @return
	 */
	public static function GetSearchContent($arrProperty, $arrValue, $strHTMLControlName){
		
		if (in_array(strlen($arrValue['VALUE']), array(3, 6))) return $arrValue['VALUE'];
		else return '';

	}//\\ GetSearchContent

	/**
	 * CIBlockPropertyColor::ConvertFromToDB()
	 * Сохранение и извлечение в БД.
	 * 
	 * @param mixed $arrProperty
	 * @param mixed $arrValue
	 * @return
	 */
	public static function ConvertFromToDB($arrProperty, $arrValue){
		
		if (!in_array(strlen($arrValue['VALUE']), array(3, 6))) $arrValue['VALUE'] = '';
		
		return $arrValue;
		
	}//\\ ConvertFromToDB


	/**
	 * CIBlockPropertyColor::GetLength()
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
	 * CIBlockPropertyColor::AddHeader()
	 * Добавляет в шаблон скрипты и стили.
	 * 
	 * @return void
	 */
	private static function AddHeader() {
		
		global $APPLICATION;
		
		// Подключаем CSS
		$APPLICATION->SetAdditionalCSS('/bitrix/js/therabbit.iblock_props_color/colorpicker.css');

		// Подключаем JS
		$APPLICATION->AddHeadScript('//code.jquery.com/jquery-1.11.2.min.js');
		$APPLICATION->AddHeadScript('/bitrix/js/therabbit.iblock_props_color/colorpicker.js');
		$APPLICATION->AddHeadScript('/bitrix/js/therabbit.iblock_props_color/eye.js');
		$APPLICATION->AddHeadScript('/bitrix/js/therabbit.iblock_props_color/utils.js');
		
	}//\\ AddHeader
	
	/**
	 * CIBlockPropertyColor::GetHtmlForEdit()
	 * Возвращает HTML для редактирования.
	 * 
	 * @param mixed $strNameInput
	 * @param string $strValue
	 * @return
	 */
	private static function GetHtmlForEdit($strNameInput, $strValue = '', $strJS = ''){
		
		// Подключаем JS и CSS
		self::AddHeader();
		
		$strNameSelector = 'colorSelector_'.str_replace('[', '_', str_replace(']', '', $strNameInput));

		$strHtml = '<div class="customWidget"><div id="'.$strNameSelector.'" class="colorSelector"><div></div></div></div>
<input type="hidden" name="'.$strNameInput.'" value="'.$strValue.'" />
<script>
$(function(){
	$(\'#'.$strNameSelector.'\').parents(\'tr:eq(1)\').find(\'td:eq(0)\').css(\'vertical-align\', \'middle\');
	'.(strlen($strValue) ? '$(\'#'.$strNameSelector.' div\').css(\'backgroundColor\', \'#'.$strValue.'\');' : '').'
	
	$(\'input[name="'.$strNameInput.'"]\').on(\'change\', function(){
		if ($(this).val().length) $(\'#'.$strNameSelector.' div\').css(\'backgroundColor\', \'#\' + $(this).val());
		else $(\'#'.$strNameSelector.' div\').css(\'backgroundColor\', \'\');
	});
	
	
	$(\'#'.$strNameSelector.'\').ColorPicker({
		'.(strlen($strValue) ? 'color: \'#'.$strValue.'\',' : '').'
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$(\'#filter_prop_color_'.$strNameInput.'\').prop(\'checked\', false);
			$(\'#'.$strNameSelector.' div\').css(\'backgroundColor\', \'#\' + hex);
			$(\'input[name="'.$strNameInput.'"]\').val(hex);
		}
	});
'.$strJS.'	
});
</script>
';
		
		return $strHtml;
		
	}//\\ GetHtmlForEdit
	
}//\\ CIBlockPropertyColor