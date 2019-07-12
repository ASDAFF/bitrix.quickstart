<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
	return;
?>
<?
$main=0;
foreach($arResult as $itemIdex => $arItem):
	if ($arItem["DEPTH_LEVEL"]==1) $main++;
endforeach;
?>

<table width="100%" class="pr" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="pr_lv" width="1%" >
				</td>
				<td class="pr_v" width="80%">
				</td>
				<td class="pr_rv" width="1%">
				</td>
			</tr>
			<tr>
				<td class="pr_lc" width="1%">
				</td>
				<td width="80%">
					<table width="100%" style="padding-left: 10px;">
						<tr>
							<?foreach($arResult as $itemIdex => $arItem):?>
								<?if ($arItem["DEPTH_LEVEL"]==1):?>
									<td width="<?=100/$main?>%"><div class="maintext1"><?=$arItem["TEXT"]?></div></td>
								<?endif;?>
							<?endforeach?>
						</tr>

						<tr>
							<td colspan="<?=$main?>" >
								<div class="item"></div>
							</td>
						</tr>
						
						
						
<tr>
<?$i=0;?>
	<?foreach($arResult as $itemIdex => $arItem):?>
		<?if ($arItem["DEPTH_LEVEL"]==1) $link=$arItem["LINK"]; ?>
		<?if ($arItem["DEPTH_LEVEL"]==1 && $i==0):?>
			<td width="<?=100/$main?>%">
				<ul class="menusp">
		<?elseif ($arItem["DEPTH_LEVEL"]==1 && $i!=0):?>
				</ul>
			</td>
			<td width="<?=100/$main?>%" >
				<ul class="menusp">											
		<?elseif (strpos($link, "/search/")===false):?>
			<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
			
		<?elseif (strpos($link, "/search/")!==false):?>
												
			<?$APPLICATION->IncludeComponent("bitrix:search.title", "template2", array(
	"NUM_CATEGORIES" => "3",
	"TOP_COUNT" => "5",
	"ORDER" => "date",
	"USE_LANGUAGE_GUESS" => "Y",
	"CHECK_DATES" => "N",
	"SHOW_OTHERS" => "Y",
	"PAGE" => "#SITE_DIR#search/",
	"CATEGORY_OTHERS_TITLE" => "",
	"CATEGORY_0_TITLE" => GetMessage("SEARCH_NEWS"),
	"CATEGORY_0" => array(
		0 => "iblock_news",
	),
	"CATEGORY_0_iblock_news" => array(
		0 => "1",
	),
	"CATEGORY_1_TITLE" => GetMessage("SEARCH_CATALOG"),
	"CATEGORY_1" => array(
		0 => "iblock_catalog",
		1 => "iblock_offers",
	),
	"CATEGORY_1_iblock_catalog" => array(
		0 => "all",
	),
	"CATEGORY_1_iblock_offers" => array(
		0 => "all",
	),
	"CATEGORY_2_TITLE" => GetMessage("SEARCH_ARTICLES"),
	"CATEGORY_2" => array(
		0 => "main",
	),
	"CATEGORY_2_main" => array(
	),
	"SHOW_INPUT" => "Y",
	"INPUT_ID" => "title-search-input2",
	"CONTAINER_ID" => "search2"
	),
	false
);?>
												
		<?endif;?>
		<?$i++;?>
	<?endforeach?>
		<?if (strpos($link, "/search/")===false) {?></ul><?}?>
												<div class="clear"></div>
											</div>
										</td>
</tr>







				</table>
				</td>
				<td class="pr_rc" width="1%">	
				</td>

			</tr>
			<tr>
				<td class="pr_ln" width="1%" >
				</td>
				<td class="pr_n" width="80%">
				</td>
				<td class="pr_rn" width="1%" >
				</td>
			</tr>
		</table>







			
					
					


						

			
