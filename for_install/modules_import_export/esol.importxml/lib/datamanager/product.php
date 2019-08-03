<?php
namespace Bitrix\EsolImportxml\DataManager;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Product
{
	protected $ie = null;
	protected $logger = null;
	protected $pricer = null;
	protected $params = null;
	protected $saveProductWithOffers = null;
	
	public function __construct($ie=false)
	{
		$this->ie = $ie;
		$this->logger = $this->ie->logger;
		$this->pricer = $this->ie->pricer;
		$this->params = $this->ie->params;
		$this->saveProductWithOffers = $this->ie->saveProductWithOffers;
	}
	
	public function GetOfferParentId()
	{
		return $this->ie->GetOfferParentId();
	}
	
	public function GetFieldSettings($key)
	{
		return $this->ie->GetFieldSettings($key);
	}
	
	public function GetCurrentIblock()
	{
		return $this->ie->GetCurrentIblock();
	}
	
	public function GetIblockElementValue($arProp, $val, $fsettings, $bAdd = false, $allowNF = false, $allowMultiple = false)
	{
		return $this->ie->GetIblockElementValue($arProp, $val, $fsettings, $bAdd, $allowNF, $allowMultiple);
	}
	
	public function GetFloatVal($val, $precision=0)
	{
		return $this->ie->GetFloatVal($val, $precision);
	}
	
	public function GetBoolValue($val, $numReturn = false, $defaultValue = false)
	{
		return $this->ie->GetBoolValue($val, $numReturn, $defaultValue);
	}
	
	public function ApplyMargins($val, $fieldKey)
	{
		return $this->ie->ApplyMargins($val, $fieldKey);
	}
	
