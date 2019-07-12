<footer id="page-footer">

	    	<div class="container">
				<div class="row-fluid">
				
<div class="span4"> 
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "left_bottom",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
</div>		
				
					
					
					<div class="span4">
						<div>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "center_bottom",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
									
									
									
									<?$APPLICATION->IncludeComponent(
									"bitrix:main.include",
									"",
									Array(
										"AREA_FILE_SHOW" => "sect",
										"AREA_FILE_SUFFIX" => "personal",
										"AREA_FILE_RECURSIVE" => "Y",
										"EDIT_TEMPLATE" => ""
									)
								);?>
									
									
									
						</div>						
					</div>
					
					
					
					
					<div class="span4">
					<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "right_bottom",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
													
					</div>
					
				</div>
	    	</div>
	    
			<div class="copyright">2013 <a href="#">Orange Club</a> <span class="author"> <a href="http://ErDesign.ru" title="ErDesign">ErDesign</a></span></div>
	    </footer>
<?if ($APPLICATION->GetCurPage()!="/"):?>	    
<script>
$("#page-header").css("border-bottom","none");
</script>
<?endif;?>
      
     
                
    </body>
</html>