<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage siteshouse.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class BasketUserFunc {

	public static function addItemBasket($prodId, $quant = 1, $prodDesc = false, $siteId = false, $arPrice = false, $arDiscount = false, $userId = false){
		
		global $USER;
		
		if(intval($prodId)==0) return false;
		
		if(!$siteId) $siteId = SITE_ID;
		
		$arFields = array();
		
		//пользователь корзины
		$ASZ_UID = \Mlife\Asz\BasketUserFunc::getAszUid();
		if(!$ASZ_UID) {
			$ASZ_UID = \Mlife\Asz\BasketUserFunc::setAszUid();
		}else{
		
			//проверить дубль записи, если есть добавить +1
			$res = \Mlife\Asz\BasketTable::getList(
				array(
					'select' => array("ID","QUANT"),
					'filter' => array("PROD_ID"=>intval($prodId),"PROD_DESC"=>$prodDesc,"USERID"=>$ASZ_UID),
					'limit' => 1,
				)
			);
			if($ar = $res->Fetch()){
				return array('error'=>Loc::getMessage("MLIFE_ASZ_BASKET_CARTADDED"));
				//return BasketUserFunc::updateItemQuantBasket($ar["ID"],($ar["QUANT"]+$quant));
			}
		
		}
		
		$arFields["PROD_ID"] = intval($prodId);
		$arFields["SITE_ID"] = $siteId;
		$arFields["QUANT"] = $quant;
		$arFields["USERID"] = $ASZ_UID;
		$arFields["UPDATE"] = time();
		$arFields["PROD_DESC"] = $prodDesc;
		
		//скидка
		if(is_array($arDiscount)) {
			if(isset($arDiscount["VAL"], $arDiscount["CUR"], $arDiscount["ID"])){
				$arFields["DISCOUNT_VAL"] = $arDiscount["VAL"];
				$arFields["DISCOUNT_CUR"] = $arDiscount["CUR"];
			}
		}
		
		//цена
		if(is_array($arPrice)) {
			if(isset($arPrice["VAL"], $arPrice["CUR"], $arPrice["ID"])){
				$arFields["PRICE_VAL"] = $arPrice["VAL"];
				$arFields["PRICE_CUR"] = $arPrice["CUR"];
			}
		}else{
			
			//получаем типы цен для групп текущего пользователя
			$arGroups = $USER->GetUserGroupArray();
			
			if(is_array($arGroups)){
				$priceTip = \Mlife\Asz\CurencyFunc::getPriceForGroup($arGroups,SITE_ID);
			}else{
				$priceTip = \Mlife\Asz\CurencyFunc::getPriceForGroup();
			}
			
			//получаем цены
			$price = \Mlife\Asz\CurencyFunc::getPriceBase($priceTip,array($prodId),SITE_ID);
			if(isset($price[$prodId]["VALUE"],$price[$prodId]["CUR"])){
				$arFields["PRICE_VAL"] = $price[$prodId]["VALUE"];
				$arFields["PRICE_CUR"] = $price[$prodId]["CUR"];
			}
			
		}
		
		\CModule::IncludeModule('iblock');
		$arProd = \CIBlockElement::GetByID($prodId)->GetNext();
		if(!$arProd) return array('error'=>Loc::getMessage('MLIFE_ASZ_BASKET_CARTADDED2'));
		$arFields["PROD_NAME"] = $arProd["NAME"];
		$arFields["PROD_LINK"] = $arProd["DETAIL_PAGE_URL"];
		
		//TODO подумать с зависимыми товарами PARENT_PROD_ID
		
		$res = \Mlife\Asz\BasketTable::add($arFields);
		if($res->isSuccess()){
			return $res->getId();
		}else{
			return array('error'=>$res->getErrors());
		}
		
	}
	
	//обновляет количество товара
	public static function updateItemQuantBasket($id,$quant){
		if($quant==0) {
			\Mlife\Asz\BasketTable::delete($id);
		}else{
			$res = \Mlife\Asz\BasketTable::update($id,
				array("UPDATE"=>time(),"QUANT"=>$quant)
			);
			if($res->isSuccess()) {
				return $id;
			}else{
				return array('error'=>$res->getErrors());
			}
			
		}
		return $id;
	}
	
	//получаем пользака корзины
	public static function getAszUid(){
		
		global $APPLICATION;
		$ASZ_UID = $APPLICATION->get_cookie("ASZ_UID");
		
		if(!$ASZ_UID) {
			$ASZ_UID = $_SESSION["ASZ_UID"];
			if($ASZ_UID) $APPLICATION->set_cookie("ASZ_UID", $ASZ_UID);
		}else{
			$_SESSION["ASZ_UID"] = $ASZ_UID;
		}
		
		return $ASZ_UID;
	}
	
	//создание нового пользака корзины
	public static function setAszUid(){
	
		global $APPLICATION;
		
		$res = \Mlife\Asz\UserTable::add(array("TIME"=>time(),"BX_UID"=>false,"SITE_ID"=>SITE_ID));
		
		if(!$res->isSuccess()) return false;

		$ASZ_UID = $res->getId();
		
		$_SESSION["ASZ_UID"] = $ASZ_UID;
		
		$APPLICATION->set_cookie("ASZ_UID", $ASZ_UID);

		return $ASZ_UID;
		
	}
	
	//удаление позиции корзины по id товара
	public static function deleteItemBasket($prodId,$id=false){
		
		if($id) {
			$res = \Mlife\Asz\BasketTable::delete($id);
			return $res;
		}
		
		//TODO подумать с зависимыми товарами PARENT_PROD_ID
		
		$ASZ_UID = \Mlife\Asz\BasketUserFunc::getAszUid();
		
		$entity = \Mlife\Asz\BasketTable::getEntity();
		$result = new \Bitrix\Main\Entity\Result();
		
		if(intval($prodId)<1 && $ASZ_UID) return false;
		
		// delete
		$connection = \Bitrix\Main\Application::getConnection();
		$helper = $connection->getSqlHelper();

		$tableName = $entity->getDBTableName();

		$where = 'USERID='.$ASZ_UID.' AND ORDER_ID>0 AND (PARENT_PROD_ID='.$prodId.' OR PROD_ID='.$prodId.')';

		$sql = "DELETE FROM ".$tableName." WHERE ".$where;
		
		$connection->queryExecute($sql);

		return $result;
		
	}

}