<?
/************************************
*
* Universal Extensions
* last update 21.07.2014
*
************************************/

IncludeModuleLangFile(__FILE__);

class RSDevFunc
{
	public static function Init($arInit)
	{
		global $APPLICATION;
		
		if(!is_array($arInit))
			$arInit = array($arInit);
		
		if(in_array("jsfunc",$arInit))
		{
			$APPLICATION->AddHeadString("<script>var RSDevFunc_BasketEndWord_end1 = \"".GetMessage("RSDF_END_1")."\";var RSDevFunc_BasketEndWord_end2 = \"".GetMessage("RSDF_END_2")."\";var RSDevFunc_BasketEndWord_end3 = \"".GetMessage("RSDF_END_3")."\";</script>");
			$APPLICATION->AddHeadScript("/bitrix/js/redsign.devfunc/script.js");
		}
	}
	
	public static function GetDataForProductItem(&$arItems,$params=array())
	{
		if(CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog') && is_array($arItems) && count($arItems)>0)
		{
			// prepare data
			$arElements = array();
			$arElementsIDs = array();
			foreach($arItems as $iKeyItem => $arItem)
			{
				$arElementsIDs[] = $arItem['ID'];
				$arElements[$arItems[$iKeyItem]['ID']] = &$arItems[$iKeyItem];
				if(is_array($arItems[$iKeyItem]['OFFERS']))
				{
					foreach($arItems[$iKeyItem]['OFFERS'] as $iOfferKey => $arOffer)
					{
						$arElementsIDs[] = $arOffer['ID'];
						$arElements[$arOffer['ID']] = &$arItems[$iKeyItem]['OFFERS'][$iOfferKey];
					}
				}
			}
			// /prepare data
			
			$iTime = ConvertTimeStamp(time(),'FULL');
			// add quickbuy
			if(CModule::IncludeModule('redsign.quickbuy'))
			{
				$arFilter = array(
					'DATE_FROM' => $iTime,
					'DATE_TO' => $iTime,
					'QUANTITY' => 0,
					'ELEMENT_ID' => $arElementsIDs,
				);
				$dbRes = CRSQUICKBUYElements::GetList( array('ID'=>'SORT'), $arFilter);
				while($arData = $dbRes->Fetch())
				{
					if(array_key_exists($arData['ELEMENT_ID'], $arElements))
					{
						$arElements[$arData['ELEMENT_ID']]['QUICKBUY'] = $arData;
						$arElements[$arData['ELEMENT_ID']]['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arData['DATE_TO']);
					}
				}
			}
			// /add quickbuy
			// add da2
			if(CModule::IncludeModule('redsign.daysarticle2'))
			{
				$arFilter = array(
					'DATE_FROM' => $iTime,
					'DATE_TO' => $iTime,
					'QUANTITY' => 0,
					'ELEMENT_ID' => $arElementsIDs,
				);
				$dbRes = CRSDA2Elements::GetList(array('ID'=>'SORT'), $arFilter);
				while($arData = $dbRes->Fetch())
				{
					if(array_key_exists($arData['ELEMENT_ID'], $arElements))
					{
						$arElements[$arData['ELEMENT_ID']]['DAYSARTICLE2'] = $arData;
						$arElements[$arData['ELEMENT_ID']]['DAYSARTICLE2']['DINAMICA_EX'] = CRSDA2Elements::GetDinamica($arData);
					}
				}
			}
			// /add da2
			
			foreach($arElements as $iElementId => $arElement)
			{
				$CODE = false;
				if(isset($arElement['PROPERTIES'][$params['PROP_MORE_PHOTO']]['ID']))
				{
					$CODE = $params['PROP_MORE_PHOTO'];
				} elseif(isset($arElement['PROPERTIES'][$params['PROP_SKU_MORE_PHOTO']]['ID']))
				{
					$CODE = $params['PROP_SKU_MORE_PHOTO'];
				}
				// add images
				if($CODE && !is_array($arElement['PROPERTIES'][$CODE]['VALUE'] ) && IntVal($arElement['PROPERTIES'][$CODE]['VALUE'])>0)
				{
					$arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'] = array(0 => array('RESIZE' => CFile::ResizeImageGet($arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'],array('width'=>$params['MAX_WIDTH'],'height'=>$params['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array())));
				} elseif($CODE && is_array($arElement['PROPERTIES'][$CODE]['VALUE']) && count($arElement['PROPERTIES'][$CODE]['VALUE'])>0) {
					foreach($arElement['PROPERTIES'][$CODE]['VALUE'] as $iFileKey => $iFileId)
					{
						$arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'][$iFileKey] = CFile::GetFileArray($iFileId);
						$arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'][$iFileKey]['RESIZE'] = CFile::ResizeImageGet($arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'][$iFileKey],array('width'=>$params['MAX_WIDTH'],'height'=>$params['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
					}
				}
				if($arElements[$iElementId]['PREVIEW_PICTURE']['SRC'] != '')
				{
					$arElements[$iElementId]['PREVIEW_PICTURE'] = (is_array($arElements[$iElementId]['PREVIEW_PICTURE'])>0 ? $arElements[$iElementId]['PREVIEW_PICTURE'] : CFile::GetFileArray($arElements[$iElementId]['PREVIEW_PICTURE']));
					$arElements[$iElementId]['PREVIEW_PICTURE']['RESIZE'] = CFile::ResizeImageGet($arElements[$iElementId]['PREVIEW_PICTURE'],array('width'=>$params['MAX_WIDTH'],'height'=>$params['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
				}
				if($arElements[$iElementId]['DETAIL_PICTURE']['SRC'] != '')
				{
					$arElements[$iElementId]['DETAIL_PICTURE'] = (is_array($arElements[$iElementId]['DETAIL_PICTURE'])>0 ? $arElements[$iElementId]['DETAIL_PICTURE'] : CFile::GetFileArray($arElements[$iElementId]['DETAIL_PICTURE']));
					$arElements[$iElementId]['DETAIL_PICTURE']['RESIZE'] = CFile::ResizeImageGet($arElements[$iElementId]['DETAIL_PICTURE'],array('width'=>$params['MAX_WIDTH'],'height'=>$params['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
				}
				// /add images
				// have set?
				$arElements[$iElementId]['HAVE_SET'] = CCatalogProductSet::isProductHaveSet($arElement['ID'], CCatalogProductSet::TYPE_GROUP);
				// /have set?
			}
			
			foreach($arItems as $iKeyItem => $arItem)
			{
				$CODE = $params['PROP_MORE_PHOTO'];
				$HAVE_OFFERS = (is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) ? true : false;
				if($HAVE_OFFERS) { $PRODUCT = &$arItem['OFFERS'][0]; } else { $PRODUCT = &$arItem; }
				// first image
				$arItems[$iKeyItem]['FIRST_PIC'] = false;
				$arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = false;
				if(is_array($PRODUCT['PREVIEW_PICTURE']['RESIZE']) && $params['PAGE']!='detail')
				{
					$arItems[$iKeyItem]['FIRST_PIC'] = $PRODUCT['PREVIEW_PICTURE'];
				} elseif(is_array($PRODUCT['DETAIL_PICTURE']['RESIZE']))
				{
					$arItems[$iKeyItem]['FIRST_PIC'] = $PRODUCT['DETAIL_PICTURE'];
					$arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = $PRODUCT['DETAIL_PICTURE'];
				} elseif($CODE && is_array($PRODUCT['PROPERTIES'][$CODE]['VALUE'][0]['RESIZE']))
				{
					$arItems[$iKeyItem]['FIRST_PIC'] = $PRODUCT['PROPERTIES'][$CODE]['VALUE'][0];
					$arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = $PRODUCT['PROPERTIES'][$CODE]['VALUE'][0];
				} elseif(is_array($arItem['PREVIEW_PICTURE']['RESIZE']) && $params['PAGE']!='detail')
				{
					$arItems[$iKeyItem]['FIRST_PIC'] = $arItem['PREVIEW_PICTURE'];
				} elseif(is_array($arItem['DETAIL_PICTURE']['RESIZE']))
				{
					$arItems[$iKeyItem]['FIRST_PIC'] = $arItem['DETAIL_PICTURE'];
					$arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = $arItem['DETAIL_PICTURE'];
				} elseif(!empty($arItem['OFFERS']))
				{
					$CODE = $params['PROP_SKU_MORE_PHOTO'];
					foreach($arItem['OFFERS'] as $arOffer)
					{
						if(is_array($arOffer['PROPERTIES'][$CODE]['VALUE'][0]['RESIZE']))
						{
							$arItems[$iKeyItem]['FIRST_PIC'] = $arOffer['PROPERTIES'][$CODE]['VALUE'][0];
							$arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = $arOffer['PROPERTIES'][$CODE]['VALUE'][0];
							break;
						}
					}
				}
				// /first image
			}
		}
	}
	
	public static function GetNoPhoto($arSizes)
	{
		$return = false;
		$fileid = COption::GetOptionInt('redsign.devfunc', 'no_photo_fileid', 0);
		if($fileid>0)
		{
			$return = CFile::ResizeImageGet($fileid,array('width'=>$arSizes['MAX_WIDTH'],'height'=>$arSizes['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
		}
		return $return;
	}
	
	public static function BasketEndWord($num,$end1="",$end2="",$end3="")
	{
		if($end1=='') $end1 = GetMessage('RSDF.END_1');
		if($end2=='') $end2 = GetMessage('RSDF.END_2');
		if($end3=='') $end3 = GetMessage('RSDF.END_3');
		$status = array($end1,$end2,$end3);
		$array = array(2,0,1,1,1,2);
		return $status[($num%100>4 && $num%100<20)? 2 : $array[($num%10<5)?$num%10:5]];
	}
	
	public static function DeviceDetect()
	{
		$return = array(
			"DEVICE" => "pc",
			//"OS" => "",
			//"BROWSER" => "",
		);
		
		$wap_profile = $_SERVER["HTTP_X_WAP_PROFILE"];
		$user_agent = $_SERVER["HTTP_USER_AGENT"];
		
		if(strpos($user_agent,"Windows Phone")>0)
		{
			$return = array(
				"DEVICE" => "smartphone",
			);
		} elseif(strpos($user_agent,"Android")>0)
		{
			if(isset($wap_profile) && $wap_profile!="")
			{
				$return = array(
					"DEVICE" => "smartphone",
				);
			} else {
				$return = array(
					"DEVICE" => "tab",
				);
			}
		} elseif(strpos($user_agent,"iPhone"))
		{
			$return = array(
				"DEVICE" => "smartphone",
			);
		} elseif(strpos($user_agent,"iPad"))
		{
			$return = array(
				"DEVICE" => "tab",
			);
		} elseif(strpos($user_agent,"Windows")>0)
		{
			$return = array(
				"DEVICE" => "pc",
			);
		} 
		return $return;
	}
}