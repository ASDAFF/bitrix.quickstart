<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("asdaff.favorite"))
{
	ShowError(GetMessage("API_FAVORITE_NOT_INSTALLED"));
	return;
}
if(!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("API_SALE_NOT_INSTALLED"));
	return;
}

$arParams['ACTION_VARIABLE'] = $arParams['ACTION_VARIABLE']!='' ? $arParams['ACTION_VARIABLE'] : 'action';
$arParams['PRODUCT_ID_VARIABLE'] = $arParams['PRODUCT_ID_VARIABLE']!='' ? $arParams['PRODUCT_ID_VARIABLE'] : 'id';
$ELEMENT_ID = IntVal( $_REQUEST[$arParams['PRODUCT_ID_VARIABLE']] );

if( $_REQUEST[$arParams['ACTION_VARIABLE']]=='RefreshFavorite' )
{
	$FUserID = CSaleBasket::GetBasketUserID();
	$dbRes = CAPIFavorite::GetList(array(),array('FUSER_ID'=>$FUserID));
	while($arFields = $dbRes->Fetch())
	{
		$deleteTmp = ($_REQUEST["DELETE_".$arFields['ELEMENT_ID']]=='Y')?'Y':'N';
		if($deleteTmp=='Y')
		{
			CAPIFavorite::Delete($arFields['ID']);
		}
	}
} elseif( $_REQUEST[$arParams['ACTION_VARIABLE']]=='add2favorite' && $ELEMENT_ID>0 )
{
	$res = APIFavoriteAddDel($ELEMENT_ID);
}

if( $this->StartResultCache($arParams['CACHE_TIME']) )
{
	$arFavorite = array();
	$arOrder = array();
	$arFilter = array(
		"FUSER_ID" => CSaleBasket::GetBasketUserID(),
	);
	$res = CAPIFavorite::GetList($arOrder, $arFilter);
	while($data = $res->Fetch())
	{
		$arFavorite[] = $data;
	}
	$arResult["COUNT"] = count($arFavorite);
	$arResult["ITEMS"] = $arFavorite;

	$this->IncludeComponentTemplate();
}
