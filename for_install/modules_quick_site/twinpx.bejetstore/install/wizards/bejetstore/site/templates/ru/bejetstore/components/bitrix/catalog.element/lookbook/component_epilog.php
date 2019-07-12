<?//print_R($arResult);die;?>
<? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/components/bitrix/catalog.section/tabs/style.css');?>
<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/components/bitrix/catalog.section/tabs/script.js");?>
<?global $addFilter;
//$addFilter = array("PARAMS" => array("iblock_section" => $arResult["ID"]));?>
<?/*$this->initComponentTemplate();?>
<?$this->__template->SetViewTarget("section_tags_position");?>
<?$tags=explode(',', $arResult['TAGS']);?>
<ul class="nav nav-pills bj-h1-nav hidden-xs text-right">
	<?foreach($tags as $t):?>
	<li><a href="/search/?q=<?=trim($t)?>"><?=trim($t)?></a></li>
	<?endforeach?>
</ul>

<?$this->__template->EndViewTarget();*/?>