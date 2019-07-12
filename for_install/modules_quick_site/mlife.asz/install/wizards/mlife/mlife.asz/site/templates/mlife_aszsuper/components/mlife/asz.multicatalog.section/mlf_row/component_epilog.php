<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
global $APPLICATION;
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.timer.js');
if(count($arResult["ITEM_IDS"])>0){
	?>
	<?
	if($arParams["HIDE_QUANT"]=="Y"){
		CModule::IncludeModule("mlife.asz");
		$res = \Mlife\Asz\QuantTable::GetList(array('select'=>array("PRODID","KOL"),'filter'=>array("PRODID"=>$arResult["ITEM_IDS"])));
		$scr = '';
		while($arRes = $res->Fetch()){
			if($arRes["KOL"]>0){
				$scr .= '$(".prod'.$arRes["PRODID"].' .avalible").html("'.GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_1").'");';
			}else{
				$scr .= '$(".prod'.$arRes["PRODID"].' .avalible").html("'.GetMessage("MLIFE_ASZ_CATALOG_SECTION_E_2").'").addClass("zakaz");';
				if($arParams["HIDE_BY"]=="Y" && $arParams["ZAKAZ"]!="Y"){
					$scr .= '$(".prod'.$arRes["PRODID"].' .addToCart").remove();';
				}
			}
		}
	}
	?>
	<script>
		$(document).ready(function(){
		
			function createPopupDiv(content){
				
				$('body').append('<div id="popup" class="corzMessage"><div class="wrapFixerPopup">\
				<div class="content">'+content+'</div>\
				<div class="button b-close"><a href="#"><?=GetMessage("MLIFE_ASZ_CATALOG_CLOSER")?></a></div></div></div>');
				
			}
			
			$(document).on('click','.b-close',function(e){
				e.preventDefault();
				$("#popup").hide().remove();
			});
		
			<?=$scr;?>
			$(document).on('click','.addToCart a',function(e){
				e.preventDefault();
				var pid = $(this).attr('data-id');
				var p_name = $(".prod"+pid+" .desc .name a").html();
				var p_image = $(".prod"+pid+" .image a").html();
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
	<?
}
?>