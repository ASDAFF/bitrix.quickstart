<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<div class="catalog-section-sorter">
	<?if($arParams["ALFA_CHOSE_TEMPLATES_SHOW"]=="Y"):?>
		<ul>
		<?=GetMessage("MSG_TEMPLATE")?>
		<?foreach($arResult["CTEMPLATE"] as $template):?>
			<li><a href="<?=$template["URL"]?>"><?if($template["USING"]=="Y"):?><strong><?endif;?><?=($template["NAME_LANG"]!="" ? $template["NAME_LANG"] : $template["VALUE"])?><?if($template["USING"]=="Y"):?></strong><?endif;?></a></li>
		<?endforeach;?>
		</ul>
	<?endif;?>
	<?if($arParams["ALFA_SHORT_SORTER"]=="Y"):?>
		<ul>
			<?=GetMessage("MSG_SORT")?>
			<?$arrUsed = array();
			$arrUsed[] = $arResult["USING"]["CSORTING"]["ARRAY"]["GROUP"];
			if(isset($arResult["USING"]["CSORTING"]) && is_array($arResult["USING"]["CSORTING"])):?>
				<li><a href="<?=$arResult["USING"]["CSORTING"]["ARRAY"]["URL2"]?>"><strong><?=($arResult["USING"]["CSORTING"]["ARRAY"]["NAME_LANG"]!="" ? $arResult["USING"]["CSORTING"]["ARRAY"]["NAME_LANG"] : $arResult["USING"]["CSORTING"]["ARRAY"]["VALUE"])?></strong></a></li>
			<?endif;?>
			<?foreach($arResult["CSORTING"] as $sort):?>
				<?if(!in_array($sort["GROUP"],$arrUsed) ):?>
					<li><a href="<?=$sort["URL"]?>"><?=($sort["NAME_LANG"]!="" ? $sort["NAME_LANG"] : $sort["VALUE"])?></a></li>
					<?$arrUsed[] = $sort["GROUP"];?>
				<?endif;?>
			<?endforeach;?>
		</ul>
	<?else:?>
		<?if($arParams["ALFA_SORT_BY_SHOW"]=="Y"):?>
			<ul>
			<?=GetMessage("MSG_SORT")?>
			<?foreach($arResult["CSORTING"] as $sort):?>
				<li><a href="<?=$sort["URL"]?>"><?if($sort["USING"]=="Y"):?><strong><?endif;?><?=($sort["NAME_LANG"]!="" ? $sort["NAME_LANG"] : $sort["VALUE"])?><?if($sort["USING"]=="Y"):?></strong><?endif;?></a></li>
			<?endforeach;?>
			</ul>
		<?endif;?>
	<?endif;?>
	<?if($arParams["ALFA_OUTPUT_OF_SHOW"]=="Y"):?>
		<ul>
		<?=GetMessage("MSG_OUTPUT")?>
		<?foreach($arResult["COUTPUT"] as $output):?>
			<li><a href="<?=$output["URL"]?>"><?if($output["USING"]=="Y"):?><strong><?endif;?><?=($output["NAME_LANG"]!="" ? $output["NAME_LANG"] : $output["VALUE"])?><?if($output["USING"]=="Y"):?></strong><?endif;?></a></li>
		<?endforeach;?>
		</ul>
	<?endif;?>
</div>