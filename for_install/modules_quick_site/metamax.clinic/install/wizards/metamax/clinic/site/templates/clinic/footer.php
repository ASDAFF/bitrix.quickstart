<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
</div>

<div class="clear"></div>
</div>

<div id="footer">
<div class="box-c"> <em class="ctl"><b>&bull;</b></em> <em class="ctr"><b>&bull;</b></em></div> 
<div class="box-inner">

<table cellspacing="0" cellpadding="0" width="100%" class="footer-content">
<tr>

<td valign="top" width="35%">
	
	<div class="copyright">
	<?$APPLICATION->IncludeFile(
		SITE_DIR."include/copyright.php",
		Array(),
		Array("MODE"=>"html")
	);?>
	</div>
	<div><a href="#SITE_DIR#contacts/"><?=GetMessage("FOOTER_CONTACTS")?></a></div>
	
</td>
	
<td valign="top" width="40%">
	
	<div>
	<?$APPLICATION->IncludeComponent(
		"bitrix:search.form",
		"flat",
		Array(
			"USE_SUGGEST" => "N",
			"PAGE" => "#SITE_DIR#search/index.php"
		),
	false
	);?> 
	</div>
	<br />
	<div><a href="#SITE_DIR#sitemap/"><?=GetMessage("FOOTER_SITEMAP")?></a></div>
	
</td>
	
<td valign="top" width="25%">
	<div class="metamax">
	<div><?=GetMessage("FOOTER_METAMAX")?></div>
	<br />
	<div><img src="<?=SITE_TEMPLATE_PATH?>/img/logo_bitrix.gif" width="88" height="18" alt="" border="0" /></div>
	</div>
</td>

</tr>
</table>
</div>
</div>

</div>

</body>
</html>