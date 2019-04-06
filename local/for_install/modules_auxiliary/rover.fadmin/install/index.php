<?php
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class rover_fadmin extends CModule
{
    var $MODULE_ID	= "rover.fadmin";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $PARTNER_NAME;
    var $PARTNER_URI;

    /**
     * rover_fadmin constructor.
     */
    function __construct()
    {
        global $errors;
        
		$arModuleVersion    = array();
        $errors             = array();

        require(__DIR__ . "/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION		= $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE	= $arModuleVersion["VERSION_DATE"];
        } else {
            $errors[] = Loc::getMessage('rover_fa__version_info_error');
		}

        $this->MODULE_NAME			= Loc::getMessage("rover_fa__name");
        $this->MODULE_DESCRIPTION	= Loc::getMessage("rover_fa__descr");
        $this->PARTNER_NAME         = GetMessage("rover_fa__partner_name");
        $this->PARTNER_URI          = GetMessage("rover_fa__partner_uri");
	}

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function DoInstall()
    {
        global $APPLICATION;
        $rights = $APPLICATION->GetGroupRight($this->MODULE_ID);

        if ($rights == "W")
            $this->ProcessInstall();
	}

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function DoUninstall()
    {
        global $APPLICATION;
        $rights = $APPLICATION->GetGroupRight($this->MODULE_ID);

        if ($rights == "W")
            $this->ProcessUninstall();
    }

    /**
     * @return array
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function GetModuleRightsList()
    {
        return array(
            "reference_id" => array("D", "R", "W"),
            "reference" => array(
                Loc::getMessage('rover_fa__reference_deny'),
                Loc::getMessage('rover_fa__reference_read'),
                Loc::getMessage('rover_fa__reference_write')
            )
        );
    }

	/**
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	private function ProcessInstall()
    {
        global $APPLICATION, $errors;

        if (PHP_VERSION_ID < 50306)
            $errors[] = Loc::getMessage('rover_fa__php_version_error');

        $this->copyFiles();

        if (empty($errors))
            ModuleManager::registerModule($this->MODULE_ID);

	    $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_fa__install_title"), $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/message.php"));
    }

	/**
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	private function ProcessUninstall()
	{
        global $APPLICATION, $errors;

        $this->removeFiles();

        //if (empty($errors))
        // uninstall anywhere
        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_fa__uninstall_title"), $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/unMessage.php"));
	}

    /**
     * @param $fromDir
     * @param $toDir
     * @author Pavel Shulaev (http://rover-it.me)
     */
    private function copyDir($fromDir, $toDir)
    {
        global $errors;

        $dir = $this->checkDir($toDir);

        if (!is_writable($dir->getPhysicalPath())){
            $errors[] = Loc::getMessage('rover_fa__ERROR_PERMISSIONS', array('#path#' => $dir->getPhysicalPath()));
            return;
        }

        $fromDir = getLocalPath("modules/". $this->MODULE_ID . $fromDir);

        if (!\CopyDirFiles(
            Application::getDocumentRoot() . $fromDir,
            Application::getDocumentRoot() . $toDir,
            TRUE,
            TRUE)
        )
            $errors[] = Loc::getMessage('rover_fa__ERROR_COPY_FILES',
                array('#pathFrom#' => $fromDir, '#toPath#' => $toDir));
    }

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    private function copyFiles()
    {
        $this->copyDir('/install/js/', '/bitrix/js/' . $this->MODULE_ID . '/');
        $this->copyDir('/install/css/', '/bitrix/css/' . $this->MODULE_ID . '/');
    }

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    private function removeFiles()
    {
       $this->deleteDir('/bitrix/js/' . $this->MODULE_ID . '/');
       $this->deleteDir('/bitrix/css/' . $this->MODULE_ID . '/');
    }

    /**
     * @param $dirName
     * @author Pavel Shulaev (http://rover-it.me)
     */
    private function deleteDir($dirName)
    {
        global $errors;

        $dirName = str_replace(array('//', '///'), '/', Application::getDocumentRoot() . '/' . $dirName);

        if (!is_writable($dirName)){
            $errors[] = Loc::getMessage('rover_fa__ERROR_PERMISSIONS', array('#path#' => $dirName));
            return;
        }

        Directory::deleteDirectory($dirName);
    }

    /**
     * @param $path
     * @return Directory
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @author Pavel Shulaev (http://rover-it.me)
     */
    private function checkDir($path)
    {
        $path = Application::getDocumentRoot() . $path;

        if (Directory::isDirectoryExists($path))
            $dir = new Directory($path);
        else
            $dir = Directory::createDirectory($path);

        $dir->markWritable();

        return $dir;
    }
}