	public function SaveProduct($ID, $IBLOCK_ID, $arProduct, $arPrices, $arStores, $parentID=false)
	{
		if(!is_array($arProduct))
		{
			$arProduct = array();
		}
		if($parentID && defined('\Bitrix\Catalog\ProductTable::TYPE_OFFER'))
		{
			$arProduct['TYPE'] = \Bitrix\Catalog\ProductTable::TYPE_OFFER;
		}
		$isOffer = (bool)($parentID > 0);
			
		if((!empty($arProduct) || !empty($arPrices) || !empty($arStores)))
		{
			$arProduct['ID'] = $ID;
		}
		
		if(empty($arProduct)) return false;
		
		if(isset($arProduct['QUANTITY'])) $arProduct['QUANTITY'] = $this->GetFloatVal($arProduct['QUANTITY']);
		foreach(array('CAN_BUY_ZERO', 'NEGATIVE_AMOUNT_TRACE', 'SUBSCRIBE', 'QUANTITY_TRACE') as $key)
		{
			if(isset($arProduct[$key]))
			{
				if(ToUpper(trim($arProduct[$key]))=='D') $arProduct[$key] = 'D';
				else $arProduct[$key] = $this->GetBoolValue($arProduct[$key], false, 'D');
			}
		}
		if(!isset($arProduct['QUANTITY_TRACE']) && $this->params['QUANTITY_TRACE']=='Y') $arProduct['QUANTITY_TRACE'] = 'Y';
		if(isset($arProduct['VAT_INCLUDED'])) $arProduct['VAT_INCLUDED'] = $this->GetBoolValue($arProduct['VAT_INCLUDED']);
		if(isset($arProduct['WEIGHT'])) $arProduct['WEIGHT'] = $this->GetFloatVal($arProduct['WEIGHT'], 2);
		if(isset($arProduct['PURCHASING_PRICE']) || isset($arProduct['PURCHASING_CURRENCY']))
		{
			if(!isset($arProduct['PURCHASING_CURRENCY']) || (isset($arProduct['PURCHASING_CURRENCY']) && !trim($arProduct['PURCHASING_CURRENCY'])))
			{
				$arProduct['PURCHASING_CURRENCY'] = $this->params['DEFAULT_CURRENCY'];
			}
			$arProduct['PURCHASING_CURRENCY'] = $this->pricer->GetCurrencyVal($arProduct['PURCHASING_CURRENCY']);
		}
		
		if(isset($arProduct['PURCHASING_PRICE']) && $arProduct['PURCHASING_PRICE']!=='')
		{
			$pKey = ($isOffer ? 'OFFER_' : '').'ICAT_PURCHASING_PRICE';
			$arProduct['PURCHASING_PRICE'] = $this->ApplyMargins($arProduct['PURCHASING_PRICE'], $pKey);
			$arProduct['PURCHASING_PRICE'] = $this->GetFloatVal($arProduct['PURCHASING_PRICE'], 2);
		}
		
		if(isset($arProduct['MEASURE_RATIO']))
		{
			$arProductMeasureRatio = array(
				'RATIO' => $arProduct['MEASURE_RATIO'],
				'PRODUCT_ID' => $ID,
				'IS_DEFAULT' => 'Y'
			);
			unset($arProduct['MEASURE_RATIO']);
			$dbRes = \CCatalogMeasureRatio::getList(array(), array('PRODUCT_ID' => $arProductMeasureRatio['PRODUCT_ID'], 'IS_DEFAULT'=>''), false, false, array_merge(array('ID'), array_keys($arProductMeasureRatio)));
			$cntRes = $dbRes->SelectedRowsCount();
			while(($cntRes > 1) && ($arRatio = $dbRes->Fetch()))
			{
				\CCatalogMeasureRatio::delete($arRatio['ID']);
				$cntRes--;
			}
			if($arRatio = $dbRes->Fetch())
			{
				foreach($arRatio as $k=>$v)
				{
					if($v==$arProductMeasureRatio[$k])
					{
						unset($arProductMeasureRatio[$k]);
					}
				}
				if(!empty($arProductMeasureRatio))
				{
					\CCatalogMeasureRatio::update($arRatio['ID'], $arProductMeasureRatio);
				}
			}
			else
			{
				\CCatalogMeasureRatio::add($arProductMeasureRatio);
			}
		}
		
		if(isset($arProduct['BARCODE']))
		{
			if(!is_array($arProduct['BARCODE'])) $arProduct['BARCODE'] = array_map('trim', explode($this->params['ELEMENT_MULTIPLE_SEPARATOR'], $arProduct['BARCODE']));
			$arProduct['BARCODE'] = array_unique($arProduct['BARCODE']);
			$dbRes = \CCatalogStoreBarCode::getList(array(), array('PRODUCT_ID' => $ID), false, false, array('ID', 'BARCODE'));
			$arBarcodesDB = array();
			while($arr = $dbRes->Fetch())
			{
				if(in_array($arr['BARCODE'], $arProduct['BARCODE']))
				{
					unset($arProduct['BARCODE'][array_search($arr['BARCODE'], $arProduct['BARCODE'])]);
				}
				else
				{
					$arBarcodesDB[] = $arr['ID'];
				}
			}
			
			if(!empty($arBarcodesDB))
			{
				foreach($arBarcodesDB as $bid)
				{
					if(!empty($arProduct['BARCODE']))
					{
						$barcode = array_shift($arProduct['BARCODE']);
						\CCatalogStoreBarCode::Update($bid, array(
							'BARCODE' => $barcode,
							'STORE_ID' => 0,
							'ORDER_ID' => false
						));
					}
					else
					{
						\CCatalogStoreBarCode::Delete($bid);
					}
				}
			}
			
			if(!empty($arProduct['BARCODE']))
			{
				foreach($arProduct['BARCODE'] as $barcode)
				{
					$arProductBarcode = array(
						'BARCODE' => $barcode,
						'PRODUCT_ID' => $ID
					);
					\CCatalogStoreBarCode::add($arProductBarcode);
				}
			}
			unset($arProduct['BARCODE']);
		}
		
		if(isset($arProduct['VAT_ID']))
		{
			$vatName = ToLower($arProduct['VAT_ID']);
			if(!isset($this->catalogVats)) $this->catalogVats = array();
			if(!isset($this->catalogVats[$vatName]))
			{
				$dbRes = \CCatalogVat::GetList(array(), array('NAME'=>$arProduct['VAT_ID']), array('ID'));
				$arr = $dbRes->Fetch();
				if(!$arr && is_numeric($arProduct['VAT_ID']))
				{
					$dbRes = \CCatalogVat::GetList(array(), array('RATE'=>$arProduct['VAT_ID']), array('ID'));
					$arr = $dbRes->Fetch();					
				}
				if($arr)
				{
					$this->catalogVats[$vatName] = $arr['ID'];
				}
				else
				{
					$this->catalogVats[$vatName] = false;
				}
			}
			$arProduct['VAT_ID'] = $this->catalogVats[$vatName];
		}
		
		$arSet = array();
		if(isset($arProduct['SET_ITEM_ID']))
		{
			$arSetKeys = preg_grep('/^SET_/', array_keys($arProduct));
			foreach($arSetKeys as $setKey)
			{
				$arSet[substr($setKey, 4)] = $arProduct[$setKey];
				unset($arProduct[$setKey]);
			}
		}
		
		$arSet2 = array();
		if(isset($arProduct['SET2_ITEM_ID']))
		{
			$arSetKeys = preg_grep('/^SET2_/', array_keys($arProduct));
			foreach($arSetKeys as $setKey)
			{
				$arSet2[substr($setKey, 5)] = $arProduct[$setKey];
				unset($arProduct[$setKey]);
			}
		}
		
		$productChange = $productExists = false;
		//$dbRes = \CCatalogProduct::GetList(array(), array('ID'=>$ID), false, false, array_merge(array_keys($arProduct), array('TYPE', 'SUBSCRIBE')));
		$dbRes = $this->GetList(array(), array('ID'=>$ID), false, false, array_merge(array_keys($arProduct), array('TYPE', 'QUANTITY', 'SUBSCRIBE', 'SUBSCRIBE_ORIG', 'QUANTITY_TRACE', 'QUANTITY_TRACE_ORIG', 'CAN_BUY_ZERO', 'CAN_BUY_ZERO_ORIG', 'NEGATIVE_AMOUNT_TRACE_ORIG')));
		
		while($arCProduct = $dbRes->Fetch())
		{
			$productExists = true;
			$arCProduct['SUBSCRIBE'] = $arCProduct['SUBSCRIBE_ORIG'];
			$arCProduct['QUANTITY_TRACE'] = $arCProduct['QUANTITY_TRACE_ORIG'];
			$arCProduct['CAN_BUY_ZERO'] = $arCProduct['CAN_BUY_ZERO_ORIG'];
			$arCProduct['NEGATIVE_AMOUNT_TRACE'] = $arCProduct['NEGATIVE_AMOUNT_TRACE_ORIG'];
			
			/*Delete unchanged data*/
			if(defined('\Bitrix\Catalog\ProductTable::TYPE_SKU') && $arCProduct['TYPE']==\Bitrix\Catalog\ProductTable::TYPE_SKU && $this->saveProductWithOffers===false)
			{
				$arPrices = $arStores = array();
				continue;
			}
			if(isset($arProduct['QUANTITY']) && ($this->params['QUANTITY_AS_SUM_STORE']=='Y' || $this->params['QUANTITY_AS_SUM_PROPERTIES'])) unset($arProduct['QUANTITY']);
			if($this->params['ELEMENT_IMAGES_FORCE_UPDATE']!='Y')
			{
				foreach($arProduct as $k=>$v)
				{
					if($v==$arCProduct[$k]
						|| (in_array($k, array('WEIGHT', 'PURCHASING_PRICE')) && (float)$v==(float)$arCProduct[$k])
						|| (in_array($k, array('QUANTITY_TRACE', 'CAN_BUY_ZERO', 'NEGATIVE_AMOUNT_TRACE', 'SUBSCRIBE')) && $v==$arCProduct[$k.'_ORIG']))
					{
						unset($arProduct[$k]);
					}
				}
			}
			/*/Delete unchanged data*/
			if(!empty($arProduct))
			{
				$this->logger->AddElementChanges('ICAT_', $arProduct, $arCProduct);
				foreach(array('SUBSCRIBE', 'QUANTITY_TRACE', 'CAN_BUY_ZERO', 'QUANTITY', 'TYPE') as $key)
				{
					if(!isset($arProduct[$key])) $arProduct[$key] = (isset($arCProduct[$key.'_ORIG']) ? $arCProduct[$key.'_ORIG'] : $arCProduct[$key]);
				}
				//\CCatalogProduct::Update($arCProduct['ID'], $arProduct);
				$this->Update($arCProduct['ID'], $IBLOCK_ID, $arProduct);
				$productChange = true;
			}
		}
		
		if(!$productExists)
		{
			$this->GetDefaultProductFields($arProduct, $IBLOCK_ID);
			//\CCatalogProduct::Add($arProduct);
			$this->Add($arProduct, $IBLOCK_ID);
			$this->logger->AddElementChanges('ICAT_', $arProduct);
			$productChange = true;
		}
		
		if(!empty($arPrices))
		{
			$this->pricer->SavePrice($ID, $arPrices, $isOffer);
		}
		
		if(!empty($arStores))
		{
			$this->SaveStore($ID, $IBLOCK_ID, $arStores);
		}
		
		if(!empty($arSet))
		{
			$this->SaveCatalogSet($ID, $arSet, \CCatalogProductSet::TYPE_GROUP);
		}
		
		if(!empty($arSet2))
		{
			$this->SaveCatalogSet($ID, $arSet2, \CCatalogProductSet::TYPE_SET);
		}
		
		/*Update offer parent*/
		if($parentID && $productChange)
		{
			if(class_exists('\Bitrix\Catalog\Product\Sku'))
			{
				\Bitrix\Catalog\Product\Sku::updateAvailable($parentID);
			}
		}
		/*/Update offer parent*/
	}
	
