
                            </div> <!-- end container -->
						 </div>
					  </div>
					 </div>
	             <!---- About Us ------>
	           <div class="about" id="about">
	 		 	<div class="wrap">
	 		 	    <h2><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/about_us_title.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h2>
	 		 	      <div class="line rose"><span> </span></div>
	 		            <h4><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/about_us_desc.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h4>
	 		 	                <div class="testimonials">
	 		 	       <?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/about_us.php",
					"EDIT_TEMPLATE" => ""
					),false);?>         
	 
					             </div>
					        </div>
	 		           </div>
	           <!--- Testimonials ----------->
	            <div class="feedback" id="contact">
	 		 	   <div class="wrap">
	 		 	    <h2><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/feedback_title.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h2>
	 		 	      <div class="line blue"><span> </span></div>
	 		            <h4><?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/feedback_desc.php",
					"EDIT_TEMPLATE" => ""
					),false);?></h4>
					<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/main_feedback.php",
					"EDIT_TEMPLATE" => ""
					),false);?>
					           </div>
	 		           </div>
	            
	            
	      </div>       	    
      </div>
     		<div class="copy-right">
			<div class="wrap">
				<?$APPLICATION->IncludeComponent("bitrix:main.include","", Array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/copyrights.php", "EDIT_TEMPLATE" => "" ),false);?>
			</div>
	  </div>       
   <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.scrollTo.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.nav.js"></script>
	<script>
	$(document).ready(function() {
		$('#nav').onePageNav({
			begin: function() {
			console.log('start')
			},
			end: function() {
			console.log('stop')
			}
		});
	});
	</script>
	</body>
</html>
