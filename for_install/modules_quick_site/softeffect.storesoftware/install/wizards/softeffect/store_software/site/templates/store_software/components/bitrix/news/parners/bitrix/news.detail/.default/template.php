<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->AddChainItem($arResult["NAME"])?>

<?			
$dbSecTmp = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$IB_CATALOG, 'ID'=>$arResult["DISPLAY_PROPERTIES"]["SECTION"]['VALUE']), FALSE, array('UF_*'));
$arSec = $dbSecTmp->GetNext();
$arResult['MORE']['TEXT'] = htmlspecialchars_decode($arSec['DESCRIPTION']);
$arResult['MANUF']['URL'] = '/catalog/'.$arSec['CODE'].'/';
$arResult['MANUF']['NAME'] = $arSec['NAME'];
?>

<div class="news-detail">
	
	<table>
		<tr>
			<td>
				<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
				<img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
				<?endif?>
				<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
					<span class="news-date-time"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
				<?endif;?>
				<?//if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
					<?//=$arResult["NAME"]?>
				<?//endif;?>
			</td>
			<td>
				<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
				<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
				<?endif;?>
				<?if($arResult["NAV_RESULT"]):?>
					<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
					<?echo $arResult["NAV_TEXT"];?>
					<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
			 	<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
					<?echo $arResult["DETAIL_TEXT"];?>
			 	<?else:?>
					<?echo $arResult["PREVIEW_TEXT"];?>
				<?endif?><br/><br/>
				<a class="promo-link" title="Каталог  <?=$arResult['MANUF']['NAME']?>" href=" <?=$arResult['MANUF']['URL']?>">Каталог <?=$arResult['MANUF']['NAME'] ?>>></a>
			</td>
		</tr>
	</table>
		
	<div style="clear:both"></div>
	<br />
	
	<ul class="tabs" style="margin-left: 5px!important;">
		
		<li><a href="#Статус">Статус партнерства</a></li>
		<li><a href="#Бренд">О производителе</a></li>
		
		
	</ul>
	<div class="panes" style="border-right: 0; border-left: 0; border-bottom: 0;">

		<div>
			<?=$arResult["DISPLAY_PROPERTIES"]["STATUS"]['DISPLAY_VALUE'];?>
			<br/><br/>
			<?=CFile::ShowImage( $arResult["DISPLAY_PROPERTIES"]["SERT"]['VALUE'][0], 615,4000, "border=0", "", true);?>
			
		
			
		<?
			if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
			{
				?>
				<div class="news-detail-share">
					<noindex>
					<?
					$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
							"HANDLERS" => $arParams["SHARE_HANDLERS"],
							"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
							"PAGE_TITLE" => $arResult["~NAME"],
							"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
							"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
							"HIDE" => $arParams["SHARE_HIDE"],
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);
					?>
					</noindex>
				</div>
				<?
			}
			?>
		</div>
		
		<div>
				
			<?
			echo $arResult['MORE']['TEXT'];
			echo $arResult['MANUF']['URL'];
			?>
		</div>
		
	</div>
	
</div>