	public function SaveStore($ID, $IBLOCK_ID, $arStores)
	{
		$isChanges = false;
		foreach($arStores as $sid=>$arFieldsStore)
		{
			if(isset($arFieldsStore['AMOUNT'])) $arFieldsStore['AMOUNT'] = $this->GetFloatVal($arFieldsStore['AMOUNT']);
			$dbRes = \CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID'=>$ID, 'STORE_ID'=>$sid), false, false, array_merge(array('ID'), (is_array($arFieldsStore) ? array_keys($arFieldsStore) : array())));
			while($arPrice = $dbRes->Fetch())
			{
				/*Delete unchanged data*/
				if($this->params['ELEMENT_IMAGES_FORCE_UPDATE']!='Y')
				{
					foreach($arFieldsStore as $k=>$v)
					{
						if($v==$arPrice[$k])
						{
							unset($arFieldsStore[$k]);
						}
					}
				}
				/*/Delete unchanged data*/
				if(!empty($arFieldsStore))
				{
					$this->logger->AddElementChanges("ICAT_STORE".$sid."_", $arFieldsStore, $arPrice);
					$arFieldsStore['PRODUCT_ID'] = $ID;
					\CCatalogStoreProduct::Update($arPrice["ID"], $arFieldsStore);
					$isChanges = true;
				}
			}
			
			if($dbRes->SelectedRowsCount()==0)
			{
				$arFieldsStore['PRODUCT_ID'] = $ID;
				$arFieldsStore['STORE_ID'] = $sid;
				\CCatalogStoreProduct::Add($arFieldsStore);
				$this->logger->AddElementChanges("ICAT_STORE".$sid."_", $arFieldsStore);
				$isChanges = true;
			}
		}
		
		if(1 || $isChanges) $this->SetProductQuantity($ID, $IBLOCK_ID);
	}
	
