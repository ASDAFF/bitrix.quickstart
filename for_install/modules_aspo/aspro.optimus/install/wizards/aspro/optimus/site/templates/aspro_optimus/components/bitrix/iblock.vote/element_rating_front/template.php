<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$frame = $this->createFrame()->begin();?>
<?
if($arParams["DISPLAY_AS_RATING"] == "vote_avg")
{
	if($arResult["PROPERTIES"]["vote_count"]["VALUE"])
		$DISPLAY_VALUE = round($arResult["PROPERTIES"]["vote_sum"]["VALUE"]/$arResult["PROPERTIES"]["vote_count"]["VALUE"], 2);
	else
		$DISPLAY_VALUE = 0;
}
else
	$DISPLAY_VALUE = $arResult["PROPERTIES"]["rating"]["VALUE"];
?>

<??>
<div class="iblock-vote small">


<table class="table-no-border">
	<tr>
	<?if($arResult["VOTED"] || $arParams["READ_ONLY"]==="Y"):?>
		<?if($DISPLAY_VALUE):?>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<?if(round($DISPLAY_VALUE) > $i):?>
					<td><div <? /* id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" */ ?> class="star-voted" title="<?echo $name?>"></div></td>
				<?else:?>
					<td><div <? /* id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" */ ?> class="star-empty" title="<?echo $name?>"></div></td>
				<?endif?>
			<?endforeach?>
		<?else:?>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<td><div <? /* id="vote_<?echo $arResult["ID"]?>_<?echo $i?>"  */ ?> class="star" title="<?echo $name?>"></div></td>
			<?endforeach?>
		<?endif?>
	<?else:
		$onclick = "voteScript.do_vote(this, 'vote_".$arResult["ID"]."', ".$arResult["AJAX_PARAMS"].")";
		?>
		<?if($DISPLAY_VALUE):?>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<?if(round($DISPLAY_VALUE) > $i):?>
					<td><div  <? /* id="vote_<?echo $arResult["ID"]?>_<?echo $i?>"  */ ?> class="star-active star-voted" title="<?echo $name?>"></div></td>
				<?else:?>
					<td><div  <? /* id="vote_<?echo $arResult["ID"]?>_<?echo $i?>"  */ ?> class="star-active star-empty" title="<?echo $name?>"></div></td>
				<?endif?>
			<?endforeach?>
		<?else:?>
			<?foreach($arResult["VOTE_NAMES"] as $i=>$name):?>
				<td><div <? /* id="vote_<?echo $arResult["ID"]?>_<?echo $i?>"  */ ?> class="star-active star-empty" title="<?echo $name?>"></div></td>
			<?endforeach?>
		<?endif?>
	<?endif?>
	</tr>
</table>
</div>