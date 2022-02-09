</div><!--/.b-content-->
		</div><!--/.b-wrapper-->
	</div><!--/.b-cover-->
</div><!--/.b-page-->
<footer class="b-footer">
	<div class="b-wrapper clearfix">
		<div class="b-footer__left">
   <?$APPLICATION->IncludeComponent(
	"bitrix:main.include", 
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/inc/bottom_left_block.php",
		"EDIT_TEMPLATE" => ""
	)
);?>     
		</div>
		<div class="b-footer__right">
                    
 <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"bottom",
Array(),
false
);?> 
			<div class="clearfix">
				<div class="b-address">
   <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/inc/bottom_address.php",
		"EDIT_TEMPLATE" => ""
	)
);?>    
                                </div>
				<div class="b-worktime"><?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/inc/bottom_worktime.php",
		"EDIT_TEMPLATE" => ""
	)
);?></div>
			</div>
		</div>
	</div>
</footer><!--/.b-footer-->


<?
//popup login form
$APPLICATION->IncludeComponent("bitrix:system.auth.form", "", array(
	"SHOW_ERRORS" => "Y",
	"AUTH_URL"=>"/personal/",
	),
	false
);
?>  
</body>
</html>
        


<?
return;?>			</div><!--/.b-content-->
		</div><!--/.b-wrapper-->
	</div><!--/.b-cover-->
</div><!--/.b-page-->
<footer class="b-footer">
	<div class="b-wrapper clearfix">
		<div class="b-footer__left">
   <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/inc/bottom_left_block.php",
		"EDIT_TEMPLATE" => ""
	)
);?>     
		</div>
		<div class="b-footer__right">
                    
 <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"bottom",
Array(),
false
);?> 
			<div class="clearfix">
				<div class="b-address">
   <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/inc/bottom_address.php",
		"EDIT_TEMPLATE" => ""
	)
);?>    
                                </div>
				<div class="b-worktime"><?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/inc/bottom_worktime.php",
		"EDIT_TEMPLATE" => ""
	)
);?></div>
			</div>
		</div>
	</div>
</footer><!--/.b-footer-->





<?
//popup login form
$APPLICATION->IncludeComponent("bitrix:system.auth.form", "", array(
	"SHOW_ERRORS" => "Y",
	"AUTH_URL"=>"/personal/",
	),
	false
);
?>

</body>
</html>