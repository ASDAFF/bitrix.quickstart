<?
/************************************
*
* general class
* last update 27.06.2014
*
************************************/

IncludeModuleLangFile(__FILE__);

class CRSQUICKBUYMain
{
	function OnBeforePrologElementUpdate()
	{
		global $APPLICATION;
		
		CRSQUICKBUYElements::CheckAutoRenewal();
		
		if(CRSQUICKBUYTab::NeedAddTab())
		{
			$VALUE_TYPE = 'F';
			$VALUE_TYPE = ($_REQUEST['redsign_quickbuy_discount_type']=='P'?'P':$VALUE_TYPE);
			$VALUE_TYPE = ($_REQUEST['redsign_quickbuy_discount_type']=='S'?'S':$VALUE_TYPE);
			$quickbuy_id = IntVal($_REQUEST['redsign_quickbuy_id']);
			$arFields = array(
				'ELEMENT_ID' => IntVal($_REQUEST['redsign_quickbuy_element_id']),
				'ACTIVE' => 'Y',
				'DATE_FROM' => $_REQUEST['redsign_quickbuy_date_from'],
				'DATE_TO' => $_REQUEST['redsign_quickbuy_date_to'],
				'DISCOUNT' => IntVal($_REQUEST['redsign_quickbuy_discount']),
				'VALUE_TYPE' => $VALUE_TYPE,
				'CURRENCY' => htmlspecialchars($_REQUEST['redsign_quickbuy_currency']),
				'AUTO_RENEWAL' => ($_REQUEST['redsign_quickbuy_auto_renewal']=='Y'?'Y':'N'),
				'QUANTITY' => IntVal($_REQUEST['redsign_quickbuy_quantity']),
			);
			
			if($quickbuy_id>0 && $_REQUEST['redsign_quickbuy_active']=='Y')
			{
				CRSQUICKBUYElements::Update($quickbuy_id, $arFields);
			} elseif($quickbuy_id<1 && $_REQUEST['redsign_quickbuy_active']=='Y') {
				CRSQUICKBUYElements::Add($arFields);
			} elseif($quickbuy_id>0 && $_REQUEST['redsign_quickbuy_active']!='Y') {
				CRSQUICKBUYElements::Delete($quickbuy_id);
			}
		}
	}
	
	function OnOrderUpdate($ID, $arFields)
	{
		$bitrix_default_quantity_trace = COption::GetOptionString('catalog', 'default_quantity_trace', 'N');
		if(CModule::IncludeModule('sale') && $bitrix_default_quantity_trace=='Y')
		{
			$arOrder = array('ORDER_ID' => 'DESC');
			$arFilter = array(
				'FUSER_ID' => CSaleBasket::GetBasketUserID(),
				'LID' => SITE_ID,
				'ORDER_ID' => $ID
			);
			$arNavStartParams = array('nTopCount'=>1);
			$res = CSaleBasket::GetList($arOrder, $arFilter, false, $arNavStartParams);
			if ($arOrder = $res->Fetch())
			{
				$time = ConvertTimeStamp(time(),'FULL');
				$arQBFilter = array('ELEMENT_ID' => $arOrder['PRODUCT_ID'],'DATE_FROM' => $time, 'DATE_TO' => $time, 'QUANTITY' => 0);
				$res2 = CRSQUICKBUYElements::GetList(array('SORT' => 'ASC'), $arQBFilter);
				if($data = $res2->Fetch())
				{
					$arFieldsNew['QUANTITY'] = round($data['QUANTITY'] - 1);
					if($arFieldsNew['QUANTITY']<1)
					{
						CRSQUICKBUYElements::Delete($data['ID']);
					} else {
						CRSQUICKBUYElements::Update($data['ID'], $arFieldsNew);
					}
				}
			}
		}
	}
	
	function GetTimeLimit($DATE_TO)
	{
		$SECOND_IN_DAY = 86400;
		$SECOND_IN_HOUR = 3600;
		$SECOND_IN_MINUTE = 60;
		$arTimeLimit = array();
		
		if(!is_array($DATE_TO) && $DATE_TO!='')
		{
			$TIME_LIMIT = strtotime($DATE_TO) - strtotime(date('d.m.Y H:i:s'));
			
			$C_DAYS = floor($TIME_LIMIT/$SECOND_IN_DAY);
			$C_HOUR = floor(($TIME_LIMIT-$C_DAYS*$SECOND_IN_DAY)/$SECOND_IN_HOUR);
			$C_MINUTE = floor(($TIME_LIMIT-$C_DAYS*$SECOND_IN_DAY-$C_HOUR*$SECOND_IN_HOUR)/$SECOND_IN_MINUTE);
			$C_SECOND = floor($TIME_LIMIT-$C_DAYS*$SECOND_IN_DAY-$C_HOUR*$SECOND_IN_HOUR-$C_MINUTE*$SECOND_IN_MINUTE);
			
			$arTimeLimit = array(
				'DATE_NOW' => strtotime(date('d.m.Y H:i:s')),
				'DATE_TO' => strtotime($DATE_TO),
				'TIME_LIMIT' => $TIME_LIMIT,
				'DAYS' => $C_DAYS,
				'HOUR' => $C_HOUR,
				'MINUTE' => $C_MINUTE,
				'SECOND' => $C_SECOND,
			);
			return $arTimeLimit;
		}
	}
}