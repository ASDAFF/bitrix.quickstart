<?
/**
 *
 * ������ ���������� ������� OnlineDengi ��� CMS 1� �������.
 * @copyright ������ OnlineDengi http://www.onlinedengi.ru/ (��� "�����������"), 2010
 *
 */
 
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$arWizardDescription = Array(
        'NAME' => GetMessage('WZ_DESCRIPTION_TITLE'),
        'DESCRIPTION' => GetMessage('WZ_DESCRIPTION_DESCRIPTION'),
        'ICON' => '',
        'COPYRIGHT' => GetMessage('WZ_DESCRIPTION_COPY'),
        'VERSION' => '1.0.0',
        'STEPS' => Array(
                'Start',
                'GetPaymentParamsStep',
                'ReportStep',
                'FinalStep',
                'CancelStep',
        ),
);
