<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$incol = intval( sizeof( $arResult["ITEMS"] ) / 3) + 1;
?>

<div class="brands-list" style="margin-top: 0px;">
	<div class="brands-col">
			<?$i = 0;?>
			<?foreach($arResult["ITEMS"] as $arItem){?>
				<?
				$letter = strtoupper( substr($arItem["NAME"], 0, 1) );
				if($i == $incol){
					$i = 0;
					?></div><div class="brands-col"><?
				}else 
					$i++;
				if($firstname == ""){
					$firstname = $letter;
					?><ul><li class="title"><?=$letter;?></li><?
				}elseif( $letter != $firstname ){
					$firstname = $letter;
					?></ul><ul><li class="title"><?=$letter;?></li><?
				}
				?>
				<li><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></li>
			<?}?>
		</ul>
		
	</div>
	<div class="clear"></div>
</div>

