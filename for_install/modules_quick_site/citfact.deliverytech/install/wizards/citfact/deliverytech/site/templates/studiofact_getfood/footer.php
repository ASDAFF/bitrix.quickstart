<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile(__FILE__); ?>
		<? if ($_REQUEST["open_popup"] != "Y") { ?>
						</div>
						<footer>
							<div class="box padding">
								<div class="row"> 				 
									<div class="col-md-6">
										<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_text.php"), false);?>
									</div>
									<div class="col-md-6">
										<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_text2.php"), false);?>
									</div>
								</div>
								<div class="row"> 				 
									<div class="col-md-6">
										<div class="socials">
											<?=GetMessage("STUDIOFACT_SOCIALS");?><br />
											<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_socials.php"), false);?>
										</div>
									</div>
									<div class="col-md-6">
										<br /><br /><div id="bx-composite-banner"></div>
									</div>
								</div>
							</div>
							<?//$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/logic_box.php"), false);?>
						</footer>
					</div>
				</div>
			</div>
		</div>
		<? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/feedback_form.php"), false); ?>
		<? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/metrics.php"), false); ?>
		<? } else { ?>
			<script type="text/javascript">$(document).ready(function () { $("#bx-composite-banner").remove(); });</script>
		<? } ?>
		<div id="sfp_add_to_basket_head" style="display: none;"><?=GetMessage("SFP_ADD_TO_BASKET_HEAD");?></div>
		<div id="sfp_show_offers_head" style="display: none;"><?=GetMessage("SFP_SHOW_OFFERS_HEAD");?></div>
	</body>
</html>