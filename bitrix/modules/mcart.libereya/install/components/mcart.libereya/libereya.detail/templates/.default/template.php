<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="library-item">
	<div class="library-info">
	<?//echo '<pre>'; print_r($arResult); echo '</pre>'; ?>	
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
			<div class="library-photo">
				<img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
			</div>
		<?endif?>
		<div class="library-content">
			<h2><?=$arResult["NAME"]?></h2>
			<?if(!empty($arResult['PROPERTIES']['IS_NEW']['VALUE'])):?>
				<h6><?=GetMessage("MCART_LIBEREYA_NOVINKA")?></h6>
			<?endif;?>


			<div class="rating-conteiner">
				<div class="rating">
				<?$APPLICATION->IncludeComponent(
					"mcart.libereya:libereya.vote",
					"ajax",
					Array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ELEMENT_ID" => $arResult["ID"],
						"MAX_VOTE" => $arParams["MAX_VOTE"],
						"VOTE_NAMES" => $arParams["VOTE_NAMES"],
						"CACHE_TYPE" => $parent->arParams["CACHE_TYPE"],
						"CACHE_TIME" => $parent->arParams["CACHE_TIME"],
						"DISPLAY_AS_RATING" => $parent->arParams["DISPLAY_AS_RATING"],
					),
					$component->GetParent()
				);?>
				</div>	
				<?if(!empty($arResult['PROPERTIES']['FORUM_MESSAGE_CNT']['VALUE'])):?>
				<a href="#reviews_block"><?=GetMessage("MCART_LIBEREYA_OTZYVOV")?>: <?=$arResult['PROPERTIES']['FORUM_MESSAGE_CNT']['VALUE']?> </a>
				<?endif;?>

				<div class="cb"></div>
			</div>

			
			<div class="book-info book-info-small">
				<?if($arResult["PROPERTIES"]["BOOK_TYPE"]["VALUE"] == GetMessage("MCART_LIBEREYA_ELEKTRONNAA")):?>
					<?=GetMessage("MCART_LIBEREYA_ELEKTRONNAA")?>
				<?else:?>
					<?=GetMessage("MCART_LIBEREYA_BUMAJNAA")?>:
				<?endif;?>	
				 
					<?if(	empty($arResult["PROPERTIES"]["BOOKING"]["VALUE"]) && 
							empty($arResult["PROPERTIES"]["READER"]["VALUE"]) ||
							!empty($arResult["PROPERTIES"]['BOOK_FILE']['VALUE'])
							):?>
						
						<?if(!empty($arResult["PROPERTIES"]['BOOK_FILE']['VALUE'])):?>
					<br/><?=GetMessage("MCART_LIBEREYA_FILE_KNIGI")?>:
						<?foreach($arResult["PROPERTIES"]['BOOK_FILE']['VALUE'] as $file):?>
							<?$book_file = CFile::GetFileArray($file);?>
						
							<br/>&nbsp;&nbsp;<a href="<?=$book_file['SRC']?>" target="_blank"><?=$book_file['ORIGINAL_NAME']?></a>
						<?endforeach;?>
						<?else:?>
							<span class="grey"><?=GetMessage("MCART_LIBEREYA_NET_V_NALICII")?></span>	
						<?endif?>
					<?else:?>		
						<span class="grey"><?=GetMessage("MCART_LIBEREYA_NET_V_NALICII")?></span>
					<?endif;?><br>
			<?if(!empty($arResult['READER'])):?>	
				<?=GetMessage("MCART_LIBEREYA_CITATELQ")?>: <?=implode(' ', $arResult['READER'])?>
			<?endif;?>	
			</div>

			<div class="btn-group">
				<span id="booking_result_<?=$arResult['ID']?>">
					<?if(!empty($arResult["PROPERTIES"]['BOOK_FILE']['VALUE']) || $arResult["PROPERTIES"]["BOOK_TYPE"]["VALUE"] == GetMessage("MCART_LIBEREYA_ELEKTRONNAA")):?>
						<span class="btn btn-greys"><?=GetMessage("MCART_LIBEREYA_ELEKTRONNAA")?></span>
					<?elseif(	empty($arResult["PROPERTIES"]["BOOKING"]["VALUE"]) && 
							empty($arResult["PROPERTIES"]["READER"]["VALUE"])	
							):?>
						<a class="btn btn-green" onclick="app.loadAsync('/bitrix/components/mcart.libereya/libereya/async.php', 'async=y&element_id=<?=$arResult['ID']?>&action=booking', 'booking_result_<?=$arResult['ID']?>');" href="javascript:;"><?=GetMessage("MCART_LIBEREYA_ZABRONIROVATQ")?></a>
					<?else:?>
						<span class="btn btn-greys"><?=GetMessage("MCART_LIBEREYA_NET_V_NALICII")?></span>
						<span id="return_message_<?=$arResult['ID']?>">
						<a class="btn btn-blues" onclick="app.loadAsync('/bitrix/components/mcart.libereya/libereya/async.php', 'async=y&element_id=<?=$arResult['ID']?>&action=return_message', 'booking_result_<?=$arResult['ID']?>');" href="javascript:;"><?=GetMessage("MCART_LIBEREYA_SOOBSITQ_O_POSTUPLEN")?></a>
						</span>
					<?endif;?>
				</span>
				
			</div>

			<div class="book-info">
				<?=$arResult["NAME"]?><br>
				<?if(!empty($arResult['LINKED']['AUTHORS'])):?><?=GetMessage("MCART_LIBEREYA_AVTOR")?>: <?=implode(', ',$arResult['LINKED']['AUTHORS'])?><br><?endif;?>
			<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
				<?=$arProperty["NAME"]?>:&nbsp;
				<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
					<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
				<?else:?>
					<?=$arProperty["DISPLAY_VALUE"];?>
				<?endif?>
				<br />
			<?endforeach;?>
			</div>

			
		</div>
	</div>
				<div id="tab-conteiner" class="tab-conteiner">
					<div id="tab-group" class="tab-group">
						<ul>
							<li><a class="tab tab-current" onclick="return app.opentab(this)" href="#anons"><?=GetMessage("MCART_LIBEREYA_ANONS")?></a></li>
							<li><a class="tab" onclick="return app.opentab(this)" href="#paragraph"><?=GetMessage("MCART_LIBEREYA_OGLAVLENIE")?></a></li>
						</ul>
						<div class="cb"></div>
					</div>		
					<div id="tab-paragraph" class="tab-content">
						<?echo $arResult["DETAIL_TEXT"];?>
					</div>
					<div id="tab-anons" class="tab-content tab-active">
						<?echo $arResult["PREVIEW_TEXT"];?>
					</div>
					
				</div>					

	<div class="cb"></div>
</div>