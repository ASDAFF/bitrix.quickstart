<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<? if (count($arResult) < 1) { return; } ?>
<ul><?
	$previousLevel = 0;
	foreach($arResult as $arItem):
		if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):
			echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
		endif;
		if ($arItem["IS_PARENT"]):
			?><li class="depth_level_<?=$arItem["DEPTH_LEVEL"];?><? if ($arItem["SELECTED"]) { echo ' selected active'; } ?>"><span><a href="<?=$arItem["LINK"];?>" class="depth_level_<?=$arItem["DEPTH_LEVEL"];?><? if ($arItem["SELECTED"]) { echo ' selected'; } ?>"><?=$arItem["TEXT"];?></a><span class="icon span_depth_level_<?=$arItem["DEPTH_LEVEL"];?>"></span></span>
				<ul><?
		else:
			?><li class="depth_level_<?=$arItem["DEPTH_LEVEL"];?><? if ($arItem["SELECTED"]) { echo ' selected'; } ?>"><a href="<?=$arItem["LINK"];?>" class="depth_level_<?=$arItem["DEPTH_LEVEL"];?><? if ($arItem["SELECTED"]) { echo ' selected'; } ?>"><?=$arItem["TEXT"];?></a></li><?
		endif;
		$previousLevel = $arItem["DEPTH_LEVEL"];
	endforeach;?>
	<? if ($previousLevel > 1) {
		echo str_repeat("</ul></li>", ($previousLevel-1) );
	} ?>
</ul>
<script type="text/javascript">
	$(document).ready(function () {
		$("#left_side span.icon.span_depth_level_1").each(function () {
			var h = parseFloat($(this).prev().height());
			h = Math.ceil(h/22)*22;
			$(this).css("height", h + 20 + "px");
		});
	});
</script>