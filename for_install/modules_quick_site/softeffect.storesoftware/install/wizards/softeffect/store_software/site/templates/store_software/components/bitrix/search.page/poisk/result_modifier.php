<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($USER->IsAdmin()) {
	//var_dump($arResult["SEARCH"]);
	foreach ($arResult['SEARCH'] as $key => $value) {
		if (strpos($value['ITEM_ID'], 'S')!==FALSE) { // если это секция - строим другой URL
			$arUrl = explode('/', $value['URL']);
			preg_match('/S([0-9]+)/', $value['ITEM_ID'], $matches);
		
			$url = '/'.$arUrl[1].'/';
			$dbSec = CIBlockSection::GetNavChain(FALSE, $matches[1]);
			while ($arSec = $dbSec->GetNext()) {
				$url .= $arSec['CODE'].'/';
			}
			$url .= $arUrl[count($arUrl)-1];

			$arResult['SEARCH'][$key]['URL'] = $url;
			$arResult['SEARCH'][$key]['~URL'] = $url;
		}
	}
}

$arResult["TAGS_CHAIN"] = array();
if($arResult["REQUEST"]["~TAGS"])
{
	$res = array_unique(explode(",", $arResult["REQUEST"]["~TAGS"]));
	$url = array();
	foreach ($res as $key => $tags)
	{
		$tags = trim($tags);
		if(!empty($tags))
		{
			$url_without = $res;
			unset($url_without[$key]);
			$url[$tags] = $tags;
			$result = array(
				"TAG_NAME" => htmlspecialcharsex($tags),
				"TAG_PATH" => $APPLICATION->GetCurPageParam("tags=".urlencode(implode(",", $url)), array("tags")),
				"TAG_WITHOUT" => $APPLICATION->GetCurPageParam((count($url_without) > 0 ? "tags=".urlencode(implode(",", $url_without)) : ""), array("tags")),
			);
			$arResult["TAGS_CHAIN"][] = $result;
		}
	}
}
?>