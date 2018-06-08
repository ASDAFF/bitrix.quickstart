<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"asdaff.favorite",
	array(
		"CAPIFavorite" => "classes/".$DBType."/favorite.php",
	)
);

function APIFavoritePropCheck($IBLOCK_ID)
{
	$PropertyID = 0;
	$CODE = 'APIFAVORITE_COUNTER';
	if(CModule::IncludeModule('iblock') && IntVal($IBLOCK_ID)>0)
	{
		$propRes = CIBlockProperty::GetList(array('ID'=>'ASC'),array('IBLOCK_ID'=>$IBLOCK_ID,'CODE'=>$CODE));
		if($arProp = $propRes->GetNext())
		{
			$PropertyID = $arProp['ID'];
		} else {
			$arFields = array(
				'NAME' => GetMessage('API.FAVORITE.PROP_NAME'),
				'ACTIVE' => 'Y',
				'SORT' => '500',
				'CODE' => $CODE,
				'PROPERTY_TYPE' => 'N',
				'IBLOCK_ID' => $IBLOCK_ID,
				'WITH_DESCRIPTION' => 'N',
			);
			$iblockproperty = new CIBlockProperty;
			$PropertyID = $iblockproperty->Add($arFields);
		}
	}
	return $PropertyID;
}

function APIFavoriteCounterIncDec($ELEMENT_ID)
{
	$res = false;
	$CODE = 'APIFAVORITE_COUNTER';
	if(CModule::IncludeModule('iblock') && CModule::IncludeModule('sale') && IntVal($ELEMENT_ID)>0)
	{
		$res = CIBlockElement::GetByID($ELEMENT_ID);
		if($arElement = $res->GetNext())
		{
			$IBLOCK_ID = $arElement["IBLOCK_ID"];
			$dbProps = CIBlockElement::GetProperty($IBLOCK_ID,$ELEMENT_ID,array('ID'=>'ASC'),array('CODE'=>$CODE));
			if($arProps = $dbProps->Fetch())
			{
				$propID = $arProps['ID'];
				$res = true;
				$VALUE = IntVal($arProps["VALUE"])>0 ? $arProps["VALUE"] : 0;
			} else {
				$propID = APIFavoritePropCheck($IBLOCK_ID);
				$dbProps = CIBlockElement::GetProperty($IBLOCK_ID,$ELEMENT_ID,array('ID'=>'ASC'),array('ID'=>$propID));
				if($arProps = $dbProps->Fetch())
				{
					$res = true;
					$VALUE = IntVal($arProps["VALUE"])>0 ? $arProps["VALUE"] : 0;
				}
			}
			if($res)
			{
				$FUserID = CSaleBasket::GetBasketUserID();
				$dbRes = CAPIFavorite::GetList(array(),array('FUSER_ID'=>$FUserID,'ELEMENT_ID'=>$ELEMENT_ID));
				if($arFields = $dbRes->Fetch())
				{
					$res = 1;
					$VALUE--;
					CAPIFavorite::Delete($arFields['ID']);
				} else {
					$res = 2;
					$VALUE++;
					$arFields = array(
						'FUSER_ID' => $FUserID,
						'ELEMENT_ID' => $ELEMENT_ID,
						'PRODUCT_ID' => 0,
					);
					CAPIFavorite::Add($arFields);
				}
				$VALUE = (IntVal($VALUE)>0 ? $VALUE : 0);
				CIBlockElement::SetPropertyValueCode($ELEMENT_ID,$propID,$VALUE);
			}
		}
	}
	return $res;
}

function APIFavoriteAddDel($ELEMENT_ID)
{
	$res = false;
	if(IntVal($ELEMENT_ID)>0)
	{
		$res = CIBlockElement::GetByID($ELEMENT_ID);
		if($arElement = $res->GetNext())
		{
			$FUserID = CSaleBasket::GetBasketUserID();
			$dbRes = CAPIFavorite::GetList(array(),array('FUSER_ID'=>$FUserID,'ELEMENT_ID'=>$ELEMENT_ID));
			if($arFields = $dbRes->Fetch())
			{
				$res = 1;
				CAPIFavorite::Delete($arFields['ID']);
			} else {
				$res = 2;
				$arFields = array(
					'FUSER_ID' => $FUserID,
					'ELEMENT_ID' => $ELEMENT_ID,
					'PRODUCT_ID' => 0,
				);
				CAPIFavorite::Add($arFields);
			}
		}
	}
	return $res;
}
