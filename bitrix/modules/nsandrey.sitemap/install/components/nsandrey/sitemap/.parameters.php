<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule('nsandrey.sitemap');

$sitemap = new CSitemapStructure($_SERVER['DOCUMENT_ROOT']);

include_once('tabs/CComponentTabs.php');
$tabs = new CComponentTabs(GetMessage("NSANDREY_SITEMAP_VSE_NASTROYKI"));

$arIBlock = array('-- '.GetMessage("NSANDREY_SITEMAP_NET"));
$rsIBlock = CIBlock::GetList(Array('ID' => 'asc'), Array('ACTIVE' => 'Y'));
while($arr = $rsIBlock->Fetch())
{
	$arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}

$settings = $sitemap->buildSectionSettings($arCurrentValues, $arIBlock);

//Массив групп
$arMGroups = array_merge(array(
	'ACTIVE_SETTINGS' => array('NAME' => GetMessage("NSANDREY_SITEMAP_AKTIVNOSTQ_RAZDELOV")),
	'NAME_SETTINGS' => array('NAME' => GetMessage("NSANDREY_SITEMAP_NAZVANIA_RAZDELOV"))
), $settings['GROUPS']);

//Массив параметров
$arMParams = $settings['PARAMETERS'];

$tabs->addTab('ACTIVE_TAB', GetMessage("NSANDREY_SITEMAP_AKTIVNOSTQ_RAZDELOV"), array('ACTIVE_SETTINGS'));
$tabs->addTab('NAME_TAB', GetMessage("NSANDREY_SITEMAP_NAZVANIA_RAZDELOV"), array('NAME_SETTINGS'));
$tabs->addTab('SETTINGS_TAB', GetMessage("NSANDREY_SITEMAP_NASTROYKA_RAZDELOV"), array_keys($settings['GROUPS']));

$arComponentParameters = array('GROUPS' => $arMGroups, 'PARAMETERS' => $arMParams);

$tabs->init($arComponentParameters);