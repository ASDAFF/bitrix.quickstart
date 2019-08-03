<?php
namespace Bitrix\EsolImportxml\DataManager;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Price
{
	protected $ie = null;
	protected $logger = null;
	protected $params = null;
	
	public function __construct($ie=false)
	{
		$this->ie = $ie;
		$this->logger = $this->ie->logger;
		$this->params = $this->ie->params;
	}
	
	public function SavePrice($ID, $arPrices, $isOffer = false)
	{
		$basePriceId = $this->GetBasePriceId();
		if(count($arPrices) > 1 && isset($arPrices[$basePriceId]))
		{
			$arPricesOld = $arPrices;
			$arPrices = array($basePriceId => $arPricesOld[$basePriceId]);
			foreach($arPricesOld as $gid=>$arFieldsPrice)
			{
				if($gid!=$basePriceId)
				{
					$arPrices[$gid] = $arFieldsPrice;
				}
			}
		}
		
		foreach($arPrices as $gid=>$arFieldsPrice)
		{
			$pKey = ($isOffer ? 'OFFER_' : '').'ICAT_PRICE'.$gid.'_PRICE';
			$noCurrency = false;
			$arFieldsPriceExtra = array();
			foreach($arFieldsPrice as $k=>$v)
			{
				if(strpos($k, 'EXTRA')===0)
				{
					if($k=='EXTRA') $arFieldsPriceExtra['PERCENTAGE'] = $v;
					else $arFieldsPriceExtra[substr($k, 6)] = $v;
				}
			}
			if(!empty($arFieldsPriceExtra))
			{
				$arFilter = array();
				if($arFieldsPriceExtra['ID']) $arFilter = array('ID' => $arFieldsPriceExtra['ID']);
				else
				{
					if(!$arFieldsPriceExtra['NAME'] && $arFieldsPriceExtra['PERCENTAGE']) $arFieldsPriceExtra['NAME'] = $arFieldsPriceExtra['PERCENTAGE'].'%';
					if($arFieldsPriceExtra['NAME']) $arFilter = array('NAME' => $arFieldsPriceExtra['NAME']);
				}	
				if(!empty($arFilter))
				{
					if(!isset($this->arPriceExtras)) $this->arPriceExtras = array();
					$dbRes = \CExtra::GetList(array(), $arFilter, false, array('nTopCount'=>1), array('ID'));
					if($arExtra = $dbRes->Fetch())
					{
						if(count($arFieldsPriceExtra) > 0)
						{
							\CExtra::Update($arExtra['ID'], $arFieldsPriceExtra);
						}
						$arFieldsPrice['EXTRA_ID'] = $this->arPriceExtras[$arFieldsPrice['EXTRA']] = $arExtra['ID'];
					}
					else
					{
						$pid = \CExtra::Add($arFieldsPriceExtra);
						if($pid > 0)
						{
							$arFieldsPrice['EXTRA_ID'] = $this->arPriceExtras[$arFieldsPrice['EXTRA']] = $pid;
						}
					}
				}
			}
			
			$extKeys = preg_grep('/^PRICE\|.*QUANTITY_/', array_keys($arFieldsPrice));
			if((!isset($arFieldsPrice['PRICE']) || $arFieldsPrice['PRICE']==='')
				&& (!isset($arFieldsPrice['PRICE_EXT']) || $arFieldsPrice['PRICE_EXT']==='')
				&& (!isset($arFieldsPrice['EXTRA_ID'])) && empty($extKeys)) continue;

			$recalcPrice = (bool)($gid!=$basePriceId && isset($arFieldsPrice['EXTRA_ID']));
			$recalcPrice2 = (bool)($gid==$basePriceId);
			if(!$arFieldsPrice['CURRENCY'])
			{
				$arFieldsPrice['CURRENCY'] = $this->params['DEFAULT_CURRENCY'];
				$noCurrency = true;
			}
			$arFieldsPrice['CURRENCY'] = $this->GetCurrencyVal($arFieldsPrice['CURRENCY']);
			
			$arSubPrices = array();
			if(isset($arFieldsPrice['PRICE_EXT']) && !empty($arFieldsPrice['PRICE_EXT']))
			{
				$arParts = array_map('trim', explode(';', $arFieldsPrice['PRICE_EXT']));
				foreach($arParts as $part)
				{
					list($qf, $qt, $p, $c) = explode(':', $part);
					$arSubPrices[] = array(
						'QUANTITY_FROM' => $qf,
						'QUANTITY_TO' => $qt,
						'PRICE' => $p,
						'CURRENCY' => $c
					);
				}
				unset($arFieldsPrice['PRICE_EXT']);
				$noCurrency = false;
			}
			elseif(isset($arFieldsPrice['QUANTITY_FROM']) || isset($arFieldsPrice['QUANTITY_TO']))
			{
				if(isset($arFieldsPrice['PRICE']))
				{
					$arPrice = $arFieldsPrice['PRICE'];
					if(!is_array($arPrice)) $arPrice = array($arPrice);
					else $arPrice = array_values($arPrice);
					$arCurrencies = $arFieldsPrice['CURRENCY'];
					if(!is_array($arCurrencies)) $arCurrencies = array($arCurrencies);
					else $arCurrencies = array_values($arCurrencies);
					$arQuantityFrom = (isset($arFieldsPrice['QUANTITY_FROM']) ? $arFieldsPrice['QUANTITY_FROM'] : '');
					if(!is_array($arQuantityFrom)) $arQuantityFrom = array($arQuantityFrom);
					else $arQuantityFrom = array_values($arQuantityFrom);
					$arQuantityTo = (isset($arFieldsPrice['QUANTITY_TO']) ? $arFieldsPrice['QUANTITY_TO'] : '');
					if(!is_array($arQuantityTo)) $arQuantityTo = array($arQuantityTo);
					else $arQuantityTo = array_values($arQuantityTo);
					
					foreach($arPrice as $k=>$price)
					{
						$arSubPrices[] = array(
							'QUANTITY_FROM' => (isset($arQuantityFrom[$k]) ? $arQuantityFrom[$k] : $arQuantityFrom[0]),
							'QUANTITY_TO' => (isset($arQuantityTo[$k]) ? $arQuantityTo[$k] : $arQuantityTo[0]),
							'PRICE' => $price,
							'CURRENCY' => (isset($arCurrencies[$k]) ? $arCurrencies[$k] : $arCurrencies[0])
						);
					}
				}
				unset($arFieldsPrice['PRICE'], $arFieldsPrice['QUANTITY_FROM'], $arFieldsPrice['QUANTITY_TO']);
			}
			elseif(!empty($extKeys))
			{
				foreach($extKeys as $extKey)
				{
					$arPriceKeys = explode('|', $extKey);
					$arSubPrice = array(array_shift($arPriceKeys) => $arFieldsPrice[$extKey]);
					foreach($arPriceKeys as $v)
					{
						$arVal = explode('=', $v);
						$arSubPrice[$arVal[0]] = $arVal[1];
					}
					$arSubPrices[] = $arSubPrice;
					unset($arFieldsPrice[$extKey]);
				}
			}
			if(isset($arFieldsPrice['PRICE']) && is_array($arFieldsPrice['PRICE'])) $arFieldsPrice['PRICE'] = current($arFieldsPrice['PRICE']);
			if(isset($arFieldsPrice['PRICE'])) $arSubPrices[] = array('PRICE' => $arFieldsPrice['PRICE']);
			elseif(isset($arFieldsPrice['EXTRA_ID']))
			{
				//$arSubPrices[] = array('EXTRA_ID' => $arFieldsPrice['EXTRA_ID']);
				$arSubPrices = array();
				$dbRes = $this->GetList(array('ID'=>'ASC'), array('PRODUCT_ID'=>$ID, 'CATALOG_GROUP_ID'=>$basePriceId), false, false, array('ID', 'QUANTITY_FROM', 'QUANTITY_TO'));
				while($arr = $dbRes->Fetch())
				{
					$arSubPrices[] = array('EXTRA_ID'=>$arFieldsPrice['EXTRA_ID'], 'QUANTITY_FROM'=>$arr['QUANTITY_FROM'], 'QUANTITY_TO'=>$arr['QUANTITY_TO']);
				}
			}

			$arFieldsPriceOrig = $arFieldsPrice;
			$arUpdatedIds = array();
			$bDeleteOld = true;
			foreach($arSubPrices as $arSubPrice)
			{
				$arFieldsPrice = array_merge($arFieldsPriceOrig, $arSubPrice);
				if(!isset($arFieldsPrice['QUANTITY_FROM'])) $arFieldsPrice['QUANTITY_FROM'] = false;
				if(!isset($arFieldsPrice['QUANTITY_TO'])) $arFieldsPrice['QUANTITY_TO'] = false;
				
				$arKeys = array_unique(array_merge(array('ID', 'PRODUCT_ID', 'CATALOG_GROUP_ID', 'QUANTITY_FROM', 'QUANTITY_TO', 'CURRENCY', 'PRICE'), array_keys($arFieldsPrice)));
				$arFilter = array('PRODUCT_ID'=>$ID, 'CATALOG_GROUP_ID'=>$gid);
				if(!empty($arUpdatedIds)) $arFilter['!ID'] = $arUpdatedIds;
				$dbRes = $this->GetList(array('ID'=>'ASC'), $arFilter, false, false, $arKeys);
				if($arPrice = $dbRes->Fetch())
				{
					if($arFieldsPrice['PRICE']!=='-')
					{
						if($recalcPrice)
						{
							$arFieldsPrice['PRODUCT_ID'] = $ID;
							$arFieldsPrice['CATALOG_GROUP_ID'] = $gid;
						}
						else
						{
							/*Delete unchanged data*/
							if($noCurrency) unset($arFieldsPrice['CURRENCY']);
							if($this->params['ELEMENT_IMAGES_FORCE_UPDATE']!='Y')
							{
								foreach($arFieldsPrice as $k=>$v)
								{
									if($v==$arPrice[$k] && $k!='QUANTITY_FROM' && $k!='QUANTITY_TO')
									{
										unset($arFieldsPrice[$k]);
									}
								}
								if(isset($arFieldsPrice['QUANTITY_FROM']) && isset($arFieldsPrice['QUANTITY_TO']) && $arFieldsPrice['QUANTITY_FROM']==$arPrice['QUANTITY_FROM'] && $arFieldsPrice['QUANTITY_TO']==$arPrice['QUANTITY_TO'])
								{
									unset($arFieldsPrice['QUANTITY_FROM'], $arFieldsPrice['QUANTITY_TO']);
								}
							}
							/*/Delete unchanged data*/
						}
						if(!empty($arFieldsPrice))
						{
							$this->logger->AddElementChanges("ICAT_PRICE".$gid.'_', $arFieldsPrice, $arPrice);
							if($recalcPrice2)
							{
								$arFieldsPrice = array_merge($arPrice, $arFieldsPrice);
								unset($arFieldsPrice['ID']);
							}
							$arFieldsPrice['PRODUCT_ID'] = $ID;
							$this->Update($arPrice["ID"], $arFieldsPrice, ($recalcPrice || $recalcPrice2));
						}
					}
					else
					{
						$this->Delete($arPrice["ID"]);
						$this->logger->AddElementChanges("ICAT_PRICE".$gid.'_', $arFieldsPrice, $arPrice);
					}
					$arUpdatedIds[] = $arPrice["ID"];
				}
				else
				{
					$bDeleteOld = false;
					if($arFieldsPrice['PRICE']!=='-')
					{
						$arFieldsPrice['PRODUCT_ID'] = $ID;
						$arFieldsPrice['CATALOG_GROUP_ID'] = $gid;
						$priceId = $this->Add($arFieldsPrice, ($recalcPrice || $recalcPrice2));
						$this->logger->AddElementChanges("ICAT_PRICE".$gid.'_', $arFieldsPrice);
						if($priceId) $arUpdatedIds[] = $priceId;
					}
				}
			}
			
			if($bDeleteOld)
			{
				$dbRes = $this->GetList(array('ID'=>'ASC'), array('PRODUCT_ID'=>$ID, 'CATALOG_GROUP_ID'=>$gid, '!ID' => $arUpdatedIds), false, false, array('ID'));
				while($arPrice = $dbRes->Fetch())
				{
					$this->Delete($arPrice["ID"]);
				}
			}
		}
	}
	
