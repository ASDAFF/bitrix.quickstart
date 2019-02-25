<?
class aat_iblockprops extends CModule
{
    var $MODULE_ID = "aat.iblockprops";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;

	public function __construct()
    {
        $arModuleVersion = array();
        
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");
        
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        
		$this->PARTNER_NAME = "Тарханов Александр";
		$this->PARTNER_URI = "http://www.a-tarhanov.ru/";
        
        $this->MODULE_NAME = "Удобная привязка к разделам/элементам инфоблока";
        $this->MODULE_DESCRIPTION = "После установки вы сможете пользоваться пользовательскими типами свойств «Привязка к разделам (checkbox/radio)» и «Привязка к элементам (checkbox/radio)»";
    }
    
    public function DoInstall()
    {
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/aat.iblockprops/install/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/aat.iblockprops/', true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/aat.iblockprops/install/css/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/css/aat.iblockprops/', true, true);
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'aat.iblockprops', 'CAATIBlockPropSection', 'GetUserTypeDescription');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'aat.iblockprops', 'CAATIBlockPropElement', 'GetUserTypeDescription');
        RegisterModule("aat.iblockprops");
    }
    
    public function DoUninstall()
    {
		DeleteDirFilesEx('/bitrix/js/aat.iblockprops/');
		DeleteDirFilesEx('/bitrix/css/aat.iblockprops/');
        UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'aat.iblockprops', 'CAATIBlockPropSection', 'GetUserTypeDescription');
        UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'aat.iblockprops', 'CAATIBlockPropElement', 'GetUserTypeDescription');
        UnRegisterModule("aat.iblockprops");
    }
}
?>