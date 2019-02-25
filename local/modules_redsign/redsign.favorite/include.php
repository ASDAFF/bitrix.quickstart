<?
global $DBType, $DB, $MESS, $APPLICATION;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"redsign.favorite",
	array(
		"CRSFavorite" => "classes/".$DBType."/favorite.php",
	)
);

function RSFavoritePropCheck($IBLOCK_ID)
{
	$PropertyID = 0;
	$CODE = 'RSFAVORITE_COUNTER';
	if(CModule::IncludeModule('iblock') && IntVal($IBLOCK_ID)>0)
	{
		$propRes = CIBlockProperty::GetList(array('ID'=>'ASC'),array('IBLOCK_ID'=>$IBLOCK_ID,'CODE'=>$CODE));
		if($arProp = $propRes->GetNext())
		{
			$PropertyID = $arProp['ID'];
		} else {
			$arFields = array(
				'NAME' => GetMessage('RS.FAVORITE.PROP_NAME'),
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

function RSFavoriteCounterIncDec($ELEMENT_ID)
{
	$res = false;
	$CODE = 'RSFAVORITE_COUNTER';
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
				$propID = RSFavoritePropCheck($IBLOCK_ID);
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
				$dbRes = CRSFavorite::GetList(array(),array('FUSER_ID'=>$FUserID,'ELEMENT_ID'=>$ELEMENT_ID));
				if($arFields = $dbRes->Fetch())
				{
					$res = 1;
					$VALUE--;
					CRSFavorite::Delete($arFields['ID']);
				} else {
					$res = 2;
					$VALUE++;
					$arFields = array(
						'FUSER_ID' => $FUserID,
						'ELEMENT_ID' => $ELEMENT_ID,
						'PRODUCT_ID' => 0,
					);
					CRSFavorite::Add($arFields);
				}
				$VALUE = (IntVal($VALUE)>0 ? $VALUE : 0);
				CIBlockElement::SetPropertyValueCode($ELEMENT_ID,$propID,$VALUE);
			}
		}
	}
	return $res;
}

function RSFavoriteAddDel($ELEMENT_ID)
{
	$res = false;
	if(IntVal($ELEMENT_ID)>0)
	{
		$res = CIBlockElement::GetByID($ELEMENT_ID);
		if($arElement = $res->GetNext())
		{
			$FUserID = CSaleBasket::GetBasketUserID();
			$dbRes = CRSFavorite::GetList(array(),array('FUSER_ID'=>$FUserID,'ELEMENT_ID'=>$ELEMENT_ID));
			if($arFields = $dbRes->Fetch())
			{
				$res = 1;
				CRSFavorite::Delete($arFields['ID']);
			} else {
				$res = 2;
				$arFields = array(
					'FUSER_ID' => $FUserID,
					'ELEMENT_ID' => $ELEMENT_ID,
					'PRODUCT_ID' => 0,
				);
				CRSFavorite::Add($arFields);
			}
		}
	}
	return $res;
}