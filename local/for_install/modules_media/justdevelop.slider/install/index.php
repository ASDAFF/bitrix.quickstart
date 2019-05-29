<?
IncludeModuleLangFile(__FILE__);
use Bitrix\Highloadblock as HL;
Class justdevelop_slider extends CModule
{
	const MODULE_ID = 'justdevelop.slider';
	var $MODULE_ID = 'justdevelop.slider'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $HLBLOCK_NAME = 'jdslide';
    var $ENTITY_ID = 'HLBLOCK_%s';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("justdevelop.slider_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("justdevelop.slider_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("justdevelop.slider_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("justdevelop.slider_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CJustdevelopSlider', 'OnBuildGlobalMenu');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CJustdevelopSlider', 'OnBuildGlobalMenu');
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		//$this->InstallDB();
		 $this->installHighloadblock();
		RegisterModule(self::MODULE_ID);
	}
 private function addCustomFieldsInHlblock($hlblockId, $fieldsExcluded = array())
    {
        $customFields = array(
            'UF_FILE' => array(
                'FIELD_NAME' => 'UF_FILE',
				'USER_TYPE_ID' => 'file',
				'XML_ID' => 'UF_JD_FILE',
				'SORT' => '100',
				'MULTIPLE' => 'N',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'N',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'Y',
                'EDIT_FORM_LABEL'   => array(
                    'RU' => GetMessage('JD_CUSTOM_FIELD_NAME_RU_UF_FILE'),
                    'EN' => GetMessage('JD_CUSTOM_FIELD_NAME_EN_UF_FILE'),
                ),
            ),
			'UF_LINK' => array(
                'FIELD_NAME' => 'UF_LINK',
				'USER_TYPE_ID' => 'string',
				'XML_ID' => 'UF_JD_LINK',
				'SORT' => '200',
				'MULTIPLE' => 'N',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'N',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'Y',
                'EDIT_FORM_LABEL'   => array(
                    'RU' => GetMessage('JD_CUSTOM_FIELD_NAME_RU_UF_LINK'),
                    'EN' => GetMessage('JD_CUSTOM_FIELD_NAME_EN_UF_LINK'),
                ),
            ),
			'UF_HEAD' => array(
                'FIELD_NAME' => 'UF_HEAD',
				'USER_TYPE_ID' => 'string',
				'XML_ID' => 'UF_JD_HEAD',
				'SORT' => '300',
				'MULTIPLE' => 'N',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'N',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'Y',
                'EDIT_FORM_LABEL'   => array(
                    'RU' => GetMessage('JD_CUSTOM_FIELD_NAME_RU_UF_HEAD'),
                    'EN' => GetMessage('JD_CUSTOM_FIELD_NAME_EN_UF_HEAD'),
                ),
            ),
            'UF_DESC' => array(
                'FIELD_NAME' => 'UF_DESC',
				'USER_TYPE_ID' => 'string',
				'XML_ID' => 'UF_JD_DESC',
				'SORT' => '400',
				'MULTIPLE' => 'N',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'N',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'Y',
                'EDIT_FORM_LABEL'   => array(
                    'RU' => GetMessage('JD_CUSTOM_FIELD_NAME_RU_UF_DESC'),
                    'EN' => GetMessage('JD_CUSTOM_FIELD_NAME_EN_UF_DESC'),
                ),
            ),
            'UF_BTNAME' => array(
                'FIELD_NAME' => 'UF_BTNAME',
				'USER_TYPE_ID' => 'string',
				'XML_ID' => 'UF_BTNAME',
				'SORT' => '500',
				'MULTIPLE' => 'N',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'N',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'Y',
                'EDIT_FORM_LABEL'   => array(
                    'RU' => GetMessage('JD_CUSTOM_FIELD_NAME_RU_UF_BTNAME'),
                    'EN' => GetMessage('JD_CUSTOM_FIELD_NAME_EN_UF_BTNAME'),
                ),
            ),
            'UF_SORT' => array(
                'FIELD_NAME' => 'UF_SORT',
				'USER_TYPE_ID' => 'string',
				'XML_ID' => 'UF_JD_SORT',
				'SORT' => '600',
				'MULTIPLE' => 'N',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'N',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
                'EDIT_FORM_LABEL'   => array(
                    'RU' => GetMessage('JD_CUSTOM_FIELD_NAME_RU_UF_SORT'),
                    'EN' => GetMessage('JD_CUSTOM_FIELD_NAME_EN_UF_SORT'),
                ),
            ),
        );

        $userTypeEntity    = new CUserTypeEntity();

        foreach ($customFields as $fieldName => $customField)
        {
            if (count($fieldsExcluded) == 0 || in_array($fieldName, $fieldsExcluded))
            {
                $dataUserFields = array(
                    'ENTITY_ID' => sprintf($this->ENTITY_ID, $hlblockId),
                    'FIELD_NAME' => $customField['FIELD_NAME'],
                    'USER_TYPE_ID' => $customField['USER_TYPE_ID'],
                    'MANDATORY' => 'N',
                    'SHOW_FILTER' => 'N',
                    'IS_SEARCHABLE' => 'N',

                    'EDIT_FORM_LABEL'   => array(
                        'ru'    => $customField['EDIT_FORM_LABEL']['RU'],
                        'en'    => $customField['EDIT_FORM_LABEL']['EN'],
                    ),

                    'LIST_COLUMN_LABEL' => array(
                        'ru'    => $customField['EDIT_FORM_LABEL']['RU'],
                        'en'    => $customField['EDIT_FORM_LABEL']['EN'],
                    ),

                    'LIST_FILTER_LABEL' => array(
                        'ru'    => $customField['EDIT_FORM_LABEL']['RU'],
                        'en'    => $customField['EDIT_FORM_LABEL']['EN'],
                    ),
                );

                $userTypeEntity->Add($dataUserFields);
            }
        }
    }

    /**
     * Добавляет HLBlock для хранения количества голосов пользователей за элементы
     */
    private function installHighloadblock()
    {
        if (CModule::IncludeModule('highloadblock'))
        {
            $selectedResultDB = HL\HighloadBlockTable::getList(
                array(
                    'filter' => array(
                        'TABLE_NAME' => $this->HLBLOCK_NAME
                    ),
                    'select' => array(
                        'ID'
                    ),
                ));
            $hlblock = $selectedResultDB->fetch();

            if (empty($hlblock['ID']))
            {
                /**
                 * Если HLBlock не найден, то создаем новый
                 */
                $hlblockData = array(
                    'NAME' => ucfirst($this->HLBLOCK_NAME),
                    'TABLE_NAME' => $this->HLBLOCK_NAME
                );

                $result = HL\HighloadBlockTable::add($hlblockData);

                if ($result->isSuccess())
                {
                    /**
                     * Добавляем пользовательские поля в HLBlock
                     */
                    $this->addCustomFieldsInHlblock($result->getId());
                }
                else
                {
                    global $APPLICATION;
                    $APPLICATION->ThrowException(GetMessage('JD_ERROR_UNABLE_ADD_HIGHLOADBLOCK'));
                }
            }
            else
            {
                /**
                 * Если HLBlock найден, то проверяем наличие пользовательских полей и добавляем недостающие
                 */
                $userTypeEntity = array('UF_FILE', 'UF_LINK', 'UF_HEAD', 'UF_DESC', 'UF_BTNAME', 'UF_SORT');

                $selectedResultUserTypeDB = CUserTypeEntity::GetList(
                    array(),
                    array(
                        'ENTITY_ID' => sprintf($this->ENTITY_ID, $hlblock['ID'])
                    ));
                while($field = $selectedResultUserTypeDB->Fetch())
                {
                    if (in_array($field['FIELD_NAME'], $userTypeEntity))
                    {
                        $keys = array_keys($userTypeEntity, $field['FIELD_NAME']);
                        unset($userTypeEntity[$keys[0]]);
                    }
                }

                if (count($userTypeEntity) > 0)
                {
                    /**
                    * Добавляем пользовательские поля в HLBlock
                    */
                   $this->addCustomFieldsInHlblock($hlblock['ID'], $userTypeEntity);
                }
            }
        }
    }

    private function unInstallHlblock($saveHlblock)
    {
        if ($saveHlblock && CModule::IncludeModule('highloadblock'))
        {
            $selectedResultDB = HL\HighloadBlockTable::getList(
                array(
                    'filter' => array(
                        'TABLE_NAME' => $this->HLBLOCK_NAME
                    ),
                    'select' => array(
                        'ID'
                    ),
                ));

            $hlblock = $selectedResultDB->fetch();

            if (! empty($hlblock['ID']))
            {
                HL\HighloadBlockTable::delete($hlblock['ID']);
            }
        }
    }

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		//$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->unInstallHlblock('Y');
	}
}
?>
