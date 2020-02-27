<?
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

if( !defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) {
	die();
}
Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
if( empty( $arResult["USERS"] ) ) {
	ShowError(GetMessage("NO_ITEMS_FOUNDED"));

	return;
}
$docRoot = Application::getInstance()->getDocumentRoot();

?><section class="raiting" id="rating">
	<div class="container container_raiting">
		<h2 class="raiting__title"><?=Loc::getMessage("RATING_HEADER");?></h2>
		<div class="raiting__wrap">
			<div class="raiting__line raiting__line_head">
				<div class="raiting__col1"><span class="raiting__tablehead"><?=Loc::getMessage("POSITION");?></span>
				</div>
				<div class="raiting__col2"><span class="raiting__tablehead"><?=Loc::getMessage("PARTICIPANT");?></span>
				</div>
				<div class="raiting__col3"><span class="raiting__tablehead"><?=Loc::getMessage("PROGRAMS");?></span>
				</div>
			</div><?

			$i = 1;
			foreach($arResult['USERS'] as $arUser){
				$src = $arUser['PERSONAL_PHOTO']['src'];
				if( !$src || !file_exists($docRoot . $src) ) {
					$src = SITE_TEMPLATE_PATH . '/images/avatar.png';
				}
				?><div class="raiting__line">
					<div class="raiting__col1">
						<div class="raiting__place"><?=$i * $arResult['PAGE_NUMBER']?></div>
					</div>
					<div class="raiting__col2">
						<span class="raiting__userpic" style="background-image: url(<?=$src?>)"></span>
<!--						<img src="--><?//=$src?><!--" alt="" class="raiting__userpic">-->
						<div class="raiting__wrapin-col2">
							<p class="raiting__famili"><?=$arUser['NAME']?> <?=$arUser['LAST_NAME']?></p><?
							if( !empty($arUser['PERSONAL_COUNTRY']) && !empty($arUser['PERSONAL_CITY']) ){
								?><p class="raiting__town"><?=$arUser['PERSONAL_CITY']?>, <?=$arUser['PERSONAL_COUNTRY']?></p><?
							}
						?></div>
					</div>
					<div class="raiting__col3">
						<p class="raiting__balls"><?=number_format($arUser['UF_PROGRAM'], 0, '', ' ')?></p>
					</div>
				</div><?
				$i++;
			}

			if( $arParams['SHOW_ALL'] != 'Y' ){
				?><div class="raiting__more-peoples">
					<a href="/rating/" data-change-url="false" data-set-hash="false" class="btn btn-bordo btn-bordo_more-peoples"><?=Loc::getMessage("BTN_TEXT");?></a>
				</div><?
			}
		?></div>
	</div>
</section>