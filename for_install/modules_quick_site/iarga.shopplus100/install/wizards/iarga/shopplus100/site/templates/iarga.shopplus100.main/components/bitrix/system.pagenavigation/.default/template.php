<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


if($arResult["NavPageSize"]==''){
	$arResult["NavShowAlways"] = $this->NavShowAlways;
	$arResult["NavQueryString"] = $this->NavQueryString;
	$arResult["NavPageNomer"] = $this->NavPageNomer;
	$arResult["NavPageCount"] = $this->NavPageCount;
	$arResult["NavPageSize"] = $this->NavPageSize;
}

if($arResult["NavShowAlways"] || $arResult['NavPageCount']>1):
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
if($GLOBALS['base']!=''){
	$GLOBALS['ppl_res']["sUrlPath"] = $GLOBALS['base'];
	$GLOBALS['ppl_res']["NavNum"] = $this->NavNum;
}else{
	$GLOBALS['ppl_res']["sUrlPath"] = $this->sUrlPath;
	$GLOBALS['ppl_res']["NavNum"] = $this->NavNum;
}
if(!function_exists("ppl")){
	function ppl($n){	
		if($n==1) return $GLOBALS['ppl_res']["sUrlPath"].'?'.$GLOBALS['qs'];
		else return $GLOBALS['ppl_res']["sUrlPath"].'?'.$GLOBALS['qs'].'PAGEN_'.$GLOBALS['ppl_res']["NavNum"].'='.$n;
	}
}
//print_r($arResult);
$n = $arResult['NavPageNomer'];
$c = $arResult['NavPageCount'];
$num = 3;


if($arResult["NavShowAlways"] || $arResult['NavPageCount']>1):
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
$GLOBALS['ppl_res'] = $arResult;
$GLOBALS['qs'] = $strNavQueryString;
if(!function_exists("ppl")){
	function ppl($n){	
		if($n==1) return $GLOBALS['ppl_res']["sUrlPath"].'?'.$GLOBALS['qs'];
		else return $GLOBALS['ppl_res']["sUrlPath"].'?'.$GLOBALS['qs'].'PAGEN_'.$GLOBALS['ppl_res']["NavNum"].'='.$n;
	}
}
//print_r($arResult);
$n = $arResult['NavPageNomer'];
$c = $arResult['NavPageCount'];
$num = 3;
?>

<nav class="pagenav">
	<a <?if($n!=1):?>class="bt_prev" href="<?=ppl($n-1)?>"<?else:?>class="bt_prev hidden"<?endif;?>>&lt;</a>



	<?if(($n - $num) > 1):?>
		<a href="<?=ppl(1)?>">1</a>
	<?endif;?>
	<?for($i=1;$i<$n;$i++):?>
		<?$st = '<a href="'.ppl($i).'">';?>
		<?$en = '</a>';?>
		<?if($i<$n-$num)continue; elseif($i==$n-$num && $i != 1){ print $st.'&hellip;'.$en;} else print $st.$i.$en;?>
	<?endfor;?>
	<a href="#happy" class="hidden"><?=$n?></a> 				
	<?$count=0; for($i=$n+1;$i<=$c;$i++):$count++;?>
		<?$st = '<a href="'.ppl($i).'">';?>
		<?$en = '</a>';?>
		<?if($count == $num  && $i != $c){ print $st.'&hellip;'.$en; $i=$c+1;} else print $st.$i.$en;?>
	<?endfor;?>
	<?if(($n + $num) < $c):?>
		<a href="<?=ppl($c)?>"><?=$c?></a>
	<?endif;?>
	<a <?if($n!=$c):?>class="bt_next" href="<?=ppl($n+1)?>"<?else:?>class="bt_next hidden"<?endif;?>>&gt;</a>
	
</nav><!--.pagenav-end-->
<?endif;
endif;?>