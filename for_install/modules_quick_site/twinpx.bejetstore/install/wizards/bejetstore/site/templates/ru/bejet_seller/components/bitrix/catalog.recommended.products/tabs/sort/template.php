<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @var string $strElementEdit */
/** @var string $strElementDelete */
/** @var array $arElementDeleteParams */
/** @var array $arSkuTemplate */
/** @var array $templateData */
global $APPLICATION;
?>
<div class="col-sm-6">
	<div class="bj-sorting">
		<div class="dropdown">
			<span id="dLabel" role="button" data-toggle="dropdown" data-target="#" class="form-control">
				<?=(strlen(GetMessage("SORT_".trim(strtoupper($_REQUEST["sort"])))) ? GetMessage("SORT_".trim(strtoupper($_REQUEST["sort"]))) : GetMessage("SORT_CHEAP"))?> <span class="caret"></span>
			</span>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
				<li><a href="<?=$APPLICATION->GetCurPageParam("sort=cheap",array("sort"));?>#sort"><?=GetMessage("SORT_CHEAP")?></a></li>
				<li><a href="<?=$APPLICATION->GetCurPageParam("sort=new",array("sort"));?>#sort"><?=GetMessage("SORT_NEW")?></a></li>
				<li><a href="<?=$APPLICATION->GetCurPageParam("sort=popular",array("sort"));?>#sort"><?=GetMessage("SORT_POPULAR")?></a></li>
			</ul>
		</div>
		<div class="clearfix"></div>
	</div>
</div>