	public function SaveCatalogSet($ID, $arSet, $setType)
	{
		if($setType==\CCatalogProductSet::TYPE_GROUP) $fieldPrefix = 'ICAT_SET_';
		else $fieldPrefix = 'ICAT_SET2_';
		
		$arItems = array();
		foreach($arSet as $k=>$v)
		{
			$fieldSettings = $this->GetFieldSettings($fieldPrefix.$k);
			$sep = $this->params['ELEMENT_MULTIPLE_SEPARATOR'];
			if($fieldSettings['CHANGE_MULTIPLE_SEPARATOR']=='Y') $sep = $fieldSettings['MULTIPLE_SEPARATOR'];
			if(is_array($v)) $arVals = $v;
			else $arVals = array_map('trim', explode($sep, $v));
			foreach($arVals as $k2=>$v2)
			{
				if(strlen($v2) > 0)
				{
					if($k=='ITEM_ID')
					{
						$arProp = array('LINK_IBLOCK_ID' => $this->GetCurrentIblock());
						if($fieldSettings['CHANGE_LINKED_IBLOCK']=='Y' && !empty($fieldSettings['LINKED_IBLOCK']))
						{
							$arProp['LINK_IBLOCK_ID'] = $fieldSettings['LINKED_IBLOCK'];
						}
						$v2 = $this->GetIblockElementValue($arProp, $v2, $fieldSettings, false, true, true);
					}
					if(is_array($v2))
					{
						foreach($v2 as $k3=>$v3)
						{
							$arItems[$k2.($k3 > 0 ? '_'.$k3 : '')][$k] = $v3;
						}
					}
					else
					{
						$arItems[$k2][$k] = $v2;
						$arKeys = preg_grep('/^'.$k2.'_/', array_keys($arItems));
						foreach($arKeys as $key)
						{
							$arItems[$key][$k] = $v2;
						}
					}
				}
			}
		}

		$arElementIds = array();
		foreach($arItems as $k=>$v)
		{
			if(is_numeric($v['ITEM_ID'])) $arElementIds[] = $v['ITEM_ID'];
		}
		$arCheckedIds = array();
		if(!empty($arElementIds))
		{
			$dbRes = \CIblockElement::GetList(array(), array('ID'=>$arElementIds, '!CATALOG_TYPE'=>3), false, false, array('ID'));
			while($arr = $dbRes->Fetch())
			{
				$arCheckedIds[] = $arr['ID'];
			}
		}

		$arItemIds = array();
		foreach($arItems as $k=>$v)
		{
			if($v['ITEM_ID']==0 || $v['ITEM_ID']==$ID || !in_array($v['ITEM_ID'], $arCheckedIds))
			{
				unset($arItems[$k]);
				continue;
			}
			if(!isset($arItems[$k]['QUANTITY'])) $arItems[$k]['QUANTITY'] = 1;
			$arItems[$k]['QUANTITY'] = (int)$arItems[$k]['QUANTITY'];
			if($arItems[$k]['QUANTITY'] < 1) $arItems[$k]['QUANTITY'] = 1;
			
			$key = (isset($arItemIds[$arItems[$k]['ITEM_ID']]) ? $arItemIds[$arItems[$k]['ITEM_ID']] : false);
			if(!isset($arItems[$k]['ITEM_ID']) || $key!==false)
			{
				if($key!==false)
				{
					$arItems[$key]['QUANTITY'] += $arItems[$k]['QUANTITY'];
				}
				unset($arItems[$k]);
				continue;
			}
			$arItemIds[$arItems[$k]['ITEM_ID']] = $k;
		}

		$obSet = new \CCatalogProductSet;
		if(\CCatalogProductSet::isProductHaveSet($ID, $setType))
		{
			$arSets = \CCatalogProductSet::getAllSetsByProduct($ID, $setType);

			while(count($arSets) > 1)
			{
				$set = array_pop($arSets);
				$obSet->delete($set['SET_ID']);
			}
			
			$set = array_pop($arSets);
			if(empty($arItems))
			{
				$obSet->delete($set['SET_ID']);
			}
			else
			{
				$set['ITEMS'] = $arItems;
				$obSet->update($set['SET_ID'], $set);
			}
		}
		elseif(!empty($arItems))
		{
			$arFields = array(
				'TYPE' => $setType,
				'ITEM_ID' => $ID,
				'ITEMS' => $arItems
			);
			$obSet = new \CCatalogProductSet;
			$obSet->Add($arFields);
		}
	}
	
