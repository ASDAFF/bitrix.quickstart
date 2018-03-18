<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$current_view = $arParams['DEFAULT_VIEW'];

if (isset($_REQUEST['current_view']))
	$current_view = $_REQUEST['current_view'] == 'list' ? 'list' : 'table';

$current_filter = $_REQUEST['current_filter'] == 'adv' ? 'adv' : 'simple';

$arParams['CURRENT_VIEW'] = $current_view;
$arParams['CURRENT_FILTER'] = $current_filter;
?>

<!--Здесь управление отображением фильтра-->
<script>
var current_filter = '<?=CUtil::JSEscape($current_filter)?>';
var arFilters = ['simple', 'adv'];
function BXSetFilter(new_current_filter)
{
	if (current_filter == new_current_filter)
		return;
	
	for (var i = 0; i < arFilters.length; i++)
	{
		var obTabContent = document.getElementById('bx_users_filter_' + arFilters[i]);
		var obTab = document.getElementById('bx_users_selector_tab_' + arFilters[i]);
		
		if (null != obTabContent)
		{
			obTabContent.style.display = new_current_filter == arFilters[i] ? 'block' : 'none';
			current_filter = new_current_filter;
		}
		
		if (null != obTab)
		{
			obTab.className = new_current_filter == arFilters[i] ? 'bx-selected' : '';
		}
	}
}
</script>

<h1><?=GetMessage('FILTER')?></h1>

<div style="float:left">
	<ul class="bx-users-selector">
		<li id="bx_users_selector_tab_simple"<?=$current_filter == 'adv' ? '' : ' class="bx-selected"'?> onclick="BXSetFilter('simple')"><?=GetMessage('INTR_COMP_IS_TPL_FILTER_SIMPLE')?></li>
		<li id="bx_users_selector_tab_adv"<?=$current_filter == 'adv' ? ' class="bx-selected"' : ''?> onclick="BXSetFilter('adv')"><?=GetMessage('INTR_COMP_IS_TPL_FILTER_ADV')?></li>
	</ul>

	<div class="bx-users-selector-filter" id="bx_users_filter_simple"<?=$current_filter == 'adv' ? ' style="display: none;"' : ''?>>
		<?$arFilterValues = $APPLICATION->IncludeComponent("rarus.sms4b:intranet.structure.selector", "simple", $arParams, $component);?>
	</div>


	<div class="bx-users-selector-filter" id="bx_users_filter_adv"<?=$current_filter == 'adv' ? '' : ' style="display: none;"'?>>
		<?$arFilterValues = $APPLICATION->IncludeComponent("rarus.sms4b:intranet.structure.selector", "advanced", $arParams, $component);?>
	</div>
</div>

<script>
function BXToggleAlphabet()
{
	var obAlph = document.getElementById('bx_alph');
	if (null != obAlph)
	{
		obAlph.style.visibility = obAlph.style.visibility == 'hidden' ? 'visible' : 'hidden';
	}
}
</script>

<div style = "float:left">
	<div>
		<?$APPLICATION->IncludeComponent("rarus.sms4b:intranet.structure.selector", 'alphabet', $arParams, $component);?>
	</div>
</div>

<div style="clear: both;"></div>

<?
if ($current_view == 'list' && $arParams['LIST_VIEW'] == 'group')
{
	$arParams['SHOW_NAV_TOP'] = 'N';
	$arParams['SHOW_NAV_BOTTOM'] = 'N';
}

$arParams['USER_PROPERTY'] = 
	$current_view == 'list' 
	? (
		$arParams['LIST_VIEW'] == 'group' 
		? $arParams['USER_PROPERTY_GROUP'] 
		: $arParams['USER_PROPERTY_LIST']
	) 
	: $arParams['USER_PROPERTY_TABLE'];
?>

<?
$APPLICATION->IncludeComponent("rarus.sms4b:employee.list", '', $arParams, $component, array('HIDE_ICONS' => 'Y'));
?>