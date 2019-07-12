<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class CurencyFunc {
	
	//курсы
	private static $arCurency = null;
	
	//базовая валюта
	private static $baseCurency = null;
	
	//типы цен
	private static $priceTip = array();
	
	//получение минимальных цен на товары, отконвертированных в базовую валюту
	public static function getPriceBase($arPriceTip,$arTovarId,$siteId=null){
	
		if(!is_array($arPriceTip) || !is_array($arTovarId)) return false;
		
		$arCurs = self::getCurs($siteId);
		//print_r($arCurs);
		$baseCur = self::$baseCurency;
		
		if(!$arCurs) return false;
		
		$res = PriceTable::getList(
			array(
				'select' => array("PRICEVAL","PRICECUR","PRODID"),
				'filter' => array("PRODID"=>$arTovarId,"PRICEID"=>$arPriceTip,">PRICEVAL"=>0)
			)
		);
		
		$arResult = array();
		
		while($arRes = $res->Fetch()){
			//print_r($arRes);echo'<br/>';
			if($arRes["PRICECUR"]!==$baseCur){
				if(isset($arCurs["LIST"][$arRes["PRICECUR"]])){
					$priceVal = $arRes["PRICEVAL"] * $arCurs["LIST"][$arRes["PRICECUR"]];
				}else{
					$priceVal = $arRes["PRICEVAL"];
				}
			}else{
				$priceVal = $arRes["PRICEVAL"];
			}
			if(!isset($arResult[$arRes['PRODID']]['VALUE']) || ($arResult[$arRes['PRODID']]['VALUE']>$priceVal)){
				$arResult[$arRes['PRODID']] = array("VALUE"=>$priceVal,"CUR"=>$baseCur,"DISPLAY"=>self::priceFormat($priceVal,$baseCur,$siteId));
			}
			
		}
		
		return $arResult;
		
	}
	
	//пролучение курсов валют
	public static function getCurs($siteId=null){
		
		if(self::$baseCurency!==null) return array("BASE"=>self::$baseCurency,"LIST"=>self::$arCurency);
		
		if($siteId===null){
			$filter = array("=SITEID"=>false);
		}else{
			$filter  = array("LOGIC"=>"OR",array("=SITEID"=>$siteId),array("=SITEID"=>false));
		}
		
		$res = CurencyTable::getList(
			array(
				'select' => array("CODE","BASE","CURS"),
				'filter' => array($filter)
			)
		);
		
		$arCurency = array();
		
		while($arRes = $res->Fetch()){
			if($arRes["BASE"]=="Y"){
				self::$baseCurency = $arRes['CODE'];
			}else{
				$arCurency[$arRes['CODE']] = $arRes["CURS"];
			}
		}
		if(count($arCurency)>0) self::$arCurency = $arCurency;
		
		return array("BASE"=>self::$baseCurency,"LIST"=>self::$arCurency);
		
	}
	
	//конвертация в базовую валюту
	public static function convertBase($price,$cur,$siteId=false) {
		$arCurs = self::getCurs($siteId);
		if(isset($arCurs["LIST"][$cur])){
			$priceVal = $price * $arCurs["LIST"][$cur];
		}else{
			$priceVal = $price;
		}
		return $priceVal;
	}
	
	//конвертация из базовой валюты
	public static function convertFromBase($price,$cur,$siteId=false) {
		$arCurs = self::getCurs($siteId);
		if(isset($arCurs["LIST"][$cur])){
			$priceVal = $price / $arCurs["LIST"][$cur];
		}else{
			$priceVal = $price;
		}
		return $priceVal;
	}
	
	//получить базовую валюту
	public static function getBaseCurency($siteId=false){
		$arCurs = self::getCurs($siteId);
		return $arCurs["BASE"];
	}
	
	//получение доступных типов цен для группы пользователей
	public static function getPricetip($group=false,$siteId=null){
		
		$groupCache = $group;
		if(!$group) $groupCache = 'all';
		
		if(isset(self::$priceTip[$groupCache])) return self::$priceTip[$groupCache];
		
		$filter = array();
		
		if($siteId===null){
			$filter[] = array("=SITE_ID"=>false);
		}else{
			$filter[]  = array("LOGIC"=>"OR",array("=SITE_ID"=>$siteId),array("=SITE_ID"=>false));
		}
		
		if(!$group) {
			$filter[] = array("=PRICETIPRIGHT.IDGROUP"=>false);
		}else{
			$filter[]  = array("LOGIC"=>"OR",array("=PRICETIPRIGHT.IDGROUP"=>$group),array("=PRICETIPRIGHT.IDGROUP"=>false));
		}
		
		$main_query = new \Bitrix\Main\Entity\Query(PricetipTable::getEntity());
		$main_query->setSelect(array("UNIQUE_ID"));
		$main_query->setFilter($filter);
		$main_query->registerRuntimeField("UNIQUE_ID",array('data_type' => 'integer', 'expression' => array('DISTINCT(%s)', 'ID')));
		$res = $main_query->exec();
		
		while($arRes = $res->Fetch()){
			self::$priceTip[$groupCache][] = $arRes['UNIQUE_ID'];
		}
		return self::$priceTip[$groupCache];
		
	}
	
	public function getPriceForGroup($arGroup=false,$siteId=null){
		
		if(!is_array($arGroup)) return self::getPricetip(false,$siteId);
		
		$priceTipArr = array();
		
		foreach($arGroup as $groupid) {
			$tipTemp = self::getPricetip($groupid,$siteId);
			$priceTipArr = array_merge($priceTipArr,$tipTemp);
		}
		
		return array_unique($priceTipArr);
		
	}
	
	public function priceFormat($price,$cur=false,$siteId=false){
		
		if(!$price) $price = 0.00;
		if(!$cur) {
			$ar = self::getCurs($siteId);
			$cur = $ar["BASE"];
		}
		
		$event = new \Bitrix\Main\Event("mlife.asz", "OnPriceFormat",array(
			'price'=>$price,
			'curency' => $cur,
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
		
		$template = "#PRICE# ".$cur;
		
		if($cur=="BUR") {
			$template = "#PRICE# ".Loc::getMessage('MLIFE_ASZ_CURENCY_FUNC_BUR');
		}
		if($cur=="RUB") {
			$template = "#PRICE# ".Loc::getMessage('MLIFE_ASZ_CURENCY_FUNC_RUB');
		}
		if($cur=="USD") {
			$template = '#PRICE# '.Loc::getMessage('MLIFE_ASZ_CURENCY_FUNC_USD');
		}
		
		$arMakros = array();
		
		preg_match("/([0-9]{0,20})\.?([0-9]{0,5})/is",$price,$search);
		
		if($cur=="BUR") {
			$search[1] = round($search[1],-2);
		}
		if($cur=="RUB") {
			$search[1] = round($search[1],0);
		}
		if($cur=="USD") {
			$search[1] = round($search[1],0);
		}
		
		$arMakros["#PRICE#"] = number_format($search[1],0,""," ");
		$arMakros["#KOP#"] = $search[2];
		
		$value = str_replace(array_keys($arMakros), $arMakros, $template);
		
		return $value;
		
	}
}

?>