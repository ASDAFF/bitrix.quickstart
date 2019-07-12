<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arParams["DISPLAY_AS_RATING"] == "vote_avg")
{
	if($arResult["PROPERTIES"]["vote_count"]["VALUE"])
		$votesValue = round($arResult["PROPERTIES"]["vote_sum"]["VALUE"]/$arResult["PROPERTIES"]["vote_count"]["VALUE"], 2);
	else
		$votesValue = 0;
}
else
	$votesValue = intval($arResult["PROPERTIES"]["rating"]["VALUE"]);

$votesCount = intval($arResult["PROPERTIES"]["vote_count"]["VALUE"]);

if(isset($_REQUEST["AJAX_CALL"]) && $_REQUEST["AJAX_CALL"]=="Y")
{
	$APPLICATION->RestartBuffer();

	die(json_encode( array(
		"value" => $votesValue,
		"votes" => $votesCount
		)
	));
}

CJSCore::Init(array("ajax"));
$strObName = "bx_vo_".$arParams["IBLOCK_ID"]."_".$arParams["ELEMENT_ID"];
$arJSParams = array(
	"progressId" => $strObName."_progr",
	"ratingId" => $strObName."_rating",
	"starsId" => $strObName."_stars",
	"ajaxUrl" => $componentPath."/component.php",
	"voteId" => $arResult["ID"],
);
?><table  class="bx_item_detail_rating">
	<tr>
		<td>
			<div class="bx_item_rating">
				<div class="bx_stars_container">
					<div id="<?=$arJSParams["starsId"]?>" class="bx_stars_bg"></div>
					<div id="<?=$arJSParams["progressId"]?>" class="bx_stars_progres"></div>
				</div>
			</div>
		</td>
		<td>
			<span id="<?=$arJSParams["ratingId"]?>" class="bx_stars_rating_votes">(0)</span>
		</td>
	</tr>
</table>
<script type="text/javascript">
BX.ready(function(){
	var <?=$strObName;?> = new JCIblockVoteStars(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);

	<?=$strObName?>.ajaxParams = <?=$arResult["AJAX_PARAMS"]?>;
	<?=$strObName?>.setValue("<?=$votesCount > 0 ? ($votesValue+1)*20 : 0?>");
	<?=$strObName?>.setVotes("<?=$votesCount?>");

	<?if(!$arResult["VOTED"] && $arParams["READ_ONLY"] !== "Y"):?>
		<?=$strObName?>.bindEvents();
	<?endif;?>
});
</script>