<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="row">
    <div class="span2">
        <img width="310" height="676" alt="Каталог" src="<?= SITE_TEMPLATE_PATH ?>/images/catalog-005.jpg">
    </div>
    <div class="span8 map left-list link-mas">
    <h3><?=GetMessage("CATALOG_LABEL")?></h3>
    <?$APPLICATION->IncludeComponent("novagroup:map",
        "",
        Array(
            'SET_TITLE'=>"N",
            "ONLY_CATALOG"=>"Y"
        ),

        false
    );
    ?>
    </div>

</div>
