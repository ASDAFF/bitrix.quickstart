<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?if($_POST["ELEMENT_ID"]){
	if($_POST["OFFERS_ID"]){
		foreach($_POST["OFFERS_ID"] as $id){?>
			<div class="sku_stores_<?=$id?>" style="display: none;">
				<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "main", array(
						"PER_PAGE" => "10",
						"USE_STORE_PHONE" => $_POST["USE_STORE_PHONE"],
						"SCHEDULE" => $_POST["SCHEDULE"],
						"USE_MIN_AMOUNT" => $_POST["USE_MIN_AMOUNT"],
						"MIN_AMOUNT" => $_POST["MIN_AMOUNT"],
						"ELEMENT_ID" => $id,
						"STORE_PATH"  =>  $_POST["STORE_PATH"],
						"MAIN_TITLE"  =>  $_POST["MAIN_TITLE"],
						"MAX_AMOUNT"=>$_POST["MAX_AMOUNT"],
						"USE_ONLY_MAX_AMOUNT" => $_POST["USE_ONLY_MAX_AMOUNT"],
						"SHOW_EMPTY_STORE" => $_POST['SHOW_EMPTY_STORE'],
						"SHOW_GENERAL_STORE_INFORMATION" => $_POST['SHOW_GENERAL_STORE_INFORMATION'],
						"USE_ONLY_MAX_AMOUNT" => $_POST["USE_ONLY_MAX_AMOUNT"],
						"USER_FIELDS" => $_POST['USER_FIELDS'],
						"FIELDS" => $_POST['FIELDS'],
						"STORES" => $_POST['STORES'],
					),
					false
				);?>
			</div>
		<?}
	}else{?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "main", array(
				"PER_PAGE" => "10",
				"USE_STORE_PHONE" => $_POST["USE_STORE_PHONE"],
				"SCHEDULE" => $_POST["SCHEDULE"],
				"USE_MIN_AMOUNT" => $_POST["USE_MIN_AMOUNT"],
				"MIN_AMOUNT" => $_POST["MIN_AMOUNT"],
				"ELEMENT_ID" => $_POST["ELEMENT_ID"],
				"STORE_PATH"  =>  $_POST["STORE_PATH"],
				"MAIN_TITLE"  =>  $_POST["MAIN_TITLE"],
				"MAX_AMOUNT"=>$_POST["MAX_AMOUNT"],
				"USE_ONLY_MAX_AMOUNT" => $_POST["USE_ONLY_MAX_AMOUNT"],
				"SHOW_EMPTY_STORE" => $_POST['SHOW_EMPTY_STORE'],
				"SHOW_GENERAL_STORE_INFORMATION" => $_POST['SHOW_GENERAL_STORE_INFORMATION'],
				"USE_ONLY_MAX_AMOUNT" => $_POST["USE_ONLY_MAX_AMOUNT"],
				"USER_FIELDS" => $_POST['USER_FIELDS'],
				"FIELDS" => $_POST['FIELDS'],
				"STORES" => $_POST['STORES'],
			),
			false
		);?>
	<?}
}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>