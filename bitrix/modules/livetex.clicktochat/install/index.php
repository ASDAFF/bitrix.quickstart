<?
IncludeModuleLangFile(__FILE__);

class livetex_clicktochat extends CModule
{
	var $MODULE_ID = "livetex.clicktochat";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function livetex_clicktochat()
	{
		$this->MODULE_ID = "livetex.clicktochat"; 
		$this->MODULE_NAME = GetMessage("LIVETEX_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("LIVETEX_DESCR");
		$this->PARTNER_NAME = "livetex";
		$this->PARTNER_URI = "http://www.livetex.ru";
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
	}
    
    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        RegisterModule("livetex.clicktochat");
        $this->Install();
        LocalRedirect("/bitrix/admin/livetex.php");
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        UnRegisterModule("livetex.clicktochat");
        $this->Uninstall();
        LocalRedirect("/bitrix/admin/module_admin.php");
    }
    
    function Install()
    {
        $f = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/livetex.php","w");
        fwrite($f,'<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/livetex.clicktochat/admin/livetex.php");?>');
        fclose($f);
        $footer =
'<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/livetex.clicktochat/option.php");?>
<script type="text/javascript"> /* build:::7 */
	var liveTex = true,
		liveTexID = <?=intval($option["liveID"])?>,
		liveTex_object = true;
	(function() {
		var lt = document.createElement("script");
		lt.type ="text/javascript";
		lt.async = true;
        lt.src = "http://cs15.livetex.ru/js/client.js";
		var sc = document.getElementsByTagName("script")[0];
		if ( sc ) sc.parentNode.insertBefore(lt, sc);
		else  document.documentElement.firstChild.appendChild(lt);
	})();
</script>
<?
if(defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED===true)
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog.php");
}
?>';
               
        $f = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php","w");
        fwrite($f,$footer);
        fclose($f);
        
        $t = file_get_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/livetex.clicktochat/lang/ru/admin/livetex.php");
        $codepage = $this->get_encoding($t);
        if($codepage != LANG_CHARSET){
            $t = iconv($codepage,LANG_CHARSET,$t);
            $fp = fopen($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/livetex.clicktochat/lang/ru/admin/livetex.php","w+");
            fwrite($fp,$t);
            fclose($fp); 
        }
        CopyDirFiles($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/livetex.clicktochat/install/res", $_SERVER['DOCUMENT_ROOT']."/upload/livetex/");
        
    }
    
    function Uninstall()
    {
        $footer = '<?if(defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED===true){require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog.php");}?>';
        $f = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php","w");
        fwrite($f,$footer);
        fclose($f);
        @unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/livetex.php");
    }
    
    function get_encoding($str)
    {
        $cp_list = array('UTF-8', 'windows-1251');
        foreach ($cp_list as $k=>$codepage){
            if (md5($str) === md5(iconv($codepage, $codepage, $str))){
                return $codepage;
            }
        }
        return null;
    }
    
}
?>