<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<?
foreach($arResult as $arItem){
	if($arItem["DEPTH_LEVEL"] == "1"){
		?>
		<li>
			<div class="title"><?=$arItem["TEXT"]?></div>
			<?if(sizeof($arItem["MENUS"]) > 0){
				?><ul>
					<?foreach($arItem["MENUS"] as $val){
						?><li><a href="<?=$val["LINK"]?>"><?=($val["SELECTED"] ? "<b>" : "")?><?=$val["TEXT"]?><?=($val["SELECTED"] ? "</b>" : "")?></a></li><?
					}?>
				</ul><?
			}?>

		</li>	
		<?
	}
}
?>

<?endif?>