<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"]) > 0): ?>
	<?
	$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	$arNotify = unserialize($notifyOption);
	?>

<div class="listitem-carousel vendors">
	<ul class="lsnn" id="foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>">

<?foreach($arResult["ITEMS"] as $key => $arItem):
	if(is_array($arItem))
	{
		$bPicture = is_array($arItem["PREVIEW_IMG"]);
        ?><li class="itembg R2D2" itemscope itemtype = "http://schema.org/Product">
			<?if ($bPicture):?>
				<a class="link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="item_img" itemprop="image" src="<?=$arItem["PREVIEW_IMG"]["SRC"]?>" width="<?=$arItem["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" /></a>
			<?else:?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="no-photo-div-big" style="height:130px; width:130px;"></div></a>
			<?endif?>
		</li>
<?
	}
endforeach;
?>
    </ul>
    <div class="clearfix"></div>
    <a id="prev<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="prev" href="#">&lt;</a>
    <a id="next<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="next" href="#">&gt;</a>
    <div id="pager<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="pager"></div>
</div>
<?elseif($USER->IsAdmin()):?>
<h3 class="hitsale"><span></span><?=GetMessage("CR_TITLE_".$arParams["FLAG_PROPERTY_CODE"])?></h3>
<div class="listitem-carousel">
	<?=GetMessage("CR_TITLE_NULL")?>
</div>
<?endif;?>

<script type="text/javascript">
    $('#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>').carouFredSel({prev:'#prev<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>',next:'#next<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>',pagination:"#pager<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>",auto:false,height:'auto',circular:true,infinite:false,cookie:true});
    function setEqualHeight(columns){
        var tallestcolumn = 0;
        columns.each(function(){
            currentHeight = $(this).height();
            if(currentHeight > tallestcolumn){
                tallestcolumn = currentHeight;
            }
        });
        columns.height(tallestcolumn);
    }
    $(document).ready(function() {
        /*setEqualHeight($(".listitem li > h4"));
        setEqualHeight($("#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?> > li > h4"));
        setEqualHeight($(".listitem li > .buy"));
        setEqualHeight($("#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?> > li > .buy"));*/
        setEqualHeight($("#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?> .R2D2"));
        setEqualHeight($(".listitem .R2D2"));

        var countli = $(".caroufredsel_wrapper ul li").size()
        if(countli < 4){
            $(".listitem-carousel").find(".next").addClass("disabled")
        }
    });
</script>
