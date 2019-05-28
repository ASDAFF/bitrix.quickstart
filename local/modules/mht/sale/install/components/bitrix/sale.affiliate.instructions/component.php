<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

if(!CBXFeatures::IsFeatureEnabled('SaleAffiliate'))
	return;

$arParams["REGISTER_PAGE"] = Trim($arParams["REGISTER_PAGE"]);
if (StrLen($arParams["REGISTER_PAGE"]) <= 0)
	$arParams["REGISTER_PAGE"] = "register.php";

$arParams["SHOP_NAME"] = trim($arParams["SHOP_NAME"]);

$arParams["SHOP_URL"] = trim($arParams["SHOP_URL"]);

if (strlen($arParams["SHOP_NAME"]) <= 0 || strlen($arParams["SHOP_URL"]) <= 0)
{
	$dbSite = CSite::GetList(($b="sort"), ($o="asc"), array("LID" => SITE_ID));
	if ($arParams["arSite"] = $dbSite->GetNext())
	{
		if (strlen($arParams["SHOP_NAME"]) <= 0)
			$arParams["SHOP_NAME"] = $arParams["arSite"]["SITE_NAME"];
		if (strlen($arParams["SHOP_URL"]) <= 0)
			$arParams["SHOP_URL"] = $arParams["arSite"]["SERVER_NAME"];
	}
}

if (strlen($arParams["SHOP_NAME"]) <=0)
	$arParams["SHOP_NAME"] = COption::GetOptionString("main", "site_name", "");

if (strlen($arParams["SHOP_URL"]) <=0)
{
	if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME) > 0)
		$arParams["SHOP_URL"] = SITE_SERVER_NAME;
	else
		$arParams["SHOP_URL"] = COption::GetOptionString("main", "server_name", "");
}

$arParams["AFF_REG_PAGE"] = Trim($arParams["AFF_REG_PAGE"]);
if (StrLen($arParams["AFF_REG_PAGE"]) <= 0)
	$arParams["AFF_REG_PAGE"] = "/affiliate/register.php";


if (strlen($arParams["SET_TITLE"]) <= 0) $arParams["SET_TITLE"] = "Y";
	
if (CModule::IncludeModule("sale"))
{
	if ($arParams["SET_TITLE"] == "Y")
		$APPLICATION->SetTitle(GetMessage("SPCAT3_TECH_INSTR"));

	if ($GLOBALS["USER"]->IsAuthorized())
	{
		$dbAffiliate = CSaleAffiliate::GetList(
			array("TRANSACT_DATE" => "ASC"),
			array(
				"USER_ID" => IntVal($GLOBALS["USER"]->GetID()),
				"SITE_ID" => SITE_ID,
			),
			false,
			false,
			array("ID", "PLAN_ID", "ACTIVE", "PAID_SUM", "APPROVED_SUM", "PENDING_SUM", "LAST_CALCULATE")
		);
		
		if ($arAffiliate = $dbAffiliate->GetNext())
		{
			if ($arAffiliate["ACTIVE"] == "Y")
			{
				$arResult["affiliateParam"] = COption::GetOptionString("sale", "affiliate_param_name", "partner");
				?>
				<?
				$dbAffiliateTier = CSaleAffiliateTier::GetList(
					array(),
					array("SITE_ID" => SITE_ID),
					false,
					false,
					array("RATE1", "RATE2", "RATE3", "RATE4", "RATE5")
				);
				if (($arAffiliateTier = $dbAffiliateTier->Fetch()) && DoubleVal($arAffiliateTier["RATE1"]) > 0)
				{
					$arResult["SHOW_TIER_INFO"] = true;
				}
				else
				{
					$arResult["SHOW_TIER_INFO"] = false;
				}
				
				$arResult["arAffiliate"] = $arAffiliate;
			}
			else
			{
				$arResult = false;
			}
			
			$this->IncludeComponentTemplate();
		}
		else
		{
			LocalRedirect($arParams["REGISTER_PAGE"]."?REDIRECT_PAGE=".UrlEncode($APPLICATION->GetCurPage()));
			die();
		}
	}
	else
	{
		LocalRedirect($arParams["REGISTER_PAGE"]."?REDIRECT_PAGE=".UrlEncode($APPLICATION->GetCurPage()));
		die();
	}
}
else
{
	?>
	<b><?=ShowError(GetMessage("SPCAT3_NO_SHOP"))?></b>
	<?
}
?>