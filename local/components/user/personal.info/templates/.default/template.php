<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
    var ajaxDir = "<?=$this->GetFolder();?>";
</script>
<div id="mainProfile">
	<form action="" method="GET" id="personalForm">
		<div class="row profileTable">

				<div class="col-xl-6">
					<span class="heading"><?=GetMessage("HEADING")?></span>
					<ul class="profileSettings">
	                    <li>
	                        <label><?=GetMessage('LAST_NAME')?> <?=GetMessage('NAME')?> <?=GetMessage('SECOND_NAME')?></label>
	                        <input type="text" name="FIO" value="<?=$arResult["USER"]["LAST_NAME"]?> <?=$arResult["USER"]["NAME"]?> <?=$arResult["USER"]["SECOND_NAME"]?>" class="inputText">
	                        <label><?=GetMessage('EMAIL')?></label>
	                        <input type="text" name="EMAIL" value="<?=$arResult["USER"]["EMAIL"]?>" class="inputText">
	                        <label><?=GetMessage('USER_MOBILE')?></label>
	                        <input type="text" name="USER_MOBILE" value="<?=$arResult["USER"]["PERSONAL_MOBILE"]?>" class="inputTel">
	                        <label><?=GetMessage('USER_CITY')?></label>
	                        <input type="text" name="USER_CITY" value="<?=$arResult["USER"]["PERSONAL_CITY"]?>" class="inputTel">
	                        <label><?=GetMessage('USER_ZIP')?></label>
	                        <input type="text" name="USER_ZIP" value="<?=$arResult["USER"]["PERSONAL_ZIP"]?>" class="inputTel">
	                    </li>
	                    <li>
	                            <label><?=GetMessage('USER_STREET')?></label>
	                            <textarea rows="10" cols="45" name="USER_STREET"><?if(!empty($arResult["USER"]["PERSONAL_STREET"])):?><?=$arResult["USER"]["PERSONAL_STREET"]?><?else:?><?=$arResult["USER"]["CITY_NAME"]?><?endif;?></textarea>
	                            <span class="heading"><?=GetMessage("CHANGE_PASS")?></span>
	                            <label><?=GetMessage("PASS")?></label>
	                            <input type="text" name="USER_PASSWORD" value="" class="inputTel">
	                            <label><?=GetMessage("REPASS")?></label>
	                            <input type="text" name="USER_PASSWORD_CONFIRM" value="" class="inputTel">
	                            <a href="#" class="submit"><?=GetMessage("SAVE")?></a>
	                    </li>
	                </ul>
	    		</div>
				<div class="col-xl-6">
					<div class="wrap">
						<span class="heading"><?=GetMessage("SETTINGS")?></span>
						<ul>
							<li><a href="<?=SITE_DIR?>personal/cart/"><?=GetMessage("CART")?> <span class="cnt"><?=$arResult["BASKET_COUNT"]?></span></a></li>
							<li><a href="<?=SITE_DIR?>compare/"><?=GetMessage("COMPARE")?> <span class="cnt"><?=count($_SESSION["COMPARE_LIST"]["ITEMS"]) ? count($_SESSION["COMPARE_LIST"]["ITEMS"]) : 0 ?></span></a></li>
							<li><a href="<?=SITE_DIR?>personal/history/"><?=GetMessage("HISTORY")?></a></li>
							<li><a href="<?=SITE_DIR?>callback/"><?=GetMessage("CALLBACK")?></a></li>
						</ul>
					</div>
				</div>

		</div>
	</form>
</div>

<div id="elementError">
  <div id="elementErrorContainer">
    <span class="heading"><?=GetMessage("ERROR")?></span>
    <a href="#" id="elementErrorClose"></a>
    <p class="message"></p>
    <a href="#" class="close"><?=GetMessage("CLOSE")?></a>
  </div>
</div>