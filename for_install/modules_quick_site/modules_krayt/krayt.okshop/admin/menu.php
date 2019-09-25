<?php
/**
 * Created by PhpStorm.
 * User: aleksander
 * Date: 27.02.2017
 * Time: 16:01
 */

IncludeModuleLangFile(__FILE__);

$MODULE_ID = 'okshop';
$MODULE_CODE = 'okshop';
$moduleSort = 10000;

$aMenu = array(
    "parent_menu" => "global_menu_krayt", // �������� � ������ "�������"
    "sort"        => $moduleSort,
    "section"     => $MODULE_ID,             // ��� ������ ����
    "url"         => '/bitrix/admin/k_tp_page.php?lang=' . LANGUAGE_ID,
    "text"        => GetMessage("K_TP_PAGE"),       // ����� ������ ����
    "title"       => GetMessage("K_TP_PAGE"),  // ����� ����������� ���������
);

$aModuleMenu[] = $aMenu;

return $aModuleMenu;