<?php
namespace Bitrix\EsolImportxml\DataManager;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Discount
{
	protected static $moduleId = 'esol.importxml';
		
	public function __construct($ie=false)
	{
		$this->ie = $ie;
		$this->discountModule = (Loader::includeModule('sale') && (string)\Bitrix\Main\Config\Option::get('sale', 'use_sale_discount_only') == 'Y' ? 'sale' : 'catalog');
	}
	
	public function SaveDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $name, $isOffer = false)
	{
		if(!isset($arFieldsProductDiscount['VALUE'])
			&& !isset($arFieldsProductDiscount['XML_ID'])
			&& !isset($arFieldsProductDiscount['BRGIFT'])) return;
		
		if($this->discountModule=='sale')
		{
			$this->SaveSaleDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $name, $isOffer = false);
			return;
		}
		
		$brgift = false;
		if(isset($arFieldsProductDiscount['BRGIFT']) && Loader::includeModule('sale'))
		{
			$brgift = $arFieldsProductDiscount['BRGIFT'];
			unset($arFieldsProductDiscount['BRGIFT']);
		}
		
		$onlyVal = (bool)(count(array_diff(array_keys($arFieldsProductDiscount), array('VALUE', 'VALUE_TYPE', 'CATALOG_GROUP_IDS')))==0);
		$arSites = $this->GetIblockSite($IBLOCK_ID);
		$lidValues = array();
		if(isset($arFieldsProductDiscount['LID_VALUES']))
		{
			if(is_array($arFieldsProductDiscount['LID_VALUES'])) $lidValues = $arFieldsProductDiscount['LID_VALUES'];
			unset($arFieldsProductDiscount['LID_VALUES']);
		}
		if(!empty($lidValues))
		{
			$arSites = array_intersect($arSites, array_keys($lidValues));
		}
		foreach($arSites as $siteId)
		{
			$this->SaveDiscountGift($ID, $IBLOCK_ID, $brgift, $siteId, $name, $isOffer);
			if(!isset($arFieldsProductDiscount['VALUE'])) continue;
			
			$arFieldsProductDiscount['SITE_ID'] = $siteId;
			$arFieldsProductDiscount['VALUE'] = $this->ie->GetFloatVal($arFieldsProductDiscount['VALUE'], 4);
			$xmlIdProductDiscount = 'PRODUCT_'.$ID.'_'.$arFieldsProductDiscount['SITE_ID'].(is_array($arFieldsProductDiscount['CATALOG_GROUP_IDS']) ? '_'.implode('|', $arFieldsProductDiscount['CATALOG_GROUP_IDS']) : '');
			
			if($onlyVal)
			{
				$arFieldsProductDiscount['XML_ID'] = 'IMPORT_'.$arFieldsProductDiscount['VALUE_TYPE'].'_'.$arFieldsProductDiscount['VALUE'].'_'.$arFieldsProductDiscount['SITE_ID'].(is_array($arFieldsProductDiscount['CATALOG_GROUP_IDS']) ? '_'.implode('|', $arFieldsProductDiscount['CATALOG_GROUP_IDS']) : '');
				
				$findProduct = false;
				$dbRes = \CCatalogDiscount::GetList(array(), array('PRODUCT_ID'=>$ID, '%XML_ID'=>$arFieldsProductDiscount['XML_ID']), false, false, array('ID', 'XML_ID'));
				while($arDiscount = $dbRes->Fetch())
				{
					$suffix = trim(substr($arDiscount['XML_ID'], strlen($arFieldsProductDiscount['XML_ID'])));
					if(strlen($suffix)==0 || preg_match('/^_\d+$/', $suffix)) $findProduct = true;
				}
				
				if(!$findProduct)
				{
					$lastError = '';
					$isUpdate = false;
					$suffix = '';
					$dbRes = \CCatalogDiscount::GetList(array('XML_ID'=>'ASC'), array('%XML_ID'=>$arFieldsProductDiscount['XML_ID']), false, false, array('ID', 'XML_ID', 'CONDITIONS'));
					while(!$isUpdate && ($arDiscount = $dbRes->Fetch()))
					{
						$suffix = trim(substr($arDiscount['XML_ID'], strlen($arFieldsProductDiscount['XML_ID'])));
						if(strlen($suffix)!=0 && !preg_match('/^_\d+$/', $suffix)) continue;
						
						$lastError = '';
						$arCond = unserialize($arDiscount['CONDITIONS']);
						$childrenKey = -1;
						if(is_array($arCond['CHILDREN']))
						{
							foreach($arCond['CHILDREN'] as $k=>$v)
							{
								if($v['CLASS_ID']=='CondIBElement' && ToLower($v['DATA']['logic'])=='equal')
								{
									$childrenKey = $k;
								}
							}
						}
						
						if($childrenKey >= 0)
						{
							$val = $arCond['CHILDREN'][$childrenKey]['DATA']['value'];
							if(!is_array($val)) $val = array($val);
							if(!in_array($ID, $val)) $val[] = $ID;
							$arCond['CHILDREN'][$childrenKey]['DATA']['value'] = $val;
						}
						else
						{
							$arCond = $this->GetDiscountProductCond($ID);
						}
						$arFieldsProductDiscount2 = $arFieldsProductDiscount;
						$arFieldsProductDiscount2['CONDITIONS'] = $arCond;
						unset($arFieldsProductDiscount2['XML_ID']);
						
						if(\CCatalogDiscount::Update($arDiscount['ID'], $arFieldsProductDiscount2))
						{
							$isUpdate = true;
						}
						elseif($ex = $GLOBALS['APPLICATION']->GetException())
						{
							$lastError = $ex->GetString();
						}
					}
					
					if(!$isUpdate)
					{
						if(strpos($lastError, Loc::getMessage('BT_MOD_CATALOG_DISC_ERR_CONDITIONS_TOO_LONG'))!==false)
						{
							$suffixInd = 1;
							if(strlen($suffix) > 0) $suffixInd = ((int)substr($suffix, 1) + 1);
							$arFieldsProductDiscount['XML_ID'] .= '_'.$suffixInd;
						}
						$arFieldsProductDiscount['CONDITIONS'] = $this->GetDiscountProductCond($ID);
						if(!$arFieldsProductDiscount['CURRENCY']) $arFieldsProductDiscount['CURRENCY'] = $this->ie->params['DEFAULT_CURRENCY'];
						if(!$arFieldsProductDiscount['NAME'])
						{
							if($arFieldsProductDiscount['VALUE_TYPE']=='F') $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_F").' '.$arFieldsProductDiscount['VALUE'].' '.$arFieldsProductDiscount['CURRENCY'];
							elseif($arFieldsProductDiscount['VALUE_TYPE']=='S') $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_S").' '.$arFieldsProductDiscount['VALUE'].' '.$arFieldsProductDiscount['CURRENCY'];
							else $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_P").' '. $arFieldsProductDiscount['VALUE'].'%';
						}
						\CCatalogDiscount::Add($arFieldsProductDiscount);
					}
				}
				
				//Delete old discount
				$dbRes = \CCatalogDiscount::GetList(array(), array('PRODUCT_ID'=>$ID, 'XML_ID'=>$xmlIdProductDiscount), false, false, array('ID'));
				while($arDiscount = $dbRes->Fetch())
				{
					\CCatalogDiscount::Delete($arDiscount['ID']);
				}
				$this->DeleteProductDiscount($ID, $arFieldsProductDiscount);
			}
			else
			{
				$arFieldsProductDiscount['XML_ID'] = $xmlIdProductDiscount;
				if($arFieldsProductDiscount['ACTIVE_FROM']) $arFieldsProductDiscount['ACTIVE_FROM'] = $this->ie->GetDateVal($arFieldsProductDiscount['ACTIVE_FROM']);
				if($arFieldsProductDiscount['ACTIVE_TO']) $arFieldsProductDiscount['ACTIVE_TO'] = $this->ie->GetDateVal($arFieldsProductDiscount['ACTIVE_TO']);
				if(isset($arFieldsProductDiscount['RENEWAL'])) $arFieldsProductDiscount['RENEWAL'] = $this->ie->GetBoolValue($arFieldsProductDiscount['RENEWAL']);
				if(isset($arFieldsProductDiscount['LAST_DISCOUNT'])) $arFieldsProductDiscount['LAST_DISCOUNT'] = $this->ie->GetBoolValue($arFieldsProductDiscount['LAST_DISCOUNT']);
				$arFieldsProductDiscount['CONDITIONS'] = $this->GetDiscountProductCond($ID);
				
				$dbRes = \CCatalogDiscount::GetList(array(), array('PRODUCT_ID'=>$ID, 'XML_ID'=>$arFieldsProductDiscount['XML_ID']), false, false, array('ID'));
				while($arDiscount = $dbRes->Fetch())
				{
					if((float)$arFieldsProductDiscount['VALUE'] > 0) $arFieldsProductDiscount['ACTIVE'] = 'Y';
					else 
					{
						unset($arFieldsProductDiscount['VALUE']);
						$arFieldsProductDiscount['ACTIVE'] = 'N';
					}
					\CCatalogDiscount::Update($arDiscount['ID'], $arFieldsProductDiscount);
				}
				
				if($dbRes->SelectedRowsCount()==0)
				{
					if(!$arFieldsProductDiscount['NAME']) $arFieldsProductDiscount['NAME'] = $name;
					if(!$arFieldsProductDiscount['CURRENCY']) $arFieldsProductDiscount['CURRENCY'] = $this->ie->params['DEFAULT_CURRENCY'];
					\CCatalogDiscount::Add($arFieldsProductDiscount);
				}
				$this->DeleteProductDiscount($ID, $arFieldsProductDiscount, true);
			}
		}
	}
	
	public function DeleteProductDiscount($ID, $arFieldsProductDiscount, $all=false)
	{
		$dbRes = \CCatalogDiscount::GetList(array(), array('PRODUCT_ID'=>$ID, '%XML_ID'=>'IMPORT_'), false, false, array('ID', 'VALUE', 'VALUE_TYPE', 'CONDITIONS', 'CATALOG_GROUP_ID'));
		$arDiscounts = array();
		while($arDiscount = $dbRes->Fetch())
		{
			if(isset($arDiscounts[$arDiscount['ID']]))
			{
				if(!in_array($arDiscount['CATALOG_GROUP_ID'], $arDiscounts[$arDiscount['ID']]['CATALOG_GROUP_IDS'])) $arDiscounts[$arDiscount['ID']]['CATALOG_GROUP_IDS'][] = $arDiscount['CATALOG_GROUP_ID'];
			}
			else
			{
				$arDiscount['CATALOG_GROUP_IDS'] = array($arDiscount['CATALOG_GROUP_ID']);
				$arDiscounts[$arDiscount['ID']] = $arDiscount;
			}
		}

		foreach($arDiscounts as $arDiscount)
		{
			$arDiscount['CATALOG_GROUP_IDS'] = array_diff($arDiscount['CATALOG_GROUP_IDS'], array(-1));
			if(!is_array($arFieldsProductDiscount['CATALOG_GROUP_IDS'])) $arFieldsProductDiscount['CATALOG_GROUP_IDS'] = array();
			if(!$all && $arFieldsProductDiscount['VALUE_TYPE']==$arDiscount['VALUE_TYPE'] && $arFieldsProductDiscount['VALUE']==$arDiscount['VALUE'] && (count($arFieldsProductDiscount['CATALOG_GROUP_IDS'])==count($arDiscount['CATALOG_GROUP_IDS']) && count(array_diff($arFieldsProductDiscount['CATALOG_GROUP_IDS'], $arDiscount['CATALOG_GROUP_IDS']))==0)) continue;
			$arCond = unserialize($arDiscount['CONDITIONS']);
			if(is_array($arCond['CHILDREN']))
			{
				foreach($arCond['CHILDREN'] as $k=>$v)
				{
					if($v['CLASS_ID']=='CondIBElement' && ToLower($v['DATA']['logic'])=='equal')
					{
						$val = $arCond['CHILDREN'][$k]['DATA']['value'];
						if(!is_array($val)) $val = array($val);
						$val = array_diff($val, array($ID));
						if(!empty($val)) $arCond['CHILDREN'][$k]['DATA']['value'] = $val;
						else unset($arCond['CHILDREN'][$k]);
					}
				}
			}
			if(empty($arCond['CHILDREN'])) \CCatalogDiscount::Delete($arDiscount['ID']);
			else \CCatalogDiscount::Update($arDiscount['ID'], array('CONDITIONS'=>$arCond));
		}
	}
	
	public function GetDiscountProductCond($ID)
	{
		$arCond = \CCatalogCondTree::GetDefaultConditions();
		$arCond['CHILDREN'][] = array(
			'CLASS_ID' => 'CondIBElement',
			'DATA' => array(
				'logic' => 'Equal',
				'value' => $ID
			)
		);
		return $arCond;
	}
	
	public function SaveDiscountGift($ID, $IBLOCK_ID, $giftId, $siteId, $name, $isOffer = false)
	{
		if($giftId===false || strlen($giftId)==0) return;
		$arGifts = explode($this->ie->params['ELEMENT_MULTIPLE_SEPARATOR'], $giftId);
		$arGifts = array_diff(array_map('trim', $arGifts), array('', '0'));
		if(empty($arGifts)) return;

		$arGiftIds = array();
		foreach($arGifts as $giftId)
		{
			$relField = $this->ie->fieldSettings[($isOffer ? 'OFFER_' : '').'ICAT_DISCOUNT_BRGIFT']['REL_ELEMENT_FIELD'];
			if($relField && $relField!='IE_ID')
			{
				$arFilter = array('IBLOCK_ID'=>$IBLOCK_ID);
				if(strpos($relField, 'IE_')===0)
				{
					$key = substr($relField, 3);
					$arFilter[$key] = $giftId;
				}
				elseif(strpos($relField, 'IP_PROP')===0)
				{
					$key = substr($relField, 7);
					$arFilter['PROPERTY_'.$key] = $giftId;
				}
				$dbRes = \CIblockElement::GetList(array('ID'=>'ASC'), $arFilter, false, array('nTopCount'=>10000), array('ID'));
				$i = $len = 0;
				while(($arElem = $dbRes->Fetch()) && $len < 65000)
				{
					$giftId = $arElem['ID'];
					if($giftId > 0 && !in_array($giftId, $arGiftIds))
					{
						$arGiftIds[] = $giftId;
						$len += strlen($giftId) + strlen($i++) + 6;
					}
				}
			}
			else
			{
				$giftId = (int)$giftId;
				if($giftId > 0) $arGiftIds[$giftId] = $giftId;
			}
		}
		
		if(count($arGiftIds) > 0)
		{
			$arDiscountFields = array(
				'ACTIVE' => 'Y',
				'XML_ID' => 'GIFT_'.$ID.'_'.$siteId,
				'CONDITIONS' => array(
					'CLASS_ID' => 'CondGroup',
					'DATA' => array(
							'All' => 'AND',
							'True' => 'True'
						),
					'CHILDREN' => array(
						0 => array(
							'CLASS_ID' => 'CondBsktProductGroup',
							'DATA' => array(
								'Found' => 'Found',
								'All' => 'AND'
							),
							'CHILDREN' => array(
								0 => array(
									'CLASS_ID' => 'CondIBElement',
									'DATA' => array(
										'logic' => 'Equal',
										'value' => $ID,
									)
								)
							)
						)
					)
				),
				'ACTIONS' => array(
					'CLASS_ID' => 'CondGroup',
					'DATA' => array(
							'All' => 'AND'
						),
					'CHILDREN' => array(
						0 => array(
							'CLASS_ID' => 'GiftCondGroup',
							'DATA' => array(
								'All' => 'AND'
							),
							'CHILDREN' => array(
								0 => array(
									'CLASS_ID' => 'GifterCondIBElement',
									'DATA' => array(
										'Type' => 'one',
										'Value' => $arGiftIds
									)
								)
							)
						)
					)
				)
			);
			
			$dbRes = \CSaleDiscount::GetList(array(), array('XML_ID'=>$arDiscountFields['XML_ID']), false, false, array('ID'));
			while($arDiscount = $dbRes->Fetch())
			{
				\CSaleDiscount::Update($arDiscount['ID'], $arDiscountFields);
			}
			
			if($dbRes->SelectedRowsCount()==0)
			{
				$arDiscountFields['LID'] = $siteId;
				$arDiscountFields['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_PRODUCT_GIFT").' '.$name;
				$arDiscountFields['PRIORITY'] = 1;
				$arDiscountFields['LAST_DISCOUNT'] = 'Y';
				$arDiscountFields['USER_GROUPS'] = array(2);
				\CSaleDiscount::Add($arDiscountFields);
			}
		}
	}
	
	public function SaveSaleDiscount($ID, $IBLOCK_ID, $arFieldsProductDiscount, $name, $isOffer = false)
	{
		if((string)\Bitrix\Main\Config\Option::get(static::$moduleId, 'DISCOUNT_MODE') == 'JOIN')
		{
			$this->SaveSaleDiscountShare($ID, $IBLOCK_ID, $arFieldsProductDiscount, $name, $isOffer);
			return;
		}

		$brgift = false;
		if(isset($arFieldsProductDiscount['BRGIFT']))
		{
			$brgift = $arFieldsProductDiscount['BRGIFT'];
			unset($arFieldsProductDiscount['BRGIFT']);
		}
		
		$onlyVal = (bool)(count(array_diff(array_keys($arFieldsProductDiscount), array('VALUE', 'VALUE_TYPE', 'CATALOG_GROUP_IDS')))==0);
		$arSites = $this->GetIblockSite($IBLOCK_ID);
		$lidValues = array();
		if(isset($arFieldsProductDiscount['LID_VALUES']))
		{
			if(is_array($arFieldsProductDiscount['LID_VALUES'])) $lidValues = $arFieldsProductDiscount['LID_VALUES'];
			unset($arFieldsProductDiscount['LID_VALUES']);
		}
		if(!empty($lidValues))
		{
			$arSites = array_intersect($arSites, array_keys($lidValues));
		}
		$arFieldsProductDiscountOrig = $arFieldsProductDiscount;
		foreach($arSites as $siteId)
		{
			$arFieldsProductDiscount = $arFieldsProductDiscountOrig;
			if($lidValues[$siteId])
			{
				$arFieldsProductDiscount = array_merge($arFieldsProductDiscount, $lidValues[$siteId]);
			}
			/*$this->SaveDiscountGift($ID, $IBLOCK_ID, $brgift, $siteId, $name, $isOffer);
			if(!isset($arFieldsProductDiscount['VALUE'])) continue;*/
			
			$discountId = 0;
			$arFieldsProductDiscount['LID'] = $siteId;
			
			$customXmlId = false;
			if(isset($arFieldsProductDiscount['XML_ID']) && strlen(trim($arFieldsProductDiscount['XML_ID'])) > 0)
			{
				$customXmlId = true;
				$arFieldsProductDiscount['XML_ID'] = trim($arFieldsProductDiscount['XML_ID']);
			}

			if(!$customXmlId || isset($arFieldsProductDiscount['VALUE']))
			{
				$arFieldsProductDiscount['VALUE'] = $this->ie->GetFloatVal($arFieldsProductDiscount['VALUE'], 4);
				if($arFieldsProductDiscount['VALUE']==0)
				{
					$this->SetDiscountProductId(0, $ID, $siteId);
					continue;
				}
			}
			
			$arFieldsProductDiscount['ACTIVE_FROM'] = ($arFieldsProductDiscount['ACTIVE_FROM'] ? $this->ie->GetDateVal($arFieldsProductDiscount['ACTIVE_FROM']) : '');
			$arFieldsProductDiscount['ACTIVE_TO'] = ($arFieldsProductDiscount['ACTIVE_TO'] ? $this->ie->GetDateVal($arFieldsProductDiscount['ACTIVE_TO']) : '');
			$arFieldsProductDiscount['PRIORITY'] = (isset($arFieldsProductDiscount['PRIORITY']) ? $this->ie->GetFloatVal($arFieldsProductDiscount['PRIORITY']) : 1);
			$arFieldsProductDiscount['LAST_DISCOUNT'] = (isset($arFieldsProductDiscount['LAST_DISCOUNT']) ? $this->ie->GetBoolValue($arFieldsProductDiscount['LAST_DISCOUNT']) :"N");
			$arFieldsProductDiscount['SORT'] = 100;
			ksort($arFieldsProductDiscount);
			$md5Params = md5(serialize($arFieldsProductDiscount));
			
			if(!$customXmlId)
			{
				$arFieldsProductDiscount['XML_ID'] = 'IMPORT_'.$arFieldsProductDiscount['VALUE_TYPE'].'_'.$arFieldsProductDiscount['VALUE'].'_'.$arFieldsProductDiscount['LID'];
				if(!$onlyVal)
				{
					$arFieldsProductDiscount['XML_ID'] .= '_'.$md5Params;
				}
			}
			
			$dbRes = \CSaleDiscount::GetList(array(), array('XML_ID'=>$arFieldsProductDiscount['XML_ID']), false, false, array('ID', 'ACTIONS'));
			while($arDiscount = $dbRes->Fetch())
			{
				$arCond = unserialize($arDiscount['ACTIONS']);
				$childrenKey1 = $childrenKey2 = -1;
				if(is_array($arCond['CHILDREN']))
				{
					foreach($arCond['CHILDREN'] as $k=>$v)
					{
						if($v['CLASS_ID']=='ActSaleBsktGrp')
						{
							$childrenKey1 = $k;
							if(is_array($v['CHILDREN']))
							{
								foreach($v['CHILDREN'] as $k2=>$v2)
								{
									if($v2['CLASS_ID']=='CondIBElement' && ToLower($v2['DATA']['logic'])=='equal')
									{
										$childrenKey2 = $k2;
									}
								}
							}
						}
					}
				}
				
				if($childrenKey1 >= 0 && $childrenKey2 >= 0)
				{
					$val = $arCond['CHILDREN'][$childrenKey1]['CHILDREN'][$childrenKey2]['DATA']['value'];
					if(!is_array($val)) $val = array($val);
					if(!in_array($ID, $val)) $val[] = $ID;
					if($customXmlId && isset($arFieldsProductDiscount['VALUE']))
					{
						$arCond = $this->GetSaleDiscountProductActions($val, $arFieldsProductDiscount);
					}
					else
					{
						$arCond['CHILDREN'][$childrenKey1]['CHILDREN'][$childrenKey2]['DATA']['value'] = $val;
					}
				}
				else
				{
					$arCond = $this->GetSaleDiscountProductActions($ID, $arFieldsProductDiscount);
				}
				$arFieldsProductDiscount2 = array('ACTIONS' => $arCond);
				if($customXmlId)
				{
					$arFieldsProductDiscount2 = array_merge($this->PrepareSaleDiscountFields($arFieldsProductDiscount), $arFieldsProductDiscount2);
				}
				
				\CSaleDiscount::Update($arDiscount['ID'], $arFieldsProductDiscount2);
				$discountId = $arDiscount['ID'];
			}
			
			if($dbRes->SelectedRowsCount()==0)
			{
				$arFieldsProductDiscount['ACTIONS'] = $this->GetSaleDiscountProductActions($ID, $arFieldsProductDiscount);
				//if(!$arFieldsProductDiscount['CURRENCY']) $arFieldsProductDiscount['CURRENCY'] = $this->ie->params['DEFAULT_CURRENCY'];
				if(!$arFieldsProductDiscount['NAME'])
				{
					if($arFieldsProductDiscount['VALUE_TYPE']=='F') $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_F").' '.$arFieldsProductDiscount['VALUE'].' '.$arFieldsProductDiscount['CURRENCY'];
					elseif($arFieldsProductDiscount['VALUE_TYPE']=='S') $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_S").' '.$arFieldsProductDiscount['VALUE'].' '.$arFieldsProductDiscount['CURRENCY'];
					else $arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_TYPE_P").' '. $arFieldsProductDiscount['VALUE'].'%';
				}
				$arFieldsProductDiscount2 = $this->PrepareSaleDiscountFields($arFieldsProductDiscount);
				$discountId = \CSaleDiscount::Add($arFieldsProductDiscount2);
			}
			
			$this->SetDiscountProductId($discountId, $ID, $siteId);
		}
	}
	
	public function SaveSaleDiscountShare($ID, $IBLOCK_ID, $arFieldsProductDiscount, $name, $isOffer = false)
	{
		$brgift = false;
		if(isset($arFieldsProductDiscount['BRGIFT']))
		{
			$brgift = $arFieldsProductDiscount['BRGIFT'];
			unset($arFieldsProductDiscount['BRGIFT']);
		}
		
		$onlyVal = (bool)(count(array_diff(array_keys($arFieldsProductDiscount), array('VALUE', 'VALUE_TYPE', 'CATALOG_GROUP_IDS', 'LID_VALUES')))==0);
		$arSites = $this->GetIblockSite($IBLOCK_ID);
		$lidValues = array();
		if(isset($arFieldsProductDiscount['LID_VALUES']))
		{
			if(is_array($arFieldsProductDiscount['LID_VALUES'])) $lidValues = $arFieldsProductDiscount['LID_VALUES'];
			unset($arFieldsProductDiscount['LID_VALUES']);
		}
		if(!empty($lidValues))
		{
			$arSites = array_intersect($arSites, array_keys($lidValues));
		}
		$arFieldsProductDiscountOrig = $arFieldsProductDiscount;
		foreach($arSites as $siteId)
		{
			$arFieldsProductDiscount = $arFieldsProductDiscountOrig;
			if($lidValues[$siteId])
			{
				$arFieldsProductDiscount = array_merge($arFieldsProductDiscount, $lidValues[$siteId]);
			}
			$this->SaveDiscountGift($ID, $IBLOCK_ID, $brgift, $siteId, $name, $isOffer);
			if(!isset($arFieldsProductDiscount['VALUE'])) continue;
			
			$discountId = 0;
			$arFieldsProductDiscount['LID'] = $siteId;
			
			$customXmlId = false;
			if(isset($arFieldsProductDiscount['XML_ID']) && strlen(trim($arFieldsProductDiscount['XML_ID'])) > 0)
			{
				$customXmlId = true;
				$arFieldsProductDiscount['XML_ID'] = trim($arFieldsProductDiscount['XML_ID']);
			}

			if(!$customXmlId || isset($arFieldsProductDiscount['VALUE']))
			{
				$arFieldsProductDiscount['VALUE'] = $this->ie->GetFloatVal($arFieldsProductDiscount['VALUE'], 4);
				if($arFieldsProductDiscount['VALUE']==0)
				{
					$this->SetDiscountShareProductId(0, $ID, $siteId, $arFieldsProductDiscount);
					continue;
				}
			}
			
			$arFieldsProductDiscount['ACTIVE_FROM'] = ($arFieldsProductDiscount['ACTIVE_FROM'] ? $this->ie->GetDateVal($arFieldsProductDiscount['ACTIVE_FROM']) : '');
			$arFieldsProductDiscount['ACTIVE_TO'] = ($arFieldsProductDiscount['ACTIVE_TO'] ? $this->ie->GetDateVal($arFieldsProductDiscount['ACTIVE_TO']) : '');
			$arFieldsProductDiscount['PRIORITY'] = (isset($arFieldsProductDiscount['PRIORITY']) ? $this->ie->GetFloatVal($arFieldsProductDiscount['PRIORITY']) : 1);
			$arFieldsProductDiscount['LAST_DISCOUNT'] = (isset($arFieldsProductDiscount['LAST_DISCOUNT']) ? $this->ie->GetBoolValue($arFieldsProductDiscount['LAST_DISCOUNT']) :"N");
			$arFieldsProductDiscount['SORT'] = 100;
			ksort($arFieldsProductDiscount);
			$arTmp = $arFieldsProductDiscount;
			unset($arTmp['VALUE_TYPE'], $arTmp['VALUE']);
			$md5Params = md5(serialize($arTmp));
			
			if(!$customXmlId)
			{
				$arFieldsProductDiscount['XML_ID'] = 'IMPORT_'.$arFieldsProductDiscount['LID'];
				if(!$onlyVal)
				{
					$arFieldsProductDiscount['XML_ID'] .= '_'.$md5Params;
				}
			}
			
			$dbRes = \CSaleDiscount::GetList(array(), array('XML_ID'=>$arFieldsProductDiscount['XML_ID']), false, false, array('ID', 'ACTIONS'));
			while($arDiscount = $dbRes->Fetch())
			{
				$arCond = unserialize($arDiscount['ACTIONS']);
				list($childrenKey1, $childrenKey2) = $this->SearchSaleDiscountProductAction($arCond, $arFieldsProductDiscount);
				
				if($childrenKey1 >= 0 && $childrenKey2 >= 0)
				{
					$val = $arCond['CHILDREN'][$childrenKey1]['CHILDREN'][$childrenKey2]['DATA']['value'];
					if(!is_array($val)) $val = array($val);
					if(!in_array($ID, $val)) $val[] = $ID;
					$arCond['CHILDREN'][$childrenKey1]['CHILDREN'][$childrenKey2]['DATA']['value'] = $val;
				}
				else
				{
					$arCond2 = $this->GetSaleDiscountProductActions($ID, $arFieldsProductDiscount);
					if(is_array($arCond['CHILDREN']))
					{
						$arCond['CHILDREN'][] = current($arCond2['CHILDREN']);
					}
					else
					{
						$arCond = $arCond2;
					}
				}
				$arFieldsProductDiscount2 = array('ACTIONS' => $arCond);
				if($customXmlId)
				{
					$arFieldsProductDiscount2 = array_merge($this->PrepareSaleDiscountFields($arFieldsProductDiscount), $arFieldsProductDiscount2);
				}
				
				\CSaleDiscount::Update($arDiscount['ID'], $arFieldsProductDiscount2);
				$discountId = $arDiscount['ID'];
			}
			
			if($dbRes->SelectedRowsCount()==0)
			{
				$arFieldsProductDiscount['ACTIONS'] = $this->GetSaleDiscountProductActions($ID, $arFieldsProductDiscount);
				//if(!$arFieldsProductDiscount['CURRENCY']) $arFieldsProductDiscount['CURRENCY'] = $this->ie->params['DEFAULT_CURRENCY'];
				if(!$arFieldsProductDiscount['NAME'])
				{
					$arFieldsProductDiscount['NAME'] = Loc::getMessage("ESOL_IX_DISCOUNT_NAME_SHARE");
				}
				$arFieldsProductDiscount2 = $this->PrepareSaleDiscountFields($arFieldsProductDiscount);
				$discountId = \CSaleDiscount::Add($arFieldsProductDiscount2);
			}
			
			$this->SetDiscountShareProductId($discountId, $ID, $siteId, $arFieldsProductDiscount);
		}
	}
	
	public function SearchSaleDiscountProductAction($arCond, $arFieldsProductDiscount)
	{
		$childrenKey1 = $childrenKey2 = -1;
		if(is_array($arCond['CHILDREN']))
		{
			foreach($arCond['CHILDREN'] as $k=>$v)
			{
				if($v['CLASS_ID']=='ActSaleBsktGrp' && $v['DATA']['Type']=='Discount' && (float)$v['DATA']['Value']==(float)$arFieldsProductDiscount['VALUE'] && $v['DATA']['Unit']==($arFieldsProductDiscount['VALUE_TYPE']=='F' ? 'CurEach' : 'Perc'))
				{
					$childrenKey1 = $k;
					if(is_array($v['CHILDREN']))
					{
						foreach($v['CHILDREN'] as $k2=>$v2)
						{
							if($v2['CLASS_ID']=='CondIBElement' && ToLower($v2['DATA']['logic'])=='equal')
							{
								$childrenKey2 = $k2;
							}
						}
					}
				}
			}
		}
		return array($childrenKey1, $childrenKey2);
	}
	
	public function GetSaleDiscountProductActions($ID, $arFieldsProductDiscount)
	{
		$arCond = Array(
			'CLASS_ID' => 'CondGroup',
			'DATA' => Array(
				'All' => 'AND'
			),
			'CHILDREN' => Array(
				Array(
					'CLASS_ID' => 'ActSaleBsktGrp',
					'DATA' => Array(
						'Type' => 'Discount',
						'Value' => $arFieldsProductDiscount['VALUE'],
						'Unit' => ($arFieldsProductDiscount['VALUE_TYPE']=='F' ? 'CurEach' : 'Perc'),
						'All' => 'AND',
						'Max' => (isset($arFieldsProductDiscount['MAX_DISCOUNT']) && (float)$arFieldsProductDiscount['MAX_DISCOUNT'] > 0 ? $arFieldsProductDiscount['MAX_DISCOUNT'] : 0),
						'True' => 'True'
					),
					'CHILDREN' => Array(
						Array(
							'CLASS_ID' => 'CondIBElement',
							'DATA' => Array(
								'logic' => 'Equal',
								'value' => (is_array($ID) ? $ID : Array($ID))
							)
						)
					)
				)
			)
		);
		return $arCond;
	}
	
	public function PrepareSaleDiscountFields($arFieldsProductDiscount)
	{
		if(isset($arFieldsProductDiscount['VALUE'])) unset($arFieldsProductDiscount['VALUE']);
		if(isset($arFieldsProductDiscount['VALUE_TYPE'])) unset($arFieldsProductDiscount['VALUE_TYPE']);
		if(isset($arFieldsProductDiscount['MAX_DISCOUNT'])) unset($arFieldsProductDiscount['MAX_DISCOUNT']);
		$arFieldsProductDiscount['ACTIVE'] = 'Y';
		$arFieldsProductDiscount['USER_GROUPS'] = $this->GetAllUserGroups();
		$arFieldsProductDiscount['CONDITIONS'] = $this->GetSaleDiscountProductConds();
		return $arFieldsProductDiscount;
	}
	
	public function GetSaleDiscountProductConds()
	{
	    return Array(
			'CLASS_ID' => 'CondGroup',
			'DATA' => Array(
				'All' => 'AND',
				'True' => 'True'
			),
			'CHILDREN' => Array()
		);
	}
	
	public function SetDiscountProductId($discountId, $productId, $siteId)
	{
		$dpEntity = $this->GetDPEntity();
		$findDiscount = false;
		$dbRes = $dpEntity::getList(array('filter'=>array('PRODUCT_ID'=>$productId, 'SITE_ID'=>$siteId)));
		while($arr = $dbRes->Fetch())
		{
			if($arr['DISCOUNT_ID']==$discountId) $findDiscount = true;
			else
			{
				$dpEntity::delete($arr['ID']);
				
				$dbRes = \CSaleDiscount::GetList(array(), array('ID'=>$arr['DISCOUNT_ID']), false, false, array('ID', 'ACTIONS'));
				while($arDiscount = $dbRes->Fetch())
				{
					$arCond = unserialize($arDiscount['ACTIONS']);
					$childrenKey1 = $childrenKey2 = -1;
					if(is_array($arCond['CHILDREN']))
					{
						foreach($arCond['CHILDREN'] as $k=>$v)
						{
							if($v['CLASS_ID']=='ActSaleBsktGrp')
							{
								$childrenKey1 = $k;
								if(is_array($v['CHILDREN']))
								{
									foreach($v['CHILDREN'] as $k2=>$v2)
									{
										if($v2['CLASS_ID']=='CondIBElement' && ToLower($v2['DATA']['logic'])=='equal')
										{
											$childrenKey2 = $k2;
										}
									}
								}
							}
						}
					}
					
					if($childrenKey1 >= 0 && $childrenKey2 >= 0)
					{
						$val = $this->GetExistsProductIds($arCond['CHILDREN'][$childrenKey1]['CHILDREN'][$childrenKey2]['DATA']['value']);
						if(!is_array($val) && $val==$productId)
						{
							\CSaleDiscount::Delete($arDiscount['ID']);
						}
						if(is_array($val) && in_array($productId, $val))
						{
							$val = array_diff($val, array($productId));
							if(empty($val)) \CSaleDiscount::Delete($arDiscount['ID']);
							else
							{
								$arCond['CHILDREN'][$childrenKey1]['CHILDREN'][$childrenKey2]['DATA']['value'] = $val;
								$arFieldsProductDiscount2 = array('ACTIONS' => $arCond);
								\CSaleDiscount::Update($arDiscount['ID'], $arFieldsProductDiscount2);
							}
						}
					}
				}
			}
		}
		if(!$findDiscount && $discountId > 0)
		{
			$dpEntity::add(array(
				'DISCOUNT_ID'=>$discountId,
				'PRODUCT_ID'=>$productId,
				'SITE_ID'=>$siteId
			));
		}
	}
	
	public function SetDiscountShareProductId($discountId, $productId, $siteId, $arFieldsProductDiscount)
	{
		$dpEntity = $this->GetDPEntity();
		$findDiscount = false;
		$dbRes = $dpEntity::getList(array('filter'=>array('PRODUCT_ID'=>$productId, 'SITE_ID'=>$siteId)));
		while($arr = $dbRes->Fetch())
		{
			if($arr['DISCOUNT_ID']==$discountId) $findDiscount = true;
			else
			{
				$dpEntity::delete($arr['ID']);
			}
				
			$dbRes = \CSaleDiscount::GetList(array(), array('ID'=>$arr['DISCOUNT_ID']), false, false, array('ID', 'ACTIONS'));
			while($arDiscount = $dbRes->Fetch())
			{
				$arCond = unserialize($arDiscount['ACTIONS']);
				if(is_array($arCond['CHILDREN']))
				{
					foreach($arCond['CHILDREN'] as $k=>$v)
					{
						if($v['CLASS_ID']=='ActSaleBsktGrp' && 
							(($arr['DISCOUNT_ID']==$discountId && ((float)$v['DATA']['Value']!=(float)$arFieldsProductDiscount['VALUE'] || $v['DATA']['Unit']!=($arFieldsProductDiscount['VALUE_TYPE']=='F' ? 'CurEach' : 'Perc')))
							|| ($arr['DISCOUNT_ID']!=$discountId)
							))
						{
							if(is_array($v['CHILDREN']))
							{
								foreach($v['CHILDREN'] as $k2=>$v2)
								{
									if($v2['CLASS_ID']=='CondIBElement' && ToLower($v2['DATA']['logic'])=='equal')
									{
										$val = $v2['DATA']['value'];
										if(!is_array($val) && $val==$productId)
										{
											unset($arCond['CHILDREN'][$k]);
											break;
										}
										if(is_array($val) && in_array($productId, $val))
										{
											$val = array_diff($val, array($productId));
											if(empty($val))
											{
												unset($arCond['CHILDREN'][$k]);
												break;
											}
											else
											{
												$arCond['CHILDREN'][$k]['CHILDREN'][$k2]['DATA']['value'] = $val;
											}
										}
									}
								}
							}
						}
					}
				}
				
				if(empty($arCond['CHILDREN']))
				{
					\CSaleDiscount::Delete($arDiscount['ID']);
				}
				else
				{
					$arFieldsProductDiscount2 = array('ACTIONS' => $arCond);
					\CSaleDiscount::Update($arDiscount['ID'], $arFieldsProductDiscount2);
				}
			}
		}
		if(!$findDiscount)
		{
			$dpEntity::add(array(
				'DISCOUNT_ID'=>$discountId,
				'PRODUCT_ID'=>$productId,
				'SITE_ID'=>$siteId
			));
		}
	}
	
	public function GetDPEntity()
	{
		if(!isset($this->dpEntity))
		{
			$this->dpEntity = new \Bitrix\EsolImportxml\DataManager\DiscountProductTable();
			$tblName = $this->dpEntity->getTableName();
			$conn = $this->dpEntity->getEntity()->getConnection();
			if(!$conn->isTableExists($tblName))
			{
				$this->dpEntity->getEntity()->createDbTable();
			}
		}
		return $this->dpEntity;
	}
	
	public function GetAllUserGroups()
	{
		if(!isset($this->arUserGroups) || !is_array($this->arUserGroups))
		{
			$this->arUserGroups = array();
			$dbRes = \Bitrix\Main\GroupTable::getList(array('select'=>array('ID')));
			while($arr = $dbRes->Fetch())
			{
				$this->arUserGroups[] = $arr['ID'];
			}
		}
		return $this->arUserGroups;
	}
	
	public function GetIblockSite($IBLOCK_ID)
	{
		if(!isset($this->arIblockSites)) $this->arIblockSites = array();
		if(!$this->arIblockSites[$IBLOCK_ID])
		{
			$arSites = array();
			$dbRes = \CIBlock::GetSite($IBLOCK_ID);
			while($arSite = $dbRes->Fetch())
			{
				$arSites[] = $arSite['SITE_ID'];
			}
			$this->arIblockSites[$IBLOCK_ID] = $arSites;
		}
		return $this->arIblockSites[$IBLOCK_ID];
	}
	
	public function GetExistsProductIds($val)
	{
		$dpEntity = $this->GetDPEntity();
		if(!is_array($val)) $val = array((int)$val);
		if(!empty($val) && class_exists('\Bitrix\Iblock\ElementTable'))
		{
			$oldVals = $val;
			$val = array();
			$dbRes = \Bitrix\Iblock\ElementTable::getList(array('filter'=>array('ID'=>$oldVals), 'select'=>array('ID')));
			while($arr = $dbRes->Fetch())
			{
				$val[] = $arr['ID'];
			}
			$excludedVals = array_diff($oldVals, $val);
			if(count($excludedVals) > 0 && $this->discountModule=='sale')
			{
				foreach($excludedVals as $id)
				{
					$dbRes = $dpEntity::getList(array('filter'=>array('PRODUCT_ID'=>$id), 'select'=>array('ID')));
					while($arr = $dbRes->Fetch())
					{
						$dpEntity::delete($arr['ID']);
					}
				}
			}
		}
		return $val;
	}
	
	public function RemoveExpiredDiscount()
	{
		if($this->discountModule=='sale')
		{
			$dbRes = \CSaleDiscount::GetList(array(), array('!ACTIVE_TO'=>false, '<ACTIVE_TO'=>ConvertTimeStamp(false, 'FULL')), false, false, array('ID'));
			while($arDiscount = $dbRes->Fetch())
			{
				\CSaleDiscount::Delete($arDiscount['ID']);
			}
		}
		else
		{
			$dbRes = \CCatalogDiscount::GetList(array(), array('!ACTIVE_TO'=>false, '<ACTIVE_TO'=>ConvertTimeStamp(false, 'FULL')), false, false, array('ID'));
			while($arDiscount = $dbRes->Fetch())
			{
				\CCatalogDiscount::Delete($arDiscount['ID']);
			}
		}
	}
}