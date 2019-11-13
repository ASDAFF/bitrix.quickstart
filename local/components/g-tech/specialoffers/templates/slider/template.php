<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if(count($arResult[ITEMS])>$arParams["SLIDER_COUNT"]){
	$rsCount = (count($arResult[ITEMS])-$arParams["SLIDER_COUNT"])/$arParams["SLIDING_PER_CLICK"];
	if(floor($rsCount)!=$rsCount){
		$count=floor($rsCount)+1;
	}else{$count=$rsCount;}
}else{$count = count($arResult[ITEMS])-1;}

$sliding_step = "608";
?>

<script>
var slide_count = 0;
var sliding_right = true;

function slide(){
	if(sliding_right == true){
      if(slide_count!="<?=$count?>"){slide_count=slide_count+1;
        $("div.sliding").animate({"left": "-=<?=$sliding_step?>"}, "normal");
      }else{
        $("div.sliding").animate({"left": "0"}, "normal");
        slide_count=0;
      }
	}
	setTimeout("slide()","10000");
}

  $(document).ready(function(){

    $("#left").click(function(){
      if(slide_count!="0"){slide_count=slide_count-1;
        $("div.sliding").animate({"left": "+=<?=$sliding_step?>"}, "normal");
      }else{
        $("div.sliding").animate({"left": "-=<?=$sliding_step*$count?>"}, "normal");
        slide_count="<?=$count?>";
      }
    });

    $("#right").click(function(){
      if(slide_count!="<?=$count?>"){slide_count=slide_count+1;
        $("div.sliding").animate({"left": "-=<?=$sliding_step?>"}, "normal");
      }else{
        $("div.sliding").animate({"left": "0"}, "normal");
        slide_count=0;
      }
    });
  setTimeout("slide()","10000");
  });
</script>

<table class="specialsliderbg" onMouseOver="sliding_right=false;" onMouseOut="sliding_right=true;"><tr>
<td width="30px" align="right"><img src="<?=$templateFolder?>/images/prev.png" id="left"></td>
<td align="center">
<div id="sliding" style="height:144px; width:608px;">
<div class="sliding" style="height:144px;">
<table cellpadding="0" cellspacing="0" border="0"><tr>
  <?foreach($arResult["ITEMS"] as $key=>$arItem):?>
    <td height="144px">
      <table cellpadding="0" cellspacing="0" border="0" width="608px" height="144px">
      <tr>
      	<td align="center" valign="middle" width="296px" rowspan="3">
      	  <a href="<?=$arItem[DETAIL_PAGE_URL]?>">
      	    <?$img = CFile::ResizeImageGet($arItem[PROPERTIES][SPECIALOFFERIMG][VALUE],Array("width"=>"280","height"=>"130"));?>
            <img src="<?=$img[src]?>" title="<?=$arItem[NAME]?>" alt="<?=$arItem[NAME]?>">
          </a>
      	</td>
      	<td valign="top" height="30px" style="overflow:hidden; padding-left:10px;"><a href="<?=$arItem[DETAIL_PAGE_URL]?>" class="specialslidername"><i><?=$arItem["NAME"]?></i></a></td>
      </tr>
      <tr><td valign="top" class="specialslidertext">
      	<?if(strlen($arItem["PROPERTIES"]["SPECIALOFFERTEXT"]["VALUE"])>'200'){
      		print(substr($arItem["PROPERTIES"]["SPECIALOFFERTEXT"]["VALUE"],0,200)."...");
      	}else{print($arItem["PROPERTIES"]["SPECIALOFFERTEXT"]["VALUE"]);}?>
      </td></tr>
      <tr><td align="right">
      	<table cellpadding="0" cellspacing="0" border="0">
      		<tr>
      			<td width="46px" height="45px" valign="top" align="right"><img src="<?=$templateFolder?>/images/button_left.png"></td>
      			<td class="specbuttonbg"><?=GetMessage("SLIDER_PRICE")?> <b><?=$arItem[PRICES][BASE][DISCOUNT_VALUE]?></b> <span style="font-family:rouble;">c</span></td>
      			<td width="32px" valign="top" align="left">
      				<a href="<?echo $arItem["ADD_URL"]?>" class="tobasket" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arItem['ID']?>', 'list', '','-1');" id="catalog_add2cart_link_<?=$arItem['ID']?>"><img border="0" src="<?=$templateFolder?>/images/button_buy.png"></a>
      			</td>
      		</tr>
      	</table>
      </td></tr>
      </table>
    </td>
  <?endforeach;?>
</tr></table>
</div>
</div>
</td>
<td width="30px" align="left"><img src="<?=$templateFolder?>/images/next.png" id="right"></td>
</tr></table>