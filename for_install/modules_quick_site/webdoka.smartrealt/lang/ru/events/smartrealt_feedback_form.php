<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

UET ( "SMARTREALT_FEEDBACK_FORM", "��������� ������ �� ������", $lid, "
    #NAME# - ���   ������  
    #EMAIL# - Email ������   
    #PHONE# - ������� ������            
    #MESSAGE# - ���������
    #OBJECT_NUMBER# - ����� �������    
    #OBJECT_SECTION_NAME# - �������� �������   
    #OBJECT_ADDRESS# - �����   
    #OBJECT_PRICE# - ����
       
    #DEFAULT_EMAIL_FROM# - E-Mail ����� �� ��������� (��������������� � ����������)
    #SITE_NAME# - �������� ����� (��������������� � ����������)
    #SERVER_NAME# - URL ������� (��������������� � ����������) 
");
 
$arr ["EVENT_NAME"] = "SMARTREALT_FEEDBACK_FORM";
$arr ["SITE_ID"] = $arSites;
$arr ["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
$arr ["EMAIL_TO"] = "#DEFAULT_EMAIL_FROM#";
$arr ["BCC"] = "";
$arr ["SUBJECT"] = "#SITE_NAME#: ��������� ������ �� ������";
$arr ["BODY_TYPE"] = "text";
$arr ["MESSAGE"] = "   
�������������� ��������� ����� #SITE_NAME#
------------------------------------------

��� ���� ���������� ��������� ����� ����� �������� �����

�����: #NAME#
E-mail ������: #EMAIL#
������� ������: #PHONE#
����� �������: #OBJECT_NUMBER#
������: #OBJECT_SECTION_NAME#, #OBJECT_ADDRESS#
����: #OBJECT_PRICE#

����� ���������:
#MESSAGE#

��������� ������������� �������������.
";

$arTemplates [] = $arr;
?>