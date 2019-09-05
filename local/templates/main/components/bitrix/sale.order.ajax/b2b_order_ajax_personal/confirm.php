<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!empty($arResult["ORDER"]))
{
	?>
	<div class="col-sm-24 sm-padding-no">
		<div class="main_inner_title">
			<h1 class="text"><?=GetMessage("MS_ORDER_TITLE");?></h1>
		</div>
	</div> <!--end col-sm-24-->
	<div class="col-sm-24 sm-padding-no order_end">
		<div class="wrap_order_number">
			<p><?=GetMessage("MS_ORDER_SUCCESS1")?> <span><?=GetMessage("MS_ORDER_NUMBER");?><?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></span> <?=GetMessage("MS_ORDER_SUCCESS2")?> <?=$arResult["ORDER"]["DATE_INSERT"]?> <?=GetMessage("MS_ORDER_SUCCESS3")?>.</p>
		</div>
	</div>
	<div class="col-sm-24 order_end">
		<div class="row">
			<div class="col-sm-16 sm-padding-left-no">
				<div class="wrap_personal_link">
					<div class="row">
						<div class="col-sm-24 col-md-12 sm-padding-right-no">
							<p class="personal_text">
							<?=GetMessage("MS_ORDER_SUCCESS4")?>
							</p>
						</div>
						<div class="col-sm-24 col-md-12">
							<a class="personal_link" href="<?=$arParams["PATH_TO_PERSONAL"]?>">
								<span class="personal_link_in">
									<span class="desc_fly_1_bg"><?=GetMessage("MS_ORDER_SUCCESS5")?></span>
								</span>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-8 sm-padding-right-no">
				<div class="wrap_personal_desc">
					<p class="personal_desc"><?=GetMessage("MS_ORDER_SUCCESS6")?></p>
					<div class="nail_1"></div>
					<div class="nail_2"></div>
					<div class="nail_3"></div>
					<div class="nail_4"></div>
				</div>
			</div>
		</div>
	</div>
	<?
	if (!empty($arResult["PAY_SYSTEM"]))
	{
		?>
		<div class="col-sm-24 sm-padding-no order_end">
			<div class="wrap_order_title">
				<h2><?=GetMessage("MS_ORDER_SUCCESS_PAY")?></h2>
			</div>
		</div>
		<div class="col-sm-24 sm-padding-no order_end">
			<div class="wrap_order_pay">
				<div class="logo_pay">
					<?=CFile::ShowImage($arResult["PAY_SYSTEM"]["LOGOTIP"], 100, 100, "border=0 class='img-responsive'", "", false);?>
					<div class="paysystem_name"><b><?= $arResult["PAY_SYSTEM"]["NAME"] ?></b></div>
				</div>
				<?
				if(strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
				{
						if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
						{
							?>
							<script language="JavaScript">
								window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
							</script>
							<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
							<?
							if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE']))
							{
								?><br />
								<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
								<?
							}
						}
						else
						{
							if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
							{
								include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
							}
						}
				}
				?>
			</div>
		</div>
		<?
	}
}
?>