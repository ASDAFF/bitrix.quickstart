<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (is_object($arParams["NAV_RESULT"]) &&  is_subclass_of($arParams["NAV_RESULT"], "CAllDBResult"))
{
	$dbresult =& $arParams["NAV_RESULT"];

	if(intval($dbresult->NavPageSize) <= 0)
		$dbresult->NavPageSize = 10;

	$arResult = Array();

	$arResult["NavShowAlways"] = $arParams["SHOW_ALWAYS"];
	$arResult["NavTitle"] = $arParams["NAV_TITLE"];
	$arResult["NavRecordCount"] = $dbresult->NavRecordCount;
	$arResult["NavPageCount"] = $dbresult->NavPageCount;
	$arResult["NavPageNomer"] = $dbresult->NavPageNomer;
	$arResult["NavPageSize"] = $dbresult->NavPageSize;
	$arResult["bShowAll"] = $dbresult->bShowAll;
	$arResult["NavShowAll"] = $dbresult->NavShowAll;
	$arResult["NavNum"] = $dbresult->NavNum;
	$arResult["bDescPageNumbering"] = $dbresult->bDescPageNumbering;
	$arResult["add_anchor"] = $dbresult->add_anchor;
	$arResult["nPageWindow"] = $nPageWindow = $dbresult->nPageWindow;
	$arResult["bSavePage"] = (CPageOption::GetOptionString("main", "nav_page_in_session", "Y")=="Y");
	$arResult["sUrlPath"] = GetPagePath(false, false);
	$arResult["NavQueryString"]= htmlspecialcharsbx(DeleteParam(array(
		"PAGEN_".$dbresult->NavNum, 
		"SIZEN_".$dbresult->NavNum, 
		"SHOWALL_".$dbresult->NavNum, 
		"PHPSESSID", 
		"clear_cache",
	)));

	if ($dbresult->bDescPageNumbering === true)
	{
		if ($dbresult->NavPageNomer + floor($nPageWindow/2) >= $dbresult->NavPageCount)
			$nStartPage = $dbresult->NavPageCount;
		else
		{
			if ($dbresult->NavPageNomer + floor($nPageWindow/2) >= $nPageWindow)
				$nStartPage = $dbresult->NavPageNomer + floor($nPageWindow/2);
			else
			{
				if($dbresult->NavPageCount >= $nPageWindow)
					$nStartPage = $nPageWindow;
				else
					$nStartPage = $dbresult->NavPageCount;
			}
		}

		if ($nStartPage - $nPageWindow >= 0)
			$nEndPage = $nStartPage - $nPageWindow + 1;
		else
			$nEndPage = 1;
	}
	else
	{
		if ($dbresult->NavPageNomer > floor($nPageWindow/2) + 1 && $dbresult->NavPageCount > $nPageWindow)
			$nStartPage = $dbresult->NavPageNomer - floor($nPageWindow/2);
		else
			$nStartPage = 1;

		if ($dbresult->NavPageNomer <= $dbresult->NavPageCount - floor($nPageWindow/2) && $nStartPage + $nPageWindow-1 <= $dbresult->NavPageCount)
			$nEndPage = $nStartPage + $nPageWindow - 1;
		else
		{
			$nEndPage = $dbresult->NavPageCount;
			if($nEndPage - $nPageWindow + 1 >= 1)
				$nStartPage = $nEndPage - $nPageWindow + 1;
		}
	}

	$arResult["nStartPage"] = $dbresult->nStartPage = $nStartPage;
	$arResult["nEndPage"] = $dbresult->nEndPage = $nEndPage;

	if ($dbresult->bDescPageNumbering === true)
	{
		$makeweight = ($dbresult->NavRecordCount % $dbresult->NavPageSize);
		$NavFirstRecordShow = 0;
		if($dbresult->NavPageNomer != $dbresult->NavPageCount)
			$NavFirstRecordShow += $makeweight;

		$NavFirstRecordShow += ($dbresult->NavPageCount - $dbresult->NavPageNomer) * $dbresult->NavPageSize + 1;

		if ($dbresult->NavPageCount == 1)
			$NavLastRecordShow = $dbresult->NavRecordCount;
		else
			$NavLastRecordShow = $makeweight + ($dbresult->NavPageCount - $dbresult->NavPageNomer + 1) * $dbresult->NavPageSize;

	}
	else
	{
		$NavFirstRecordShow = ($dbresult->NavPageNomer-1)*$dbresult->NavPageSize+1;

		if ($dbresult->NavPageNomer != $dbresult->NavPageCount)
			$NavLastRecordShow = $dbresult->NavPageNomer * $dbresult->NavPageSize;
		else
			$NavLastRecordShow = $dbresult->NavRecordCount;
	}

	$arResult["NavFirstRecordShow"] = $NavFirstRecordShow;
	$arResult["NavLastRecordShow"] = $NavLastRecordShow;

	$this->IncludeComponentTemplate();

	return $this;
}
?>