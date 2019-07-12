<?
/************************************
*
* general class
* last update 29.01.2015
*
************************************/

IncludeModuleLangFile(__FILE__);

class CRSDA2Main
{
	public static function OnBeforePrologElementUpdate() {
		global $APPLICATION;
		
		CRSDA2Elements::CheckAutoRenewal();
		
		if(CRSDA2Tab::NeedAddTab()) {
			$VALUE_TYPE = 'F';
			$VALUE_TYPE = ($_REQUEST['redsign_daysarticle2_discount_type']=='P'?'P':$VALUE_TYPE);
			$VALUE_TYPE = ($_REQUEST['redsign_daysarticle2_discount_type']=='S'?'S':$VALUE_TYPE);
			$daysarticle2_id = IntVal($_REQUEST['redsign_daysarticle2_id']);
			
			if(is_array($_REQUEST['redsign_daysarticle2_dinamics_custom_percent']) && count($_REQUEST['redsign_daysarticle2_dinamics_custom_percent'])>0) {
				$arDinamicDataTogether = array();
				foreach($_REQUEST['redsign_daysarticle2_dinamics_custom_percent'] as $k => $v) {
					if(IntVal($v)>0 && (IntVal($_REQUEST['redsign_daysarticle2_dinamics_custom_time'][$k])>0 || $_REQUEST['redsign_daysarticle2_dinamics_custom_time'][$k]=='*')) {
						$arDinamicDataTogether[$_REQUEST['redsign_daysarticle2_dinamics_custom_percent'][$k]] = $_REQUEST['redsign_daysarticle2_dinamics_custom_time'][$k];
					}
				}
				asort($arDinamicDataTogether);
			}
			$arFields = array(
				'ELEMENT_ID' => IntVal($_REQUEST['redsign_daysarticle2_element_id']),
				'ACTIVE' => 'Y',
				'DATE_FROM' => $_REQUEST['redsign_daysarticle2_date_from'],
				'DATE_TO' => $_REQUEST['redsign_daysarticle2_date_to'],
				'DISCOUNT' => IntVal($_REQUEST['redsign_daysarticle2_discount']),
				'VALUE_TYPE' => $VALUE_TYPE,
				'CURRENCY' => htmlspecialchars($_REQUEST['redsign_daysarticle2_currency']),
				'QUANTITY' => IntVal($_REQUEST['redsign_daysarticle2_quantity']),
				'AUTO_RENEWAL' => ($_REQUEST['redsign_daysarticle2_auto_renewal']=='Y' ? 'Y' : 'N'),
				'DINAMICA' => ($_REQUEST['redsign_daysarticle2_dinamic']=='evenly' ? 'evenly' : 'custom'),
				'DINAMICA_DATA' => serialize($arDinamicDataTogether),
			);
			
			if($daysarticle2_id>0 && $_REQUEST['redsign_daysarticle2_active']=='Y') {
				CRSDA2Elements::Update($daysarticle2_id, $arFields);
			} elseif($daysarticle2_id<1 && $_REQUEST['redsign_daysarticle2_active']=='Y') {
				CRSDA2Elements::Add($arFields);
			} elseif($daysarticle2_id>0 && $_REQUEST['redsign_daysarticle2_active']!='Y') {
				CRSDA2Elements::Delete($daysarticle2_id);
			}
		}
	}

	public static function OnSaleComponentOrderOneStepComplete($ID, $arFields) {
		$bitrix_default_quantity_trace = COption::GetOptionString('catalog', 'default_quantity_trace', 'N');
		if(CModule::IncludeModule('sale') && CModule::IncludeModule('iblock') && $bitrix_default_quantity_trace=='Y') {
			$arOrder = array('ORDER_ID' => 'DESC');
			$arFilter = array(
				'FUSER_ID' => CSaleBasket::GetBasketUserID(),
				'LID' => SITE_ID,
				'ORDER_ID' => $ID,
			);
			$dbBasketItems = CSaleBasket::GetList($arOrder, $arFilter);
			while($arItems = $dbBasketItems->Fetch()) {
				$time = ConvertTimeStamp(time(),'FULL');
				$arQBFilter = array('ELEMENT_ID' => $arItems['PRODUCT_ID'], 'DATE_FROM' => $time, 'DATE_TO' => $time, 'QUANTITY' => 0);
				$res2 = CRSDA2Elements::GetList(array('SORT' => 'ASC'), $arQBFilter);
				if($data = $res2->Fetch()) {
					$arFieldsNew = array(
						'ELEMENT_ID' => $arItems['PRODUCT_ID'],
						'QUANTITY' => round($data['QUANTITY'] - $arItems['QUANTITY'])
					);
					if($arFieldsNew['QUANTITY']<1) {
						CRSDA2Elements::Delete($data['ID']);
					} else {
						CRSDA2Elements::Update($data['ID'], $arFieldsNew);
					}
				} else {
					$res = CIBlockElement::GetByID($arItems["PRODUCT_ID"]);
					if($arElement = $res->GetNext()) {
						$db_props = CIBlockElement::GetProperty($arElement['IBLOCK_ID'], $arElement['ID'], array('ID'=>'ASC'), array('CODE'=>'CML2_LINK'));
						if($arProp = $db_props->Fetch()) {
							$arQBFilter = array('ELEMENT_ID' => $arProp['VALUE'], 'DATE_FROM' => $time, 'DATE_TO' => $time, 'QUANTITY' => 0);
							$res2 = CRSDA2Elements::GetList(array('SORT' => 'ASC'), $arQBFilter);
							if($data = $res2->Fetch()) {
								$arFieldsNew = array(
									'ELEMENT_ID' => $arProp['VALUE'],
									'QUANTITY' => round($data['QUANTITY'] - $arItems['QUANTITY'])
								);
								if($arFieldsNew['QUANTITY']<1) {
									CRSDA2Elements::Delete($data['ID']);
								} else {
									CRSDA2Elements::Update($data['ID'], $arFieldsNew);
								}
							}
						}
					}
				}
			}
		}
	}
	