	public function GetDefaultProductFields(&$arProduct, $IBLOCK_ID=0)
	{
		if(!isset($arProduct['MEASURE']))
		{
			if(!isset($this->defaultMeasureID))
			{
				$this->defaultMeasureID = 0;
				$dbRes = \CCatalogMeasure::getList(array(), array('IS_DEFAULT'=>'Y'));
				if($arr = $dbRes->Fetch())
				{
					$this->defaultMeasureID = $arr['ID'];
				}
			}
			if($this->defaultMeasureID > 0) $arProduct['MEASURE'] = $this->defaultMeasureID;
		}
		if(!isset($arProduct['VAT_INCLUDED']))
		{
			if(!isset($this->defaultVatIncluded))
			{
				$this->defaultVatIncluded = \Bitrix\Main\Config\Option::get('catalog', 'default_product_vat_included', 'N');
			}
			$arProduct['VAT_INCLUDED'] = $this->defaultVatIncluded;
		}
		if(!isset($arProduct['VAT_ID']) && $IBLOCK_ID > 0)
		{
			if(!isset($this->defaultVatId))
			{
				$arMainCatalog = \CCatalogSku::GetInfoByIBlock($IBLOCK_ID);
				$this->defaultVatId = (int)$arMainCatalog['VAT_ID'];
			}
			$arProduct['VAT_ID'] = $this->defaultVatId;
		}
	}
	
