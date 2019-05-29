<?php
global $MESS;

include(GetLangFileName(dirname(__FILE__) . "/lang/", "/index.php"));

class triggmine_abandonedcartrecovery extends CModule
{
	var $MODULE_ID = "triggmine.abandonedcartrecovery";
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $CLASS;

	function triggmine_abandonedcartrecovery()
	{
        global $MESS;
        $this->CLASS = 'CTriggmine';
        $this->PARTNER_NAME = "Triggmine";
        $this->PARTNER_URI = "http://www.triggmine.com/";
        $sPath = str_replace("\\", "/", __FILE__);
        $sPath = substr($sPath, 0, strlen($sPath) - strlen("/index.php"));

        include($sPath."/version.php");
        if (!empty($arModuleVersion['VERSION']) && !empty($arModuleVersion["VERSION_DATE"]))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = "TriggMine";
        $this->MODULE_DESCRIPTION = $MESS['triggmine_install_description'];
    }

	function DoInstall()
	{
        RegisterModule($this->MODULE_ID);
        CModule::IncludeModule($this->MODULE_ID);

        COption::SetOptionString($this->MODULE_ID,  "triggmine_is_on",   "N");
        COption::SetOptionString($this->MODULE_ID,  "triggmine_rest_api", "http://triggmine-api.cloudapp.net/api/Cart");
        COption::SetOptionString($this->MODULE_ID,  "triggmine_token",    "");

        RegisterModuleDependences("sale", "OnBasketAdd",            $this->MODULE_ID, $this->CLASS, "addProduct"        );
        RegisterModuleDependences("sale", "OnBasketUpdate",         $this->MODULE_ID, $this->CLASS, "updateProduct"     );
        RegisterModuleDependences("sale", "OnBeforeBasketDelete",   $this->MODULE_ID, $this->CLASS, "deleteProduct"     );
        RegisterModuleDependences("sale", "OnOrderAdd",             $this->MODULE_ID, $this->CLASS, "purchaseOrder"     );
        RegisterModuleDependences("main", "OnAfterUserAuthorize",   $this->MODULE_ID, $this->CLASS, "buyerLogin"        );
        RegisterModuleDependences("main", "OnEpilog",               $this->MODULE_ID, $this->CLASS, "retrieveLostCart"  );
        RegisterModuleDependences("main", "OnEpilog",               $this->MODULE_ID, $this->CLASS, "getVisitorId"      );

        return true;
	}

	function DoUninstall()
	{
        COption::RemoveOption($this->MODULE_ID, "triggmine_is_on"   );
        COption::RemoveOption($this->MODULE_ID, "triggmine_rest_api");
        COption::RemoveOption($this->MODULE_ID, "triggmine_token"   );

        UnRegisterModuleDependences("sale", "OnBasketAdd",          $this->MODULE_ID, $this->CLASS, "addProduct"        );
        UnRegisterModuleDependences("sale", "OnBasketUpdate",       $this->MODULE_ID, $this->CLASS, "updateProduct"     );
        UnRegisterModuleDependences("sale", "OnBeforeBasketDelete", $this->MODULE_ID, $this->CLASS, "deleteProduct"     );
        UnRegisterModuleDependences("sale", "OnOrderAdd",           $this->MODULE_ID, $this->CLASS, "purchaseOrder"     );
        UnRegisterModuleDependences("main", "OnAfterUserAuthorize", $this->MODULE_ID, $this->CLASS, "buyerLogin"        );
        UnRegisterModuleDependences("main", "OnEpilog",             $this->MODULE_ID, $this->CLASS, "retrieveLostCart"  );
        UnRegisterModuleDependences("main", "OnEpilog",             $this->MODULE_ID, $this->CLASS, "getVisitorId"      );

		UnRegisterModule($this->MODULE_ID);

		return true;
	}
}
?>
