<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arParams["DISPLAY_NAME"]=="N" && $arResult["NAME"]){
	$APPLICATION->SetTitle($arResult["NAME"]);
} ?>
<div class="news-detail">
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
	<?endif?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<span class="news-date-time"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
	<?endif;?>
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h3><?=$arResult["NAME"]?></h3>
	<?endif;?>
	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
		<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
	<?endif;?>
	<?if($arResult["NAV_RESULT"]){?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
 	<?}elseif(strlen($arResult["DETAIL_TEXT"])>0){?>
		<?echo $arResult["DETAIL_TEXT"];?>
 	<?}else{?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?}

	if(!empty($arResult["FIELDS"]) || !empty($arResult["DISPLAY_PROPERTIES"]))	$margin=' margin-bottom:10px;';
	?>
	<div style="clear:both;<?=$margin?>"></div>
	<?
	foreach($arResult["FIELDS"] as $code=>$value){?>
			<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?><br /><?
	}		
	foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty){
		if($arProperty['USER_TYPE']=='UserID'){		
			if(is_array($arProperty["VALUE"])){		
				$arProperty["DISPLAY_VALUE"]='';
				foreach ($arProperty["VALUE"] as $item) {										
					$rsUser = CUser::GetByID($item);
					$arUser = $rsUser->Fetch();					
					$value=trim($arUser['NAME'].' '.$arUser['LAST_NAME']);					
					if(empty($value)) $value=$arUser['LOGIN'];
					$value.=' (<a href="mailto:'.$arUser['EMAIL'].'">'.$arUser['EMAIL'].'</a>)';	
					$arProperty["DISPLAY_VALUE"][]=$value;
				}
			}else{
				$rsUser = CUser::GetByID($arProperty['VALUE']);
				$arUser = $rsUser->Fetch();
				$arProperty["DISPLAY_VALUE"]=trim($arUser['NAME'].' '.$arUser['LAST_NAME']);
				$arProperty["DISPLAY_VALUE"].=' (<a href="mailto:'.$arUser['EMAIL'].'">'.$arUser['EMAIL'].'</a>)';	
				if(empty($arProperty["DISPLAY_VALUE"])) $arProperty["DISPLAY_VALUE"]=$arUser['LOGIN'];
			}			
		}	
		echo $arProperty["NAME"].':&nbsp;';
		if(is_array($arProperty["DISPLAY_VALUE"])){			
			echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
		}else{		
			echo $arProperty["DISPLAY_VALUE"];
		}
		echo '<br />';		
	}
	
	if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y"){  ?>
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
		</div><?
	}  ?>
</div>