	public function SetProductQuantity($ID, $IBLOCK_ID=0)
	{
		$asSumStore = (bool)($this->params['QUANTITY_AS_SUM_STORE']=='Y' && class_exists('\Bitrix\Catalog\StoreProductTable'));
		$asSumProps = (bool)($this->params['QUANTITY_AS_SUM_PROPERTIES']=='Y' && $IBLOCK_ID > 0);
		if(!$asSumStore && !$asSumProps) return;
		
		//$arCProduct = \CCatalogProduct::GetList(array(), array('ID'=>$ID), false, false, array('ID', 'QUANTITY', 'TYPE', 'SUBSCRIBE'))->Fetch();
		$arCProduct = $this->GetList(array(), array('ID'=>$ID), false, false, array('ID', 'QUANTITY', 'TYPE', 'SUBSCRIBE', 'SUBSCRIBE_ORIG', 'QUANTITY_RESERVED'))->Fetch();
		if($arCProduct && (defined('\Bitrix\Catalog\ProductTable::TYPE_SKU') && $arCProduct['TYPE']==\Bitrix\Catalog\ProductTable::TYPE_SKU && $this->saveProductWithOffers===false)) return;
			
		$quantity = 0;
		if($asSumStore)
		{
			if($arRes = \Bitrix\Catalog\StoreProductTable::getList(array('filter'=>array('PRODUCT_ID'=>$ID),'group'=>array('PRODUCT_ID'), 'runtime'=>array(new \Bitrix\Main\Entity\ExpressionField('SUM', 'SUM(AMOUNT)')), 'select'=>array('SUM')))->Fetch())
			{
				$quantity = (float)$arRes['SUM'];
			}
		}
		if($asSumProps)
		{
			$arProps = array();
			if(!$this->GetOfferParentId() && is_array($this->params['ELEMENT_PROPERTIES_FOR_QUANTITY'])) $arProps = $this->params['ELEMENT_PROPERTIES_FOR_QUANTITY'];
			elseif($this->GetOfferParentId() && is_array($this->params['OFFER_PROPERTIES_FOR_QUANTITY'])) $arProps = $this->params['OFFER_PROPERTIES_FOR_QUANTITY'];
			$arPropKeys = array();
			foreach($arProps as $propKey)
			{
				if(strpos($propKey, 'IP_PROP')==0) $arPropKeys[] = substr($propKey, 7);
			}
			$dbRes = \CIBlockElement::GetProperty($IBLOCK_ID, $ID, array(), Array("ID"=>$arPropKeys));
			while($arr = $dbRes->Fetch())
			{
				if(in_array($arr['ID'], $arPropKeys)) $quantity += (float)$arr['VALUE'];
			}
		}
		
		if($arCProduct)
		{
			if(isset($arCProduct['QUANTITY_RESERVED']) && $arCProduct['QUANTITY_RESERVED'] > 0) $quantity -= (int)$arCProduct['QUANTITY_RESERVED'];
			if($arCProduct['QUANTITY']==$quantity) return;
			$arProduct = array('QUANTITY' => $quantity);
			$this->logger->AddElementChanges('ICAT_', $arProduct, $arCProduct);
			foreach(array('SUBSCRIBE', 'QUANTITY_TRACE', 'CAN_BUY_ZERO', 'QUANTITY', 'TYPE') as $key)
			{
				if(!isset($arProduct[$key])) $arProduct[$key] = (isset($arCProduct[$key.'_ORIG']) ? $arCProduct[$key.'_ORIG'] : $arCProduct[$key]);
			}
			//\CCatalogProduct::Update($arCProduct['ID'], $arProduct);
			$this->Update($arCProduct['ID'], $IBLOCK_ID, $arProduct);
		}
		else
		{
			$arProduct = array(
				'ID' => $ID,
				'QUANTITY' => $quantity
			);
			if($this->GetOfferParentId() && defined('\Bitrix\Catalog\ProductTable::TYPE_OFFER'))
			{
				$arProduct['TYPE'] = \Bitrix\Catalog\ProductTable::TYPE_OFFER;
			}
			$this->GetDefaultProductFields($arProduct, $IBLOCK_ID);
			//\CCatalogProduct::Add($arProduct);
			$this->Add($arProduct, $IBLOCK_ID);
			$this->logger->AddElementChanges('ICAT_', $arProduct);
		}
		
		if($this->GetOfferParentId() && class_exists('\Bitrix\Catalog\Product\Sku'))
		{
			\Bitrix\Catalog\Product\Sku::updateAvailable($this->GetOfferParentId());
		}
	}
	
	public function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		return \CCatalogProduct::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
	}
	
	public function Add($arFields, $IBLOCK_ID=false, $boolCheck = true)
	{
		return \CCatalogProduct::Add($arFields, $boolCheck);
	}
	
	public function Update($ID, $IBLOCK_ID=false, $arFields=array())
	{
		return \CCatalogProduct::Update($ID, $arFields);
	}
	
	public function Delete($ID)
	{
		return \CCatalogProduct::Delete($ID);
	}
}