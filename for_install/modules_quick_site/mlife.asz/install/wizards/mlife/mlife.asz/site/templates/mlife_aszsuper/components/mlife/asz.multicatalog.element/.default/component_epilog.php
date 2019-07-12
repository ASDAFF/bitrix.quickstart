<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
global $APPLICATION;
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.timer.js');
?>
<?
global $APPLICATION;
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/fancybox/jquery.fancybox.pack.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/fancybox/helpers/jquery.fancybox-thumbs.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/fancybox/jquery.fancybox.css');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/fancybox/helpers/jquery.fancybox-thumbs.css');

if($arParams["ADD_SECTIONS_CHAIN"]=="Y"){
	foreach($arResult["SECTIONS"] as $sect){
		$APPLICATION->AddChainItem($sect["NAME"],$sect["SECTION_PAGE_URL"]);
	}
}
	
if(isset($arResult["CHAIN_EL"])){
	$APPLICATION->AddChainItem($arResult["CHAIN_EL"]["NAME"], $arResult["CHAIN_EL"]["URL"]);
}


if($arParams["HIDE_QUANT"]=="Y"){
	CModule::IncludeModule("mlife.asz");
	$res = \Mlife\Asz\QuantTable::GetList(array('select'=>array("PRODID","KOL"),'filter'=>array("PRODID"=>$arResult["ID"])));
	$scr = '';
	while($arRes = $res->Fetch()){
		if($arRes["KOL"]>0){
			$scr .= '$(".prod'.$arRes["PRODID"].' .avalible").removeClass("zakaz").html("'.GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_1").'");';
		}else{
			$scr .= '$(".prod'.$arRes["PRODID"].' .avalible").removeClass("zakaz").html("'.GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_2").'").addClass("zakaz");';
			if($arParams["HIDE_BY"]=="Y" && $arParams["ZAKAZ"]!="Y"){
				$scr .= '$(".prod'.$arRes["PRODID"].' .addToCart").remove();';
			}
		}
	}
}
?>
<script>
	$(document).ready(function(){
		<?=$scr?>
		
		function createPopupDiv(content){
			
			$('body').append('<div id="popup" class="corzMessage"><div class="wrapFixerPopup">\
			<div class="content">'+content+'</div>\
			<div class="button b-close"><a href="#"><?=GetMessage("MLIFE_ASZ_CATALOG_CLOSER")?></a></div></div></div>');
			
		}
		
		$(document).on('click','.b-close',function(e){
			e.preventDefault();
			$("#popup").hide().remove();
		});
		
		$(document).on('click','.addToCart a',function(e){
			e.preventDefault();
			var pid = $(this).attr('data-id');
			var p_name = $(".prod"+pid+" h1").html();
			var p_image = $(".prod"+pid+" .image").html();
			$.ajax({
				 url: '<?=SITE_DIR?>personal/basket/',
				 data: {ajax:'1',action:'basket_add', prodid: pid},
				 dataType : "html",
				 success: function (data, textStatus) {
					if(data != 'ok'){
						$('.mlfSmallcart').trigger('refreshBasket');
						var cnt = "";
							cnt += '<div class="tovarName">'+p_name+'</div>\
							<div class="tovarImage">'+p_image+'</div>\
							<div class="wrapMess">'+data+'</div>\
							<div class="btn"><a href="<?=SITE_DIR?>personal/basket/"><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_3")?></a></div>';
							createPopupDiv(cnt);
					}else{
						$('.mlfSmallcart').trigger('refreshBasket');
						var cnt = "";
							cnt += '<div class="tovarName">'+p_name+'</div>\
							<div class="tovarImage">'+p_image+'</div>\
							<div class="wrapMess"><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_4")?>.</div>\
							<div class="btn"><a href="<?=SITE_DIR?>personal/basket/"><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_3")?></a></div>';
							createPopupDiv(cnt);
					}
				}
			});
		});
	});
</script>