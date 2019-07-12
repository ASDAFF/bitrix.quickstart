<?IncludeTemplateLangFile(__FILE__);?>
</div>
	</div>
    <?$APPLICATION->IncludeFile($APPLICATION->GetTemplatePath("include_areas/inc.twitter.php"), array(), array("MODE"=>"html"));?>
	<div class="fg"></div>	
</div>
<div class="footer">
	<p>
        <?$APPLICATION->IncludeFile($APPLICATION->GetTemplatePath("include_areas/inc.copyright.php"), array(), array("MODE"=>"html"));?>
    </p>
	<p><?=GetMessage("DEV")?></p>

	<div>
        <?$APPLICATION->IncludeFile($APPLICATION->GetTemplatePath("include_areas/inc.counters.php"), array(), array("MODE"=>"html"));?>
	</div>
</div>
</body>
</html>