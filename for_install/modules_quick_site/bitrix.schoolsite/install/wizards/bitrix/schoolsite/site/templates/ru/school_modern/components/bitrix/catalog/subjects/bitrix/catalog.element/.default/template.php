<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(is_array($arResult["DETAIL_PICTURE"]))
{
	?><div class="gallery"><?
	?><img border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /><?
	?></div><?
}
?>
<div class="txt"><?=(empty($arResult["DETAIL_TEXT"]))?$arResult["PREVIEW_TEXT"]:$arResult["DETAIL_TEXT"]?></div>

<?
if(isset($arResult["TEACHERS"]) && is_array($arResult["TEACHERS"]) && count($arResult["TEACHERS"]))
{
	?><div class="teachers"><?
		?><h3><?=GetMessage("CATALOG_TEACHERS")?></h3><?
		?><div class="row"><?
		foreach($arResult["TEACHERS"] as $i=>$arTeacher)
		{
			if($i%2==0 && $i!=0){?></div><div class="row"><?}
			?>
			<div class="desc">
				<div class="inCharge">
					<div class="person">
						<div class="personId">
							
							<?
							if(is_array($arTeacher["PERSONAL_PHOTO"]))
							{
								?><div class="personImg greyBorder"><img src="<?=$arTeacher["PERSONAL_PHOTO"]["SRC"]?>"><div class="c tl"></div><div class="c tr"></div><div class="c bl"></div><div class="c br"></div></div><?
							}
							?>
							<div class="personName"><?=$arTeacher["NAME"]?></div>
						</div>
						<div class="personNote">
                            <?if($arTeacher["MAIL"]){?>Email: <a href="mailto:<?=$arTeacher["MAIL"]?>"><?=$arTeacher["MAIL"]?></a>
                            <br><?}?>
							<?=$arTeacher["TEXT"]?>
						</div>
					</div>
				</div>
			</div>
			<?
		}
		?></div><?
	?></div><?

}
?>