	public static function GetDinamica($arFields) {
		$SECOND_IN_DAY = 86400;
		$SECOND_IN_HOUR = 3600;
		$SECOND_IN_MINUTE = 60;
		$NOW_HOUR = IntVal(date("H"));
		$arDinamica = array();
		
		if($arFields["DATE_FROM"]!="" && $arFields["DATE_TO"]!="") {
			$DATE_FROM = $arFields["DATE_FROM"];
			$DATE_TO = $arFields["DATE_TO"];
			$TIME_LIMIT = strtotime($DATE_TO) - strtotime(date("d.m.Y H:i:s"));
			$C_DAYS = floor($TIME_LIMIT/$SECOND_IN_DAY);
			$C_HOUR = floor(($TIME_LIMIT-$C_DAYS*$SECOND_IN_DAY)/$SECOND_IN_HOUR);
			$C_HOUR2 = floor(($TIME_LIMIT)/$SECOND_IN_HOUR);
			$C_MINUTE = floor(($TIME_LIMIT-$C_DAYS*$SECOND_IN_DAY-$C_HOUR*$SECOND_IN_HOUR)/$SECOND_IN_MINUTE);
			$C_MINUTE2 = floor(($TIME_LIMIT-$C_HOUR2*$SECOND_IN_HOUR)/$SECOND_IN_MINUTE);
			$C_SECOND = floor($TIME_LIMIT-$C_DAYS*$SECOND_IN_DAY-$C_HOUR*$SECOND_IN_HOUR-$C_MINUTE*$SECOND_IN_MINUTE);
			$C_SECOND2 = floor($TIME_LIMIT-$C_HOUR2*$SECOND_IN_HOUR-$C_MINUTE2*$SECOND_IN_MINUTE);
			$arDinamica = array(
				"DATE_NOW" => strtotime(date("d.m.Y H:i:s")),
				"DATE_FROM" => strtotime($DATE_FROM),
				"DATE_TO" => strtotime($DATE_TO),
				"TIME_LIMIT" => $TIME_LIMIT,
				"DAYS" => $C_DAYS,
				"HOUR" => $C_HOUR,
				"HOUR2" => $C_HOUR,
				"MINUTE" => $C_MINUTE,
				"MINUTE2" => $C_MINUTE,
				"SECOND" => $C_SECOND,
				"SECOND2" => $C_SECOND,
			);

			
			if($arFields["DINAMICA"]=="custom") {
				$arDinamicData = unserialize( $arFields["DINAMICA_DATA"] );
				$arDinamica["DATA"] = $arDinamicData;
				$last_persent = 0;
				$last_time = 0;
				foreach($arDinamica["DATA"] as $persent => $time) {
					$time2 = IntVal($time=="*" ? 0 : $time);
					if($NOW_HOUR<$time2) {
						$last_persent = $persent;
						$last_time = $time;
						break;
					}
				}
				$arDinamica["PHP_DATA"]["NOW_HOUR"] = $NOW_HOUR;
				$arDinamica["PHP_DATA"]["persent"] = $last_persent;
				$arDinamica["PHP_DATA"]["persent_invert"] = round(100-$arDinamica["PHP_DATA"]["persent"]);
				$arDinamica["PHP_DATA"]["time"] = $arDinamica["DATA"][$last_persent];
			} else {
				$mirror_val = strtotime($DATE_TO)-strtotime($DATE_FROM);
				$arDinamica["PHP_DATA"]["NOW_HOUR"] = $NOW_HOUR;
				$arDinamica["PHP_DATA"]["persent"] = 100-ceil($TIME_LIMIT/$mirror_val*100);
				$arDinamica["PHP_DATA"]["persent_invert"] = round(100-$arDinamica["PHP_DATA"]["persent"]);
			}
		}
		
		return $arDinamica;
	}
}