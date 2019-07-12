<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<div class="mlfCatalog-section-list">
	<?
	$CURRENT_DEPTH = $arParams['MAX_LEVEL'];
	$CURRENT_DEPTH = $TOP_DEPTH;
	foreach($arResult as $key=>$arItem):
		if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
			continue;
	?>
	<?
		if($CURRENT_DEPTH < $arItem["DEPTH_LEVEL"])
		{
			echo "\n",str_repeat("\t", $arItem["DEPTH_LEVEL"]-$TOP_DEPTH),"<ul>";
		}
		elseif($CURRENT_DEPTH == $arItem["DEPTH_LEVEL"])
		{
			echo "</li>";
		}
		else
		{
			while($CURRENT_DEPTH > $arItem["DEPTH_LEVEL"])
			{
				echo "</li>";
				echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</ul>","\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH-1);
				$CURRENT_DEPTH--;
			}
			echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</li>";
		}

		echo "\n",str_repeat("\t", $arSection["DEPTH_LEVEL"]-$TOP_DEPTH);
	?>
		<li <?if($arItem["SELECTED"]) echo 'class="active" ';?>id="mlf<?=$key?>"><a href="<?=$arItem["LINK"]?>"><span class="arw"></span><?=$arItem["TEXT"]?></a>
	<?$CURRENT_DEPTH = $arItem["DEPTH_LEVEL"];?>
	<?endforeach?>
	<?
	while($CURRENT_DEPTH > $TOP_DEPTH)
	{
		echo "</li>";
		echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</ul>","\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH-1);
		$CURRENT_DEPTH--;
	}
	?>
	</li></ul>
</div>
<script>
$(document).ready(function(){
$('.mlfCatalog-section-list').mlfakm();
});
</script>
<?endif?>