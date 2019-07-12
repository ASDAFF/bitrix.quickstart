<?php

use Bitrix\Main\Localization\Loc;

/**
 * Class for module installation
 */
class PrmediaMinimarketInstallHelper
{
	var $module;
	var $MODULE_ID;
	
	/**
	 * @var string Underscored module id 
	 */
	var $prmediaModuleId;

	/**
	 * @var string Path to bitrix folder 
	 */
	var $prmBx;

	/**
	 * @var string Module folder (bitrix || local) 
	 */
	var $prmModFolder;

	/**
	 * @var string Path to module / folder 
	 */
	var $prmMod;

	/**
	 * @var string Path to module /install subfolder 
	 */
	var $prmInstall;

	/**
	 * @var string Path to module /install/components subfolder 
	 */
	var $prmInstallComp;
	
	/**
	 * @var string Path to module /install/wizards subfolder 
	 */
	var $prmInstallWizards;

	/**
	 * @var string Path to module /install/db/{database_type} subfolder 
	 */
	var $prmInstallDb;

	/**
	 * @var string Error messages
	 */
	var $prmediaError = false;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->prmediaInitVariables();
	}

	/**
	 * Initialize module variables
	 */
	private function prmediaInitVariables()
	{
		global $DBType;

		$this->module = $module;
		$this->MODULE_ID = 'prmedia.minimarket';
		$this->prmediaModuleId = str_replace('.', '_', $this->MODULE_ID);

		$this->prmModFolder = 'bitrix';
		if (strpos(str_replace('\\', '/', __FILE__), '/local/') !== false)
		{
			$this->prmModFolder = 'local';
		}
		$this->prmBx = $_SERVER['DOCUMENT_ROOT'] . "/bitrix";
		$this->prmMod = $_SERVER['DOCUMENT_ROOT'] . "/$this->prmModFolder/modules/$this->MODULE_ID";
		$this->prmInstall = "$this->prmMod/install";
		$this->prmInstallComp = "$this->prmInstall/components";
		$this->prmInstallWizard = "$this->prmInstall/wizards";
		$this->prmInstallDb = "$this->prmInstall/db/$DBType";
	}

	/**
	 * Install tables from SQL scripts
	 */
	public function prmediaInstallDb()
	{
		global $APPLICATION, $DB;

		$this->prmediaError = $DB->RunSQLBatch("$this->prmInstallDb/install.sql");
		if ($this->prmediaError !== false)
		{
			$APPLICATION->ThrowException(implode('', $this->errors));
			return false;
		}

		$patcheFolder = "$this->prmInstallDb/up";
		$files = scandir($patcheFolder);
		sort($files, 'SORT_STRING');
		foreach ($files as $patch)
		{
			$patchf = "$patcheFolder/$patch";
			if (in_array($patch, array('.', '..')) || is_dir($patchf))
			{
				continue;
			}
			$this->prmediaError = $DB->RunSQLBatch($patchf);
			if ($this->prmediaError !== false)
			{
				$APPLICATION->ThrowException(implode('', $this->errors));
				return false;
			}
		}

		return true;
	}

	/**
	 * 
	 */
	public function prmediaSeedDb()
	{
		require "$this->prmInstall/db/seed.php";
	}
	
	/**
	 * Remove tables from database
	 */
	public function prmediaRemoveDb()
	{
		global $APPLICATION, $DB;

		$patcheFolder = "$this->prmInstallDb/down";
		$files = scandir($patcheFolder);
		sort($files, 'SORT_STRING');
		$files = array_reverse($files);
		foreach ($files as $patch)
		{
			$patchf = "$patcheFolder/$patch";
			if (in_array($patch, array('.', '..')) || is_dir($patchf))
			{
				continue;
			}
			$this->prmediaError = $DB->RunSQLBatch($patchf);
			if ($this->prmediaError !== false)
			{
				$APPLICATION->ThrowException(implode('', $this->errors));
				return false;
			}
		}

		$this->prmediaError = $DB->RunSQLBatch("$this->prmInstallDb/uninstall.sql");
		if ($this->prmediaError !== false)
		{
			$APPLICATION->ThrowException(implode('', $this->errors));
			return false;
		}

		return true;
	}

	/**
	 * Copy components
	 */
	public function prmediaCopyComponents()
	{
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/prmedia'))
		{
			mkdir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/prmedia');
		}
		CopyDirFiles($this->prmInstallComp, "$this->prmBx/components", true, true);
	}

	/**
	 * Remove components
	 */
	public function prmediaRemoveComponents()
	{
		$this->_prmediaRemoveRecursive($this->prmInstallComp, 'components');
	}

	/**
	 * Create links on module /admin files
	 */
	public function prmediaCopyAdminFiles()
	{
		$from = "$this->prmMod/admin";
		$to = "$this->prmBx/admin/$this->prmediaModuleId";
		$content = '<?php require($_SERVER[\'DOCUMENT_ROOT\'] . \'/' . $this->prmModFolder . '/modules/' . $this->MODULE_ID . '/admin/#CONTENT#\');';
		$exclude = array('menu.php');
		
		if ($reader = opendir($from))
		{
			while (false !== ($file = readdir($reader)))
			{
				if (strpos($file, '.php') !== false && !in_array($file, $exclude))
				{
					$toPath = $to . '_' . $file;
					file_put_contents($toPath, str_replace('#CONTENT#', $file, $content));
				}
			}
			closedir($reader);
		}
	}

	/**
	 * Remove links on module /admin files
	 */
	public function prmediaRemoveAdminFiles()
	{
		$from = "$this->prmMod/admin";
		$to = "$this->prmBx/admin/$this->prmediaModuleId";

		if ($reader = opendir($from))
		{
			while (false !== ($file = readdir($reader)))
			{
				if (strpos($file, '.php') !== false)
				{
					unlink($to . '_' . $file);
				}
			}
			closedir($reader);
		}
	}

	/**
	 * Copy wizards
	 */
	public function prmediaCopyWizards()
	{
		CopyDirFiles($this->prmInstallWizard, "$this->prmBx/wizards", true, true);
	}

	/**
	 * Remove components
	 */
	public function prmediaRemoveWizards()
	{
		$this->_prmediaRemoveRecursive($this->prmInstallWizard, 'wizards');
	}
	
	private function _prmediaRemoveRecursive($from, $entityName)
	{
		if ($namespaceReader = opendir($from))
		{
			while (false !== ($namespace = readdir($namespaceReader)))
			{
				$f = "$from/$namespace";
				if (in_array($namespace, array('.', '..')) || !is_dir($f))
				{
					continue;
				}
				if ($entityReader = opendir($f))
				{
					while (false !== ($entity = readdir($entityReader)))
					{
						$entityf = "$f/$entity";
						if (in_array($entity, array('.', '..')) || !is_dir($entityf))
						{
							continue;
						}
						DeleteDirFilesEx("bitrix/$entityName/$namespace/$entity");
					}
					closedir($entityReader);
				}
				if (count(glob("$this->prmBx/$entityName/$namespace/*")) == 0)
				{
					DeleteDirFilesEx("bitrix/$entityName/$namespace");
				}
			}
			closedir($namespaceReader);
		}
	}
}