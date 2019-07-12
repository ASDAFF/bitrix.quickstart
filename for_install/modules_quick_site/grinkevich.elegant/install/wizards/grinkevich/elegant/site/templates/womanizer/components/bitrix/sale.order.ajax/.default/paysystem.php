<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="label_style_bl">
	<div class="clearfix"></div>
    <h2><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></h2>
    <div class="clearfix"></div>
    <ul class="ulgray">
	<?
	foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
	{
		if(count($arResult["PAY_SYSTEM"]) == 1)
		{
			?>
			<li><label>
				<div class="gray_site_block">
				<div class="gray_site_block_top"><div></div></div>
				<div class="gray_site_block_text"><div class="gray_site_block_text_02">
				<div class="radio"><input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>"></div>
				<div class="text"><b><?=$arPaySystem["NAME"];?></b><?
				if (strlen($arPaySystem["DESCRIPTION"])>0)
				{
					?>
					<br /><?=$arPaySystem["DESCRIPTION"]?>

					<?
				}
				?></div>
				<div class="clearfix"></div>
				</div></div>
				<div class="gray_site_block_bottom"><div></div></div>
				</div></label>
			</li>
			<?
		}
		else
		{
			if (!isset($_POST['PAY_CURRENT_ACCOUNT']) OR $_POST['PAY_CURRENT_ACCOUNT'] == "N") {
			?>
			<li><label>
				<div class="gray_site_block">
				<div class="gray_site_block_top"><div></div></div>
				<div class="gray_site_block_text"><div class="gray_site_block_text_02">
				<div class="radio"><input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?>></div>
				<div class="text"><b><?=$arPaySystem["NAME"];?></b><?
				if (strlen($arPaySystem["DESCRIPTION"])>0)
				{
					?>
					<br /><?=$arPaySystem["DESCRIPTION"]?>

					<?
				}
				?></div>
				<div class="clearfix"></div>
				</div></div>
				<div class="gray_site_block_bottom"><div></div></div>
				</div></label>
			</li>
			<?
			}
		}
	}
	?>
    </ul>
</div>