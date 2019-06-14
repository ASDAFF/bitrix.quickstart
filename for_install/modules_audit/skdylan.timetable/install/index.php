<?php

use Bitrix\Main\Localization\Loc; //������ � ��������� �������
use Bitrix\Main\ModuleManager; //
use Bitrix\Main\Config\Option; //����� ��� ������ � ����������� �������, �������� � ���� ������.
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class skdylan_timetable extends CModule{

    var $MODULE_ID = "skdylan.timetable";

    public function __construct(){
        if(file_exists(__DIR__."/version.php")){

            $arModuleVersion = array();

            include_once(__DIR__."/version.php");

            $this->MODULE_ID            = str_replace("_",".", get_class($this));
            $this->MODULE_VERSION       = "1.0.0";
            $this->MODULE_VERSION_DATE  = "2018-07-09 00:00:00";
            $this->MODULE_NAME          = Loc::getMessage("TIMETABLE_NAME");
            $this->MODULE_DESCRIPTION   = Loc::getMessage("TIMETABLE_DESCRIPTION");
            $this->PARTNER_NAME          = Loc::getMessage("TIMETABLE_PARTNER_NAME");
            $this->PARTNER_URI           = Loc::getMessage("TIMETABLE_PARTNER_URI");
        }
        return false;
    }

    public function DoInstall()
    {
        global $APPLICATION;

        if(CheckVersion(ModuleManager::getVersion("main"), "14.00.00")){

            if($this->CheckTypeIB() == true) {

                ModuleManager::registerModule($this->MODULE_ID);

                $res = $this->AddBlockType();
                if ($res != true) {
                    $APPLICATION->ThrowException($res);
                    return false;
                }
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/skdylan.timetable/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/skdylan.timetable/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/", true, true);
                return true;
            }
        }else{
            $APPLICATION->ThrowException(
                Loc::getMessage("TIMETABLE_INSTALL_ERROR_VERSION")
            );
        }
        return false;

    }

    function CheckTypeIB(){
        if (CModule::IncludeModule('iblock')){
            global $APPLICATION;
            $res = CIBlockType::GetList(false, array("ID" => "participant"))->Fetch();

            if(is_array($res)) {
                $APPLICATION->ThrowException(Loc::getMessage("TIMETABLE_ERROR_PARTICIPANT"));
                return false;
            }
            $res = CIBlockType::GetList(false, array("ID" => "timetable"))->Fetch();
            if(is_array($res)) {
                $APPLICATION->ThrowException(Loc::getMessage("TIMETABLE_ERROR_TIMETABLE"));
                return false;
            }
            return true;
        }
    }

    public function AddParticipantGroup()
    {
        $group_name = GetMessage("SKDYLAN_TIMETABLE_UCASTNIKI");
        if (CModule::IncludeModule('iblock')) {
            $iB = new CIBlock;

            $IBLOCK_TYPE = "participant"; // ��� ���������
            $SITE_ID = "s1"; // ID �����

            $arFields = Array(
                "ACTIVE" => "Y",
                "NAME" => $group_name,
                //"CODE" => "catalog",
                "IBLOCK_TYPE_ID" => $IBLOCK_TYPE,
                "SITE_ID" => $SITE_ID,
                "SORT" => "5",
                //"GROUP_ID" => $arAccess, // ����� �������
                "FIELDS" => array(
                    "DETAIL_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "PREVIEW_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "SECTION_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "DETAIL_TEXT_TYPE" => array(      // ��� ���������� ��������
                        "DEFAULT_VALUE" => "html",
                    ),
                    "SECTION_DESCRIPTION_TYPE" => array(
                        "DEFAULT_VALUE" => "html",
                    ),
                    "IBLOCK_SECTION" => array(         // �������� � �������� ������������
                        "IS_REQUIRED" => "N",
                    ),

                ),
                // ������� �������
                "LIST_PAGE_URL" => "",
                "SECTION_PAGE_URL" => "",
                "DETAIL_PAGE_URL" => "",
                "INDEX_SECTION" => "N", // ������������� ������� ��� ������ ������
                "INDEX_ELEMENT" => "N", // ������������� �������� ��� ������ ������

                "VERSION" => 1, // �������� ��������� � ����� �������

                "ELEMENT_NAME" => GetMessage("SKDYLAN_TIMETABLE_UCASTNIKA"),
                "ELEMENTS_NAME" => GetMessage("SKDYLAN_TIMETABLE_UCASTNIKI"),
                "ELEMENT_ADD" => GetMessage("SKDYLAN_TIMETABLE_DOBAVITQ_UCASTNIKA"),
                "ELEMENT_EDIT" => GetMessage("SKDYLAN_TIMETABLE_IZMENITQ_UCASTNIKA"),
                "ELEMENT_DELETE" => GetMessage("SKDYLAN_TIMETABLE_UDALITQ_UCASTNIKA"),
                "SECTION_NAME" => GetMessage("SKDYLAN_TIMETABLE_GRUPPA"),
                "SECTIONS_NAME" => GetMessage("SKDYLAN_TIMETABLE_GRUPPY"),
                "SECTION_ADD" => GetMessage("SKDYLAN_TIMETABLE_DOBAVITQ_GRUPPU"),
                "SECTION_EDIT" => GetMessage("SKDYLAN_TIMETABLE_IZMENITQ_GRUPPU"),
                "SECTION_DELETE" => GetMessage("SKDYLAN_TIMETABLE_UDALITQ_GRUPPU"),
            );

            $ID = $iB->Add($arFields);
            if($ID == false )
                return false;

            // ���������� �������

            $ibp = new CIBlockProperty;

            $arFields = Array(
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_NOMER_TELEFONA"),
                    "ACTIVE" => "Y",
                    "CODE" => "phone_number",
                    "PROPERTY_TYPE" => "S",
                    "FILTRABLE" => "Y",
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "N"
                ),
                Array(
                    "NAME" => "E-mail",
                    "ACTIVE" => "Y",
                    "CODE" => "email",
                    "PROPERTY_TYPE" => "S",
                    "FILTRABLE" => "Y",
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "N"
                ),
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_KOMMENTARIY"),
                    "ACTIVE" => "Y",
                    "CODE" => "comment",
                    "PROPERTY_TYPE" => "S",
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "N"
                ),
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_SOBYTIE"),
                    "ACTIVE" => "Y",
                    "CODE" => "event",
                    "PROPERTY_TYPE" => "E",
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "Y"
                ),
            );

            foreach ($arFields as $item)
            {
                $propId = $ibp->Add($item);
                if($propId == false )
                    return false;
            }

            return true;

        }
    }

    function AddBlockType()
    {
        if (CModule::IncludeModule('iblock')) {

            $obIBlockType = new CIBlockType;
            $arFields = Array(
                Array(
                    "ID" => "timetable",
                    "SECTIONS" => "Y",
                    'IN_RSS'=>'Y',
                    "LANG" => Array(
                        "ru" => Array(
                            "NAME" => GetMessage("SKDYLAN_TIMETABLE_RASPISANIA"),
                        )
                    )
                ),
                Array(
                    "ID" => "participant",
                    "SECTIONS" => "Y",
                    'IN_RSS'=>'N',
                    "LANG" => Array(
                        "ru" => Array(
                            "NAME" => GetMessage("SKDYLAN_TIMETABLE_UCASTNIKI"),
                        )
                    )
                )
            );

            foreach ($arFields as $item)
            {
                $res = $obIBlockType->Add($item);
                if(!$res)
                {
                    $error = $obIBlockType->LAST_ERROR;
                    return $error;
                }
            }


            $res = $this->AddParticipantGroup();
            if(!$res)
            {
                $error = $obIBlockType->LAST_ERROR;
                return $error;
            }

            return true;

        }
    }



    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__."/assets/scripts",
            Application::getDocumentRoot()."/bitrix/js/".$this->MODULE_ID."/",
            true,
            true
        );

        CopyDirFiles(
            __DIR__."/assets/styles",
            Application::getDocumentRoot()."/bitrix/css/".$this->MODULE_ID."/",
            true,
            true
        );

        return false;
    }


    public function DoUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
        $this->DeleteBlockType();

        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/skdylan.timetable/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);

        DeleteDirFilesEx("/bitrix/components/skdylan.timetable");

        return true;
    }

    function DeleteBlockType()
    {   if (CModule::IncludeModule('iblock')) {
            $id = array("timetable", "participant");
            foreach ($id as $item) {
                CIBlockType::Delete($item);
            }
        }
    }


}



?>