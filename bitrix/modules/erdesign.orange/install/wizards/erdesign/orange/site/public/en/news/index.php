<?
require ($_SERVER ["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle ( "Новости" );
?><section id="content-header">
	<div class="container">
		<div class="row-fluid">
			<div class="span8">
				<hgroup class="content-title welcome">
					<h1><?$APPLICATION->ShowTitle()?></h1>

					<h2><?=$APPLICATION->GetProperty("description");?></h2>
				</hgroup>
			</div>

			<div class="span4">
				<hgroup class="fancy-headers">
					<h1>
						Случайное <span>фото</span>
					</h1>

					<h2>вечеринок</h2>
				</hgroup>
			</div>
		</div>
	</div>
</section>
<!-- /content-header -->
<section id="content-container" class="container">
	<div class="row-fluid"> 	 	 	<?
	
	$APPLICATION->IncludeComponent ( "bitrix:news", "news", array ("IBLOCK_TYPE" => "service", "IBLOCK_ID" => "#NEWS_IBLOCK_ID#", "NEWS_COUNT" => "4", "USE_SEARCH" => "N", "USE_RSS" => "N", "USE_RATING" => "N", "USE_CATEGORIES" => "N", "USE_REVIEW" => "N", "USE_FILTER" => "N", "SORT_BY1" => "ACTIVE_FROM", "SORT_ORDER1" => "DESC", "SORT_BY2" => "SORT", "SORT_ORDER2" => "ASC", "CHECK_DATES" => "Y", "SEF_MODE" => "Y", "SEF_FOLDER" => SITE_DIR."news/", "AJAX_MODE" => "N", "AJAX_OPTION_JUMP" => "N", "AJAX_OPTION_STYLE" => "Y", "AJAX_OPTION_HISTORY" => "N", "CACHE_TYPE" => "A", "CACHE_TIME" => "36000000", "CACHE_FILTER" => "N", "CACHE_GROUPS" => "Y", "SET_TITLE" => "Y", "SET_STATUS_404" => "N", "INCLUDE_IBLOCK_INTO_CHAIN" => "Y", "ADD_SECTIONS_CHAIN" => "Y", "USE_PERMISSIONS" => "N", "PREVIEW_TRUNCATE_LEN" => "", "LIST_ACTIVE_DATE_FORMAT" => "d.m.Y", "LIST_FIELD_CODE" => array (0 => "", 1 => "" ), "LIST_PROPERTY_CODE" => array (0 => "", 1 => "" ), "HIDE_LINK_WHEN_NO_DETAIL" => "N", "DISPLAY_NAME" => "Y", "META_KEYWORDS" => "-", "META_DESCRIPTION" => "-", "BROWSER_TITLE" => "-", "DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y", "DETAIL_FIELD_CODE" => array (0 => "NAME", 1 => "PREVIEW_PICTURE", 2 => "DETAIL_TEXT", 3 => "" ), "DETAIL_PROPERTY_CODE" => array (0 => "", 1 => "PROPERTY_PHOTO", 2 => "" ), "DETAIL_DISPLAY_TOP_PAGER" => "N", "DETAIL_DISPLAY_BOTTOM_PAGER" => "Y", "DETAIL_PAGER_TITLE" => "Страница", "DETAIL_PAGER_TEMPLATE" => "", "DETAIL_PAGER_SHOW_ALL" => "Y", "DISPLAY_TOP_PAGER" => "N", "DISPLAY_BOTTOM_PAGER" => "Y", "PAGER_TITLE" => "Новости", "PAGER_SHOW_ALWAYS" => "Y", "PAGER_TEMPLATE" => "orange", "PAGER_DESC_NUMBERING" => "N", "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000", "PAGER_SHOW_ALL" => "Y", "DISPLAY_DATE" => "Y", "DISPLAY_PICTURE" => "Y", "DISPLAY_PREVIEW_TEXT" => "Y", "USE_SHARE" => "N", "AJAX_OPTION_ADDITIONAL" => "", "SEF_URL_TEMPLATES" => array ("news" => "", "section" => "", "detail" => "#ELEMENT_ID#/" ) ), false );
	?> 	 	 	 	 	 	 	 	 		 		
<!-- /content -->
		<aside id="sidebar" class="span4"> 			 			 			 			 <?
		
		$APPLICATION->IncludeComponent ( "bitrix:news.list", "right_gallery", array ("IBLOCK_TYPE" => "service", "IBLOCK_ID" => "#GALLERY_IBLOCK_ID#", "NEWS_COUNT" => "1", "SORT_BY1" => "ID", "SORT_ORDER1" => "ASC", "SORT_BY2" => "SORT", "SORT_ORDER2" => "ASC", "FILTER_NAME" => "", "FIELD_CODE" => array (0 => "", 1 => "" ), "PROPERTY_CODE" => array (0 => "", 1 => "PROPERTY_PHOTO", 2 => "" ), "CHECK_DATES" => "Y", "DETAIL_URL" => "", "AJAX_MODE" => "N", "AJAX_OPTION_JUMP" => "N", "AJAX_OPTION_STYLE" => "Y", "AJAX_OPTION_HISTORY" => "N", "CACHE_TYPE" => "A", "CACHE_TIME" => "36000000", "CACHE_FILTER" => "N", "CACHE_GROUPS" => "Y", "PREVIEW_TRUNCATE_LEN" => "", "ACTIVE_DATE_FORMAT" => "d.m.Y", "SET_TITLE" => "N", "SET_STATUS_404" => "N", "INCLUDE_IBLOCK_INTO_CHAIN" => "Y", "ADD_SECTIONS_CHAIN" => "Y", "HIDE_LINK_WHEN_NO_DETAIL" => "N", "PARENT_SECTION" => "", "PARENT_SECTION_CODE" => "", "DISPLAY_TOP_PAGER" => "N", "DISPLAY_BOTTOM_PAGER" => "Y", "PAGER_TITLE" => "Новости", "PAGER_SHOW_ALWAYS" => "Y", "PAGER_TEMPLATE" => "", "PAGER_DESC_NUMBERING" => "N", "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000", "PAGER_SHOW_ALL" => "Y", "DISPLAY_DATE" => "Y", "DISPLAY_NAME" => "Y", "DISPLAY_PICTURE" => "Y", "DISPLAY_PREVIEW_TEXT" => "Y", "AJAX_OPTION_ADDITIONAL" => "" ), false );
		?> 			 			 			 	 				 				 
      <div class="widget live-sets">
				<header>
					<hgroup class="fancy-headers">
						<h1>
							Смотрите <span>и слушайте </span>
						</h1>

						<h2>наши события</h2>
					</hgroup>
				</header> 									<?
				
				$APPLICATION->IncludeComponent ( "bitrix:news.line", "video", Array ("IBLOCK_TYPE" => "service", "IBLOCKS" => "#VIDEO_IBLOCK_ID#", "NEWS_COUNT" => "20", "FIELD_CODE" => array (0 => "NAME", 1 => "PREVIEW_TEXT", 2 => "DETAIL_TEXT", 3 => "" ), "SORT_BY1" => "ID", "SORT_ORDER1" => "ASC", "SORT_BY2" => "SORT", "SORT_ORDER2" => "ASC", "DETAIL_URL" => "", "CACHE_TYPE" => "A", "CACHE_TIME" => "300", "CACHE_GROUPS" => "Y", "ACTIVE_DATE_FORMAT" => "d.m.Y" ) );
				?>			 				 </div>

			<!-- /live-stes -->

			<div class="widget upcoming-events">
<header> 										<hgroup class="fancy-headers">
						<h1>
							<span class="resp">события</span> 
						</h1>
						<h2>Не пропустите вечер!</h2>
					</hgroup> 				</header>> 				 	 							<?
				
				$APPLICATION->IncludeComponent ( "bitrix:news.line", "right_baner", Array ("IBLOCK_TYPE" => "service", "IBLOCKS" => "#EVENTS_IBLOCK_ID#", "NEWS_COUNT" => "1", "FIELD_CODE" => array (0 => "NAME", 1 => "PREVIEW_PICTURE", 2 => "PROPERTY_DATA" ), "SORT_BY1" => "ACTIVE_FROM", "SORT_ORDER1" => "DESC", "SORT_BY2" => "SORT", "SORT_ORDER2" => "ASC", "DETAIL_URL" => "", "ACTIVE_DATE_FORMAT" => "d.m.Y", "CACHE_TYPE" => "A", "CACHE_TIME" => "300", "CACHE_GROUPS" => "Y" ) );
				?> 				 	 				 </div>

			<!-- /upcoming-events -->
		</aside>
	</div>
</section>
<!-- content-container -->
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>