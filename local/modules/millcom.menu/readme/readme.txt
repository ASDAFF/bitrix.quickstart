��������
������ ���� �� ��������� � �������� ���������.

������� ��������� ��������� ����������� ���� �� �������� � ��������� ���������, ��������� ����� ����������.

����� ��������������, ��������, ��� ����������� � ���� �������� ����� � ����� �����, ��������� �������� � � ����������� � �.�. 

������������ ��� ������ ������������ ���������� bitrix:menu.sections

������� ������ �������������:
1) � ����� �������� ���� .left.menu_ext.php
2) ���������� � ��� ���:
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent(
    "millcom:menu",
    "",
    Array(
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "DEPTH_LEVEL" => "1",
        "IBLOCK_ID" => "1",
        "IBLOCK_TYPE" => "info",
        "SORT" => "Y",
    )
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>
3) �� ��������� �������� ���� ���������� ��������� ����������� ��������� ���� � ������� ������� �� ����������� menu_ext.php
���. 