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
if($arParams["SORTS"][0])
{
	foreach($arParams["SORTS"] as $sort)
	{
		$sotrs[$sort]=true;
	}
	
}
//print_r($arParams["SORTS"]);
//print_r($arParams);
?>

<div class="col-sm-6">
	<div class="bj-sorting">
		<div class="dropdown">
			<?if($arParams["SORTS"][0] && count($arParams["SORTS"])>1):?>
			<a href="#" id="dLabel" role="button" data-toggle="dropdown" data-target="#" class="form-control">
				<?=(strlen(GetMessage("SORT_".trim(strtoupper($_REQUEST["sort"])))) ? GetMessage("SORT_".trim(strtoupper($_REQUEST["sort"]))) : GetMessage("SORT_".trim(strtoupper($arParams["SORTS"][0]))) )?> 
				
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
				<?if($sotrs['cheap']):?><li><a href="<?=$APPLICATION->GetCurPageParam("sort=cheap",array("sort"));?>#sort"><?=GetMessage("SORT_CHEAP")?></a></li><?endif?>
				<?if($sotrs['new']):?><li><a href="<?=$APPLICATION->GetCurPageParam("sort=new",array("sort"));?>#sort"><?=GetMessage("SORT_NEW")?></a></li><?endif?>
				<?if($sotrs['popular']):?><li><a href="<?=$APPLICATION->GetCurPageParam("sort=popular",array("sort"));?>#sort"><?=GetMessage("SORT_POPULAR")?></a></li><?endif?>
			</ul>
			<?endif?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
