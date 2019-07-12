</div>
</div>


	<?if($APPLICATION->GetCurPage(false)==SITE_DIR):?> 
<div class="right">
    <div id="content_1" class="content">

<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR."include/news.php",
							"AREA_FILE_RECURSIVE" => "N",
							"EDIT_MODE" => "html",
						),
						false,
						Array('HIDE_ICONS' => 'Y')
					);?>
        
    </div>
  </div>

        <?else:?>
        
        
         <?endif?>
        
        
</section>
<footer>
  <nav>
      
  <?$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel", array(
	"ROOT_MENU_TYPE" => "bottom",
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "36000000",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
    );?>
      
  </nav>
    
<?$APPLICATION->IncludeFile(
        SITE_DIR."include/footer.php",
        Array(),
        Array("MODE"=>"html")
);?>

</footer>

<script type="text/javascript">
$("#tabs2").tabs({
show: function(event, ui) {
var $target = $(ui.panel);

$('.content:visible').effect(
'explode',
{},
1500,
function(){
$target.fadeIn();
});
}
});
</script> 
</body>
</html>
