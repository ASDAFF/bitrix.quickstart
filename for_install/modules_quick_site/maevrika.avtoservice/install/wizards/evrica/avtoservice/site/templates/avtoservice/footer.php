<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?> </div>

  <div id="content">
    <div id="sidebar">
	<?$APPLICATION->IncludeFile(
		SITE_DIR."include/banner.php",
		Array(),
		Array("MODE"=>"html")
	);?>
	
      <div class="information-block">
        <div class="top"></div>
      
        <div class="information-block-inner"><noindex>
            <h3><a href="#SITE_DIR#sharehold/" ><?=$MESS["TITLE_SPEC_TEMPLATE_FOOTER_NAME"]?></a></h3>
             <p><?=$MESS["TEXT_SPEC_TEMPLATE_FOOTER_NAME"]?></p>
          </noindex></div>
      
        <div class="bottom"></div>
      </div>
    <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"left",
	Array(
		"ROOT_MENU_TYPE" => "left",
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => ""
	),
false,
Array(
	'ACTIVE_COMPONENT' => 'N'
)
);?> 
    </div>
  </div>

   <table border="0" cellspacing="0" cellpadding="10" width="878" bgcolor="#f0fafa" align="center">
    <tbody>
      <tr><td bgcolor="#f0fafa" valign="middle" width="478" align="left">
          <p><?$APPLICATION->IncludeFile(
		SITE_DIR."include/copyright.php",
		Array(),
		Array("MODE"=>"html")
	);?>
            </p>
        &nbsp;&nbsp;&nbsp;
<img src="#SITE_DIR#images/logo-kreml.png" valign="middle" style="padding-right: 7px;">

 </td><td bgcolor="#f0fafa" valign="middle" width="303" align="right"><?=$MESS["TEL_TEMPLATE_FOOTER_NAME"]?><?$APPLICATION->IncludeFile(
		SITE_DIR."include/telefon.php",
		Array(),
		Array("MODE"=>"html")
	);?> 
          <br />
        <?$APPLICATION->IncludeFile(
		SITE_DIR."include/regim.php",
		Array(),
		Array("MODE"=>"html")
	);?>
          <br />
        <a href="#SITE_DIR#contacts/"><?=$MESS["SHEMA_TEMPLATE_FOOTER_NAME"]?></a>  </td></tr>
    </tbody>
  </table>

</div>
</body>
</html>