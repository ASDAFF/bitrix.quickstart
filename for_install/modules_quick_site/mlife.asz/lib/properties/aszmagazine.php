<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage siteshouse.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz\Properties;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class AszMagazine {
	
	public static $iblock = false;
	public static $siteId = false;
	public static $priceName = array();
	public static $curency = array();
	public static $init = false;
	
	public static function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "mlife_asz_system",
			"DESCRIPTION" => "ASZ system",
			"GetPropertyFieldHtml" => array("\Mlife\Asz\Properties\AszMagazine", "GetPropertyFieldHtml"),
			"GetPublicViewHTML"	=> array("\Mlife\Asz\Properties\AszMagazine", "GetPublicViewHTML"),
			"GetAdminListViewHTML"	=> array("\Mlife\Asz\Properties\AszMagazine", "GetAdminListViewHTML"),
			"ConvertToDB" => array("\Mlife\Asz\Properties\AszMagazine", "ConvertToDB"),
			"ConvertFromDB" => array("\Mlife\Asz\Properties\AszMagazine", "ConvertFromDB"),
		);
	}
	
	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		\CModule::IncludeModule("iblock");
		if($strHTMLControlName["MODE"]=="iblock_element_admin"){
			$dataAr = \Mlife\Asz\Functions::getPriceValue($value['VALUE']);
			if(is_array($dataAr['KOL']) && is_array($dataAr["PRICE"])){
				
				self::$iblock = intval($_REQUEST['IBLOCK_ID']);
				
				if(!self::$siteId){
					$res = CIBlock::GetByID(self::$iblock);
					if($ar_res = $res->GetNext()){
						$siteId = $ar_res["LID"];
						if(is_array($siteId)) $siteId = $ar_res["LID"][0];
					}
					self::$siteId = $siteId;
				}
				$siteId = self::$siteId;
				
				if(empty(self::$priceName)){
					//получаем типы цен для текущего сайта
					$price = \Mlife\Asz\PricetipTable::getList(
						array(
							'select' => array('ID','NAME',"BASE"),
							'filter' => array("LOGIC"=>"OR",array("=SITE_ID"=>$siteId),array("=SITE_ID"=>false)),
						)
					);
					while($arPricedb = $price->Fetch()){
						
						$arPrice[$arPricedb["ID"]] = array(
							self::$priceName[$arPricedb["ID"]] = $arPricedb["NAME"],
						);
					}
					
				}
				
				if(empty(self::$curency)){
					//получаем валюты для текущего сайта
					$curency = \Mlife\Asz\CurencyTable::getList(
						array(
							'select' => array('CODE'),
							'filter' => array("LOGIC"=>"OR",array("=SITEID"=>$siteId),array("=SITEID"=>false)),
							'group' => array("CODE"),
						)
					);
					$arCurency = array();
					while($arCurencydb = $curency->Fetch()){
						self::$curency[] = $arCurencydb["CODE"];
					}
				}
			
				$elementId = preg_replace("/FIELDS\[([0-9]+)\].*/is","$1",$strHTMLControlName["VALUE"]);
				$s .= '<table><tr><td><label>'.Loc::getMessage("MLIFE_ASZ_PROPERTYASZ_RESERV").': </label></td><td>';
				foreach($dataAr['KOL'] as $key=>$val) {
					$s .= '<input size="3" type="text" value="'.$val.'" class="aszSystemList_'.$key.'"> ';
				}
				$s .= '</td></tr>';
				foreach($dataAr['PRICE'] as $key=>$val) {
					$s .= '<tr><td><label>'.self::$priceName[$key].': </label></td><td><input size="8" type="text" value="'.$val['VAL'].'" class="aszSystemList_'.$key.'_VAL">';
					$s .= ' <select class="aszSystemList_'.$key.'_CUR">';
					foreach(self::$curency as $name){
						$selected = "";
						if($val['CUR']==$name){
						$selected = ' selected="selected"';
						}
						$s .= '<option value="'.$name.'"'.$selected.'>'.$name.'</option>';
					}
					$s .= '</select></td></tr>';
				}
				$s .= '</table>';
				
				$s .= '<input type="hidden" class="aszSystemListHidden" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" value="'.$value["VALUE"].'">';
				
				return $s;
			}else{
				$s = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" value="'.$value["VALUE"].'">';
				return $s;
			}
			
			
			
			$s = "";
			
			return $s;
		}
		$s = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="aszSystem" value="'.$value["VALUE"].'">';
		return $s;
	}
	
	public static function GetPublicViewHTML($arProperty, $value, $arParams)
	{
		$s = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="aszSystem" value="'.$value["VALUE"].'">';
		return $s;
	}
	
	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		if($strHTMLControlName["MODE"]=="iblock_element_admin"){
			
			\CModule::IncludeModule("iblock");
			$dataAr = \Mlife\Asz\Functions::getPriceValue($value['VALUE']);
			
			if(is_array($dataAr['KOL']) && is_array($dataAr["PRICE"])){
				
				self::$iblock = intval($_REQUEST['IBLOCK_ID']);
				
				if(!self::$siteId){
					$res = \CIBlock::GetByID(self::$iblock);
					if($ar_res = $res->GetNext()){
						$siteId = $ar_res["LID"];
						if(is_array($siteId)) $siteId = $ar_res["LID"][0];
					}
					self::$siteId = $siteId;
				}
				$siteId = self::$siteId;
				
				if(empty(self::$priceName)){
					//получаем типы цен для текущего сайта
					$price = \Mlife\Asz\PricetipTable::getList(
						array(
							'select' => array('ID','NAME',"BASE"),
							'filter' => array("LOGIC"=>"OR",array("=SITE_ID"=>$siteId),array("=SITE_ID"=>false)),
						)
					);
					while($arPricedb = $price->Fetch()){
						
						$arPrice[$arPricedb["ID"]] = array(
							self::$priceName[$arPricedb["ID"]] = $arPricedb["NAME"],
						);
					}
					
				}
			
				$elementId = preg_replace("/FIELDS\[([0-9]+)\].*/is","$1",$strHTMLControlName["VALUE"]);
				$s .= '<table style="min-width:240px;"><tr><td><label>'.Loc::getMessage("MLIFE_ASZ_PROPERTYASZ_RESERV").': </label></td><td>';
				foreach($dataAr['KOL'] as $key=>$val) {
					$s .= $val.' / ';
				}
				$s .= '</td></tr>';
				foreach($dataAr['PRICE'] as $key=>$val) {
					$s .= '<tr><td><label>'.self::$priceName[$key].': </label></td><td>';
					$s .= \Mlife\Asz\CurencyFunc::priceFormat($val['VAL'],$val["CUR"],$siteId);
					$s .= '</td></tr>';
				}
				$s .= '</table>';
				
				if(!self::$init){
					
					$jspricer = "";
					foreach($dataAr['PRICE'] as $key=>$val) {
						$jspricer .= 'cod'.$key.':::"+$(this).find(".aszSystemList_'.$key.'_VAL").val()+":::"+$(this).find(".aszSystemList_'.$key.'_CUR").val()+"+++';
					}
					
					$jskolr = 'kol:::"+$(this).find(".aszSystemList_KOL").val()+":::"+$(this).find(".aszSystemList_ZAK").val()+"+++';
					
					$jsprice = 'var aszprice = "'.$jspricer.'";';
					$jskol = 'var aszkol = "'.$jskolr.'";';
					
					\CUtil::InitJSCore('jquery');
					$script = '<script>
						$(document).ready(function(){
							$(document).on("click",".adm-btn-save",function(e){
								$("tr.adm-list-table-row").each(function(index){
									'.$jsprice.$jskol.'
									var asz_system = aszkol+aszprice;
									$(this).find(".aszSystemListHidden").val(asz_system);
								});
							});
						});
					</script>';
					$s .= $script;
					self::$init = true;
				}
				
				return $s;
			}else{
				$s = $value['VALUE'];
				return $s;
			}
			
		}
		return $value['VALUE'];
	}

	public static function ConvertFromDB($arProperty, $value)
	{
		$arResult = array('VALUE' => '');
		$arResult['VALUE'] = $value['VALUE'];
		return $arResult;
	}

	public static function ConvertToDB($arProperty, $value)
	{
		$arResult = array('VALUE' => '');
		$arResult['VALUE'] = $value['VALUE'];
		return $arResult;
	}
	
}