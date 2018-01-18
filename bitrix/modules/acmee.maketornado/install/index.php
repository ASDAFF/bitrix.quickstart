<?
IncludeModuleLangFile(__FILE__);
Class acmee_maketornado extends CModule
{
    var $MODULE_ID = 'acmee.maketornado';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    public function acmee_maketornado()
    {
	    $this->MODULE_ID = GetMessage("MODULE"); 

	    $this->MODULE_NAME = GetMessage("NAME");
	    $this->MODULE_DESCRIPTION = GetMessage("DESCRIPTION");  
        $this->PARTNER_NAME = GetMessage("PARTNER_NAME"); 
        //В PARTNER_URI нельзя использовать языковое сообщение через GetMessage, только непосредственно строку.
        $this->PARTNER_URI = "http://maketornado.com/";	

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
    }

    public function DoInstall()
    {
        RegisterModule("acmee.maketornado");
        $this->_install();
        LocalRedirect("/bitrix/admin/maketornado.php");
    }
 
    public function DoUninstall()
    {
        UnRegisterModule("acmee.maketornado");

        $footer = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
        $footer=preg_replace("/<!-- Hmead -->.*<!-- End Hmead Code -->/s","",$footer);

        $f = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php","w");
        fwrite($f,$footer);
        fclose($f);



        @unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/maketornado.php");
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/acmee.maketornado/admin/maketornado/",$_SERVER['DOCUMENT_ROOT']."/bitrix/images/maketornado/");

	}
	private function _install()
	{
        $f = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/maketornado.php","w");
        fwrite($f,'<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/acmee.maketornado/admin/maketornado.php");?>');
        fclose($f);

        CopyDirFiles($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/acmee.maketornado/admin/maketornado", $_SERVER['DOCUMENT_ROOT']."/bitrix/images/maketornado/");
	
        $footer ='
<!-- Hmead -->
<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/acmee.maketornado/option.php");?>
<script type="text/javascript">
 var _paq = _paq || []; _paq.push(["enableLinkTracking"]); _paq.push(["trackPageView"]);
 var _hmead = _hmead || {};
 _hmead.handler = function(){ _paq.push(["trackGoal", 2]); };
 (function() {
 var u=(("https:" == document.location.protocol) ? "https" : "http") + "://track.maketornado.com/";
 _paq.push(["setSiteId", "<?=intval($option["maketornadoID"])?>"]);
 _paq.push(["setTrackerUrl", u+"hmead.php"]);
 var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
 g.defer=true; g.async=true; g.src=u+"hmead.js"; s.parentNode.insertBefore(g,s);
 })();
</script>
<noscript><img src="http://track.maketornado.com/hmead.php?idsite=<?=intval($option["maketornadoID"])?>&rec=1&idGoal=1" style="border:0" alt="" /></noscript>
<!-- End Hmead Code -->';

        $text=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
        $f = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php","w");
        fwrite($f,$footer.$text);
        fclose($f);

    }
}
?>