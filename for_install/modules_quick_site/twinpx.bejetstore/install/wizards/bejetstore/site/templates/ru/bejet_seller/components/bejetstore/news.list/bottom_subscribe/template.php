<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if (count($arResult["ITEMS"]) < 1)
	return;

?>
<div class="bj-news-subscribe hidden-xs">
	<?/*?><h2><a href="<?=SITE_DIR?>news/"><?=GetMessage("NEWS")?></a></h2><?*/?>
	<div class="row">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<div class="col-sm-4 bj-news-block">
			<div class="bj-date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
			<h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h2>
			<p><?=$arItem["PREVIEW_TEXT"]?></p>
   		</div>
   	<?endforeach;?>
   	<?if(!$arResult["SUBSCRIBED"]):?>
		<div class="col-sm-4">
			<div class="bj-news-subscribe__s">

				<div class="alert alert-warning center-block text-center hide" role="alert">
					<div class="i-relative"><div class="bj-envelope-icon bj-alert-top-icon"></div></div>
					<h3><?=GetMessage("SMTH_WRONG")?></h3>
				</div>

				<div class="alert alert-success center-block text-center hide" role="alert">
					<div class="i-relative"><div class="bj-envelope-icon bj-alert-top-icon"></div></div>
					<h3><?=GetMessage("NOW_U_SUBSCRIBED")?></h3>
					<a href class="bj-news-unsubscribe-link"><?=GetMessage("UNSUBSCRIBE")?></a>
				</div>

				<form action="<?=$this->GetFolder()?>/subscribe.php" method="post" class="hide bj-news-unsubscribe-form">
					<div class="form-group">
						<?=bitrix_sessid_post()?>
						<input type="hidden" name="unsubscribe" value="Y">
					</div>
				</form>

				<form action="<?=$this->GetFolder()?>/subscribe.php" method="post" class="bj-news-subscribe-form">
					<h3><b><?=GetMessage("SUBSCRIBE_ON_NEWS")?></b></h3>
					<div class="form-group">
						<?=bitrix_sessid_post()?>
						<input type="hidden" name="email" value="">
						<input type="email" name="ml" value="" class="form-control" required>
					</div>
					<button type="submit" class="btn btn-default btn-100"><?=GetMessage("SUBSCRIBE")?></button>
				</form>
			</div>
	    </div>
	<?else:?>
	<div class="col-sm-4">
		<div class="bj-news-subscribe__s">
			<div class="alert alert-warning center-block text-center hide" role="alert">
				<div class="i-relative"><div class="bj-envelope-icon bj-alert-top-icon"></div></div>
				<h3><?=GetMessage("SMTH_WRONG")?></h3>
			</div>

			<div class="alert alert-success center-block text-center" role="alert">
				<div class="i-relative"><div class="bj-envelope-icon bj-alert-top-icon"></div></div>
				<h3><?=GetMessage("NOW_U_SUBSCRIBED")?></h3>
				<a href class="bj-news-unsubscribe-link"><?=GetMessage("UNSUBSCRIBE")?></a>
			</div>

			<form action="<?=$this->GetFolder()?>/subscribe.php" method="post" class="bj-news-unsubscribe-form">
				<div class="form-group">
					<?=bitrix_sessid_post()?>
					<input type="hidden" name="unsubscribe" value="Y">
				</div>
			</form>

			<form action="<?=$this->GetFolder()?>/subscribe.php" method="post" class="hide bj-news-subscribe-form">
				<h3><b><?=GetMessage("SUBSCRIBE_ON_NEWS")?></b></h3>
				<div class="form-group">
					<?=bitrix_sessid_post()?>
					<input type="hidden" name="email" value="">
					<input type="email" name="ml" value="" class="form-control" required>
				</div>
				<button type="submit" class="btn btn-default btn-100"><?=GetMessage("SUBSCRIBE")?></button>
			</form>
		</div>
	</div>
	<?endif;?>
	</div>
</div>