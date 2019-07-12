<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Functions {

	//преобразование цен и остатков из системной строки
	public static function getPriceValue($strPrice){
		if(!$strPrice) return array();
		$res = array();
		$tempar = explode('+++',$strPrice);
		foreach($tempar as $val){
			$curAr = explode(':::',$val);
			if(substr($curAr[0],0,3)=="cod"){
				$res['PRICE'][substr($curAr[0],3,10)] = array(
					"VAL" => round($curAr[1],2),
					"CUR" => $curAr[2]
				);
			}elseif(substr($curAr[0],0,3)=="kol"){
				$res['KOL'] = array(
					"KOL" => intval($curAr[1]),
					"ZAK" => intval($curAr[2])
				);
			}
		}
		return $res;
	}
	
	//получение прав на админку магазина для текущей группы пользователя
	public static function GetGroupRightSiteId($code="ADMIN"){
		
		global $USER;
		$arGroups = $USER->GetUserGroupArray();
		
		$res = \Mlife\Asz\OptionsTable::getList(array(
			"select" => array("*"),
			"filter" => array("VALUE"=>$arGroups,"CODE"=>$code),
		));
		$arSites = array();
		while ($arData = $res->Fetch()) {
			$arSites[] = $arData["SITEID"];
		}
		return $arSites;
	}
	
	//замена макросов стандартных почтовых событий для смс
	public function replaceBySms($mess,$arMacros){
		
		if(!$mess || !is_array($arMacros) || empty($arMacros)) return;
		
		$arFinMacros = array();
		foreach($arMacros as $key=>$val){
			$arFinMacros["#".$key."#"] = $val;
		}
		
		$mess = str_replace(array_keys($arFinMacros),$arFinMacros,$mess);
		
		return $mess;
		
	}
	
	/**
	* Метод для проверки номера телефона
	* @param string      $phone    номер телефона для проверки
	* @param boolean     $all      необязательный параметр по умолчанию true (весь мир), false (только снг)
	* @return array                phone - номер без мусора, check - результат проверки(boolean)
	*/
	public function checkPhoneNumber ($phone,$all=true) {
		
		//очистка от лишнего мусора
		$phoneFormat = '+'.preg_replace("/[^0-9A-Za-z]/", "", $phone);
		
		//проверка номера мир
		$pattern_world = "/^\+?([87](?!95[4-79]|99[^2457]|907|94[^0]|336|986)([348]\d|9[0-689]|7[0247])\d{8}|[1246]\d{9,13}|68\d{7}|5[1-46-9]\d{8,12}|55[1-9]\d{9}|55119\d{8}|500[56]\d{4}|5016\d{6}|5068\d{7}|502[45]\d{7}|5037\d{7}|50[457]\d{8}|50855\d{4}|509[34]\d{7}|376\d{6}|855\d{8}|856\d{10}|85[0-4789]\d{8,10}|8[68]\d{10,11}|8[14]\d{10}|82\d{9,10}|852\d{8}|90\d{10}|96(0[79]|17[01]|13)\d{6}|96[23]\d{9}|964\d{10}|96(5[69]|89)\d{7}|96(65|77)\d{8}|92[023]\d{9}|91[1879]\d{9}|9[34]7\d{8}|959\d{7}|989\d{9}|97\d{8,12}|99[^4568]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|9989\d{8}|380[34569]\d{8}|381\d{9}|385\d{8,9}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}|37[6-9]\d{7,11}|30[69]\d{9}|34[67]\d{8}|3[12359]\d{8,12}|36\d{9}|38[1679]\d{8}|382\d{8,9})$/";
		//проверка номера снг
		$pattern_sng = "/^((\+?7|8)(?!95[4-79]|99[^2457]|907|94[^0]|336)([348]\d|9[0-689]|7[07])\d{8}|\+?(99[^456]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|380[34569]\d{8}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}))$/";
		
		if($all) {
			$patt = $pattern_world;
		}
		else {
			$patt = $pattern_sng;
		}
		
		if(!preg_match($patt, $phoneFormat)) {
			return array('phone'=>$phoneFormat,'check'=>false);
		}
		
		return array('phone'=>$phoneFormat,'check'=>true);
	
	}
	
	public static function formatTemplateFilter($template=false,$values=array()){
		
		if(!$template) return "";
		
		$value = false;
		$event = new \Bitrix\Main\Event("mlife.asz", "OnFormatSefFilter",array(
			'template'=>$template,
			'values' => $values,
			));
		$event->send();
		if ($event->getResults()){
			foreach($event->getResults() as $evenResult){
				if($evenResult->getResultType() == \Bitrix\Main\EventResult::SUCCESS){
					$value = $evenResult->getParameters();
				}
			}
		}
		if($value) return $value;
		
		preg_match_all("/({.*?})/",$template,$match);
		if(is_array($match[0])){
			foreach($match[0] as $val){
				if(strpos($val,"this.VALUE")!==false){
					$key = preg_replace("/^(.*?)(this\.VALUE[0-9]+)(.*?)$/is","$2",$val);
					$arValues = $values[$key];
					if(!empty($arValues)){
						if(strpos($val,"=roundotdo")!==false){
							unset($value);
							foreach($arValues as &$value){
								$value = round($value);
							}
							$template = str_replace($key,"от ".$arValues[0]." до ".$arValues[1],$template);
						}
						elseif(strpos($val,"=round")!==false){
							unset($value);
							foreach($arValues as &$value){
								$value = round($value);
							}
							$template = str_replace($key,implode(", ",$arValues),$template);
						}
						elseif(strpos($val,"=otdo")!==false){
							unset($value);
							foreach($arValues as &$value){
								$value = round($value);
							}
							$template = str_replace($key,Loc::getMessage("MLIFE_ASZ_FUNCTIONS_OT")." ".$arValues[0]." ".Loc::getMessage("MLIFE_ASZ_FUNCTIONS_DO")." ".$arValues[1],$template);
						}
						elseif(strpos($val,"=lower")!==false){
							unset($value);
							foreach($arValues as &$value){
								$value = strtolower($value);
							}
							$template = str_replace($key,implode(", ",$arValues),$template);
						}
						else{
							$template = str_replace($key,implode(", ",$arValues),$template);
						}
					}else{
						$template = str_replace($val,"",$template);
					}
				}
			}
			$template = str_replace(
				array("{=round","{=roundotdo","{=otdo","{=lower","}","{="),
				"",
				$template
			);
			return $template;
		}else{
			return "";
		}
		
	}
	
}