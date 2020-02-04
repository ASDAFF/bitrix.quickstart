<?php
/**
 * Copyright (c) 3/2/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);
class CUserTypeYesNo{
	function GetUserTypeDescription() {
		return array(
			'PROPERTY_TYPE'        => 'S',
			'USER_TYPE'            => 'Checkbox',
			'DESCRIPTION'          => 'Да/Нет (Флажок)',
			'GetAdminListViewHTML' => array( __CLASS__ , 'GetTextVal' ),
			'GetPublicViewHTML'    => array( __CLASS__ , 'GetTextVal' ),
			'GetPropertyFieldHtml' => array( __CLASS__ , 'GetPropertyFieldHtml' ),
			'GetPropertyFieldHtmlMulty' => array( __CLASS__ , 'GetPropertyFieldHtml' ),
			//'AddFilterFields'      => array( __CLASS__ , 'AddFilterFields' ),
			'GetPublicFilterHTML'  => array( __CLASS__ , 'GetFilterHTML' ), 
			'GetAdminFilterHTML'   => array( __CLASS__ , 'GetFilterHTML' ),
			'ConvertToDB'          => array( __CLASS__ , 'ConvertToFromDB' ),
			'ConvertFromDB'        => array( __CLASS__ , 'ConvertToFromDB' ),
			'GetSearchContent'     => array( __CLASS__ , 'GetSearchContent' ),
		);
	}
	   
	function GetTextVal( $arProperty, $value, $strHTMLControlName ){
		return $value['VALUE'] == 'Y' ? 'Да' : 'Нет';
	}   
	
	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
		if( ! array_key_exists( 'VALUE', $value ) && $arProperty['MULTIPLE'] == 'Y' )
		{
			$value = array_shift( $value );
		}
		return '<input type="hidden" name="'.$strHTMLControlName['VALUE'].'" value="N" /><input type="checkbox" name="'.$strHTMLControlName['VALUE'].'" value="Y" '.( $value['VALUE'] == 'Y' ? 'checked="checked"' : '' ).'/>';
	}
	
	/* function AddFilterFields( $arProperty, $strHTMLControlName, &$arFilter, &$filtered ){
		if( isset( $_REQUEST[$strHTMLControlName['VALUE']] ) ){
			$prefix = $_REQUEST[$strHTMLControlName['VALUE']] == 'Y' ? '=' : '!=';
			$arFilter[$prefix.'PROPERTY_'.$arProperty['ID']] = 'Y';
			$filtered = TRUE;
		}
	} */
	
	function GetFilterHTML( $arProperty, $strHTMLControlName ){
		$select = '<select name="'.$strHTMLControlName['VALUE'].'">
			<option value="" >(любой)</option>
			<option value="Y" '.( $_REQUEST[$strHTMLControlName['VALUE']] == 'Y' ? 'selected="selected"' : '' ).'>Да</option>
			<option value="N" '.( $_REQUEST[$strHTMLControlName['VALUE']] == 'N' ? 'selected="selected"' : '' ).'>Нет</option>
		</select>';
		return $select;
	}
	
	function GetSearchContent( $arProperty, $value, $strHTMLControlName ){
		$propId = $arProperty; 
		$propParams = CIBlockProperty::GetByID( $propId )->Fetch();
		return $value['VALUE'] == 'Y' ? $propParams['NAME'] : '';
	}
	
   function ConvertToFromDB($arProperty, $value){
		$value['VALUE'] = $value['VALUE'] == 'Y' ? 'Y' : 'N';      
		return $value;
   }
   
   function GetLength( $arProperty, $value ){
		return 1;
	}
}