<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>


		<?$APPLICATION->IncludeComponent("bitrix:main.register","techno",Array(
			"USER_PROPERTY_NAME" => "", 
			"SEF_MODE" => "Y", 
			"SHOW_FIELDS" => Array(), 
			"REQUIRED_FIELDS" => Array(), 
			"AUTH" => "Y", 
			"USE_BACKURL" => "Y", 
			"SUCCESS_PAGE" => "/index.php", 
			"SET_TITLE" => "Y", 
			"USER_PROPERTY" => Array(), 
			"SEF_FOLDER" => "/", 
			"VARIABLE_ALIASES" => Array()
			)
		);?> 


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>