	public function GetBasePriceId()
	{
		if(!$this->catalogBasePriceId)
		{
			$arBasePrice = \CCatalogGroup::GetBaseGroup();
			$this->catalogBasePriceId = $arBasePrice['ID'];
		}
		return $this->catalogBasePriceId;
	}
	
	public function GetCurrencyVal($val)
	{
		if(!isset($this->arCurrencies))
		{
			$this->arCurrencies = array();
			if(Loader::includeModule('currency'))
			{
				$dbRes = \CCurrency::GetList(($by="sort"), ($order="asc"), LANGUAGE_ID);
				while($arr = $dbRes->Fetch())
				{
					$this->arCurrencies[$arr['CURRENCY']] = array(
						'FULL_NAME' => ToLower($arr['FULL_NAME']),
						'FORMAT_STRING' => ToLower(trim($arr['FORMAT_STRING'], '#. ')),
					);
				}
			}
		}
		if(!isset($this->arCurrencies[$val]))
		{
			if($val=='RUR' && isset($this->arCurrencies['RUB'])) $val = 'RUB';
			elseif($val=='€' && isset($this->arCurrencies['EUR'])) $val = 'EUR';
			elseif($val=='$' && isset($this->arCurrencies['USD'])) $val = 'USD';
			else
			{
				$compVal = ToLower(trim($val, '#. '));
				foreach($this->arCurrencies as $k=>$v)
				{
					if(in_array($compVal, $v))
					{
						$val = $k;
						break;
					}
				}
			}
		}
		if(!isset($this->arCurrencies[$val]))
		{
			$val = $this->params['DEFAULT_CURRENCY'];
		}
		return $val;
	}
	
	public function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		return \CPrice::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
	}
	
	public function Add($arFields, $boolRecalc = false)
	{
		return \CPrice::Add($arFields, $boolRecalc);
	}
	
	public function Update($ID, $arFields, $boolRecalc = false)
	{
		return \CPrice::Update($ID, $arFields, $boolRecalc);
	}
	
	public function Delete($ID)
	{
		return \CPrice::Delete($ID);
	}
}