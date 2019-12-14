<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div id="rs_easycart" class="<?=$arParams['TEMPLATE_THEME']?><?if($arParams['ADD_BODY_PADDING']=='Y'):?> addbodypadding<?endif;?>" <?
	?>style='z-index:<?=$arParams['Z_INDEX']?>;' <?
	?>data-serviceurl="<?=$arParams['SERVICE_URL']?>"><?
	if($_REQUEST['rsec_ajax_post']!='Y')
	{
		$frame = $this->createFrame('rs_easycart',false)->begin('');
	}
	
	?><div class="rsec rsec_content" <?if(IntVal($arParams['MAX_WIDTH'])>1):?>style="max-width:<?=IntVal($arParams['MAX_WIDTH'])?>px;"<?endif;?>><?
		?><div class="rsec_in"><?
			?><div class="rsec_body"><?
				?><div class="rsec_tyanya"><?
					?><i class="rsec_iconka"></i><?
					?><a class="rsec_close" href="#close"><?=GetMessage('CLOSE_EASYCART')?><i class="rsec_iconka"></a><?
				?></i></div><?
				?><div class="rsec_tabs"><?
					if( $arParams['USE_VIEWED']=='Y' )
					{
						?><div id="rsec_viewed" class="rsec_tab"><?
							include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/viewed_products.php");
						?></div><?
					}
					if( $arParams['USE_COMPARE']=='Y' && IntVal($arParams['COMPARE_IBLOCK_ID'])>0 )
					{
						?><div id="rsec_compare" class="rsec_tab<?if($arParams['ON_UNIVERSAL_AJAX_HANDLER']=='Y'):?> rsec_universalhandler<?endif;?>" <?
							if($arParams['ON_UNIVERSAL_AJAX_HANDLER']=='Y' && $arParams['UNIVERSAL_AJAX_FINDER_COMPARE_ADD']!='')
							{
								?>data-ajaxfinder_add="<?=$arParams['UNIVERSAL_AJAX_FINDER_COMPARE_ADD']?>" <?
							}
							if($arParams['ON_UNIVERSAL_AJAX_HANDLER']=='Y' && $arParams['UNIVERSAL_AJAX_FINDER_COMPARE_REMOVE']!='')
							{
								?>data-ajaxfinder_remove="<?=$arParams['UNIVERSAL_AJAX_FINDER_COMPARE_REMOVE']?>" <?
							}
							?>><?
							include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/compare.php");
						?></div><?
					}
					if( $arParams['USE_FAVORITE']=='Y' )
					{
						?><div id="rsec_favorite" class="rsec_tab<?if($arParams['ON_UNIVERSAL_AJAX_HANDLER']=='Y'):?> rsec_universalhandler<?endif;?>" <?
							if($arParams['ON_UNIVERSAL_AJAX_HANDLER']=='Y' && $arParams['UNIVERSAL_AJAX_FINDER_FAVORITE']!='')
							{
								?>data-ajaxfinder="<?=$arParams['UNIVERSAL_AJAX_FINDER_FAVORITE']?>" <?
							}
							?>><?
							include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/favorite.php");
						?></div><?
					}
					if( $arParams['USE_BASKET']=='Y' )
					{
						?><div id="rsec_basket" class="rsec_tab<?if($arParams['ON_UNIVERSAL_AJAX_HANDLER']=='Y'):?> rsec_universalhandler<?endif;?>" <?
							if($arParams['ON_UNIVERSAL_AJAX_HANDLER']=='Y' && $arParams['UNIVERSAL_AJAX_FINDER_BASKET']!='')
							{
								?>data-ajaxfinder="<?=$arParams['UNIVERSAL_AJAX_FINDER_BASKET']?>" <?
							}
							?>><?
							include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket.php");
						?></div><?
					}
				?></div><?
			?></div><?
		?></div><?
	?></div><?
	
	?><div class="rsec rsec_headers"><?
		?><div class="rsec_in" <?if(IntVal($arParams['MAX_WIDTH'])>1):?>style="max-width:<?=IntVal($arParams['MAX_WIDTH'])?>px;"<?endif;?>><?
			?><div class="rsec_body"><?
				if($arParams['USE_ONLINE_CONSUL']=='Y')
				{
					?><a class="rsec_online" href="<?=($arParams['ONLINE_CONSUL_LINK']!=''?$arParams['ONLINE_CONSUL_LINK']:"#")?>"><i class="rsec_iconka"></i><span class="rsec_name"><?=GetMessage('ONLINE_CONSULTANT')?></span></a><?
				}
				if( $arParams['USE_VIEWED']=='Y' )
				{
					echo $APPLICATION->GetViewContent('rsec_thistab_viewed');
				}
				if( $arParams['USE_COMPARE']=='Y' && IntVal($arParams['COMPARE_IBLOCK_ID'])>0 )
				{
					echo $APPLICATION->GetViewContent('rsec_thistab_compare');
				}
				if( $arParams['USE_FAVORITE']=='Y' )
				{
					echo $APPLICATION->GetViewContent('rsec_thistab_favorite');
				}
				if( $arParams['USE_BASKET']=='Y' )
				{
					echo $APPLICATION->GetViewContent('rsec_basketheadlink');
				}
			?></div><?
		?></div><?
	?></div><?
	
	if($_REQUEST['rsec_ajax_post']!='Y')
	{
		$frame->end();
	}
?></div>