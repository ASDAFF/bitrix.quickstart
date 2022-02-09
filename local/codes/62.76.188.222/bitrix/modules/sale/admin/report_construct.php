<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

if(!CBXFeatures::IsFeatureEnabled('SaleReports'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

	// <editor-fold desc="--------- Server processing ---------">
ClearVars();
$errorMessage = '';
$errAdmMessage = null;
$fCriticalError = false;

// Using report module
if (!CModule::IncludeModule('report'))
{
	$errorMessage .= GetMessage("REPORT_MODULE_NOT_INSTALLED").'<br>';
	$fCriticalError = true;
}

// Using catalog module
if (!CModule::IncludeModule('catalog'))
{
	$errorMessage .= GetMessage("CATALOG_MODULE_NOT_INSTALLED").'<br>';
	$fCriticalError = true;
}

// Using iblock module
if (!CModule::IncludeModule('iblock'))
{
	$errorMessage .= GetMessage("IBLOCK_MODULE_NOT_INSTALLED").'<br>';
	$fCriticalError = true;
}

// If exists $copyID parameter and it more than 0, then will creating a copy of existing report.
$copyID = (int)$_REQUEST['copyID'];
$fCopyMode = ($copyID > 0) ? true : false;

// If exists $ID parameter and it more than 0, then will creating a new report.
$ID = (int)$_REQUEST['ID'];
$fEditMode = ($ID > 0) ? true : false;

// If editing report that exists, then beforehand we gets its parameters.
$arRepParams = array();
if ($fEditMode || $fCopyMode)
{
	if ($fEditMode) $repID = $ID;
	else if ($fCopyMode) $repID = $copyID;

	if (!($arRepParams = Bitrix\Report\Report::getById($repID)->NavNext(false)))
	{
		$errorMessage .= GetMessage("SALE_REPORT_CONSTRUCT_ERROR_EDIT_REPORT_ON_GET_PARAMS").'<br>';
		$fCriticalError = true;
	}
}

CBaseSaleReportHelper::init();

//<editor-fold defaultstate='collapsed' desc="Forming parameters of component report.construct">
$arParams = array(
	'ACTION' => 'create',
	'TITLE' => GetMessage('SALE_REPORT_CONSTRUCT_NEW_REPORT_TAB'),
	'PATH_TO_REPORT_LIST' => '/bitrix/admin/sale_report.php?lang='.LANG,
	'PATH_TO_REPORT_CONSTRUCT' => '/bitrix/admin/report_construct.php?lang='.LANG,
	'PATH_TO_REPORT_VIEW' => '/bitrix/admin/sale_report_view.php?lang='.LANG.'&ID=#report_id#'
);

// check helper selection
$fSelectHelperMode = false;
$rep_owner = '';
if ($rep_owner = $_REQUEST['rep_owner'])
{
	try
	{
		// filter rep_owner value
		$matches = array();
		$rep_owner = substr($rep_owner, 0, 50);
		if (preg_match('/^[A-Z_][A-Z0-9_-]*[A-Z0-9_]$/i', $rep_owner, $matches)) $rep_owner = $matches[0];
		else $rep_owner = '';

		if (!$rep_owner || !in_array($rep_owner, CBaseSaleReportHelper::getOwners()))
			throw new Exception(GetMessage('REPORT_UNKNOWN_ERROR'));

		if (!$fCriticalError)
		{
			// set owner id
			$arParams['OWNER_ID'] = $rep_owner;
			// get helper name
			$arParams['REPORT_HELPER_CLASS'] = CBaseSaleReportHelper::getHelperByOwner($rep_owner);
		}
	}
	catch (Exception $e)
	{
		$errorMessage .= $e->getMessage().'<br>';
		$fCriticalError = true;
	}
}

if ($fEditMode)
{
	$arParams['report'] = $arRepParams;
	$arParams['ACTION'] = 'edit';
	$arParams['TITLE'] = $arRepParams['TITLE'];
	$arParams['REPORT_ID'] = $ID;
	$arParams['REPORT_HELPER_CLASS'] = CBaseSaleReportHelper::getHelperByOwner($arRepParams['OWNER_ID']);
	$rep_owner = $arRepParams['OWNER_ID'];
}

elseif ($fCopyMode)
{
	$arParams['report'] = $arRepParams;
	$arParams['ACTION'] = 'copy';
	$arParams['TITLE'] = $arRepParams['TITLE'];
	$arParams['REPORT_ID'] = $copyID;
	$arParams['REPORT_HELPER_CLASS'] = CBaseSaleReportHelper::getHelperByOwner($arRepParams['OWNER_ID']);
	$rep_owner = $arRepParams['OWNER_ID'];
}

if ($arParams['ACTION'] == 'create' && !$arParams['REPORT_HELPER_CLASS']) $fSelectHelperMode = true;
//</editor-fold>

// <editor-fold defaultstate="collapsed" desc="POST action">
if ($_REQUEST['cancel'])
{
	$url = $fEditMode ? str_replace('#report_id#', $ID, $arParams['PATH_TO_REPORT_VIEW']) : $arParams['PATH_TO_REPORT_LIST'];
	LocalRedirect($url);
}
// </editor-fold>
	// </editor-fold>



// Page header
$rep_title = ($fEditMode) ? GetMessage("SALE_REPORT_EDIT_TITLE") : GetMessage("SALE_REPORT_CONSTRUCT_TITLE");
if (is_set($arParams['TITLE']) && !empty($arParams['TITLE'])) $rep_title .= ' "'.$arParams['TITLE'].'"';
$APPLICATION->SetTitle($rep_title);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");



	// <editor-fold desc="--------- Page output ---------">

if( $errorMessage )
{
	$errAdmMessage = new CAdminMessage(
		array(
			"DETAILS"=>$errorMessage,
			"TYPE"=>"ERROR",
			"MESSAGE"=>(
				($fEditMode) ? GetMessage('SALE_REPORT_CONSTRUCT_ERROR_EDIT_REPORT')
					: GetMessage('SALE_REPORT_CONSTRUCT_ERROR_ADD_REPORT')
			),
			"HTML"=>true
		)
	);
	echo $errAdmMessage->Show();
}

if (!$fCriticalError)
{
		// <editor-fold desc="------------ Form output ------------">
	?>

<?php
	$aMenu = array(
		array(
			"TEXT" => GetMessage("REPORT_RETURN_TO_LIST"),
			"LINK" => $arParams["PATH_TO_REPORT_LIST"],
			"ICON"=>"btn_list",
		)
	);
	$context = new CAdminContextMenu($aMenu);
	$context->Show();
?>

<div class="adm-detail-content-wrap">
	<div class="adm-detail-content">
		<div class="adm-detail-content-item-block">
			<form method="POST" name="task-filter-form" id="task-filter-form" action="<? echo $APPLICATION->GetCurPage() . '?lang=' . LANG; ?>">
			<input type="hidden" name="ID" value="<?=$ID?>" />
			<?php if (!$fSelectHelperMode && $arParams['REPORT_HELPER_CLASS']) : ?>
			<input type="hidden" name="rep_owner" value="<?=$rep_owner?>" />
			<?php endif; ?>

			<style type="text/css">
				table.report-table { width: 100%; }
			</style>

			<table cellspacing="0" class="report-table">
			<tr>
				<td>
					<?php if ($fSelectHelperMode) : ?>
					<span>
						<style type="text/css">
							.reports-title-label {
								color: #92907E;
								font-size: 14px;
								padding: 5px 0 6px 4px;
							}
						</style>
						<span class="reports-title-label"><?=GetMessage('SALE_REPORT_HELPER_SELECTOR_LABEL_TEXT').':'?></span>
						<select id="sale-report-helper-selector" name="rep_owner" class="filter-dropdown">
							<?php foreach (CBaseSaleReportHelper::getOwners() as $ownerId) : ?>
							<?php if (GetMessage('SALE_REPORT_HELPER_NAME_'.$ownerId)) :?>
							<option value="<?=htmlspecialcharsbx($ownerId)?>"><?php echo GetMessage('SALE_REPORT_HELPER_NAME_'.htmlspecialcharsbx($ownerId)); ?></option>
							<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</span>
					<?php endif; // if ($fSelectHelperMode) : ?>
					<?php
					if (!$fSelectHelperMode) {
						$APPLICATION->IncludeComponent(
							'bitrix:report.construct',
							'admin',
							array(
								'REPORT_ID' => $arParams['REPORT_ID'],
								'ACTION' => $arParams['ACTION'],
								'TITLE' => $arParams['TITLE'],
								'PATH_TO_REPORT_LIST' => $arParams['PATH_TO_REPORT_LIST'],
								'PATH_TO_REPORT_CONSTRUCT' => $arParams['PATH_TO_REPORT_CONSTRUCT'],
								'PATH_TO_REPORT_VIEW' => $arParams['PATH_TO_REPORT_VIEW'],
								'REPORT_HELPER_CLASS' => $arParams['REPORT_HELPER_CLASS']
							),
							null,
							array('HIDE_ICONS' => 'Y')
						);
					}
					?>
				</td>
			</tr>

			<?php if (!$fSelectHelperMode) : ?>
			<tr>
				</td>
					<!-- custom filter value control examples -->
					<div id="report-filter-value-control-examples-custom" style="display: none">

						<span name="report-filter-value-control-LID">
							<select class="report-filter-select" name="value">
								<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
								<? foreach(CBaseSaleReportHelper::getSiteList() as $kID => $vSiteName): ?>
								<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($vSiteName)?></option>
								<? endforeach; ?>
							</select>
						</span>

						<span name="report-filter-value-control-STATUS">
							<select class="report-filter-select" name="value">
								<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
								<? foreach(CBaseSaleReportHelper::getStatusList() as $kID => $vStatusName): ?>
								<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($vStatusName)?></option>
								<? endforeach; ?>
							</select>
						</span>

						<span name="report-filter-value-control-PAY_SYSTEM">
							<select class="report-filter-select" name="value">
								<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
								<? foreach(CBaseSaleReportHelper::getPaySystemList() as $kID => $vName): ?>
								<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($vName)?></option>
								<? endforeach; ?>
							</select>
						</span>

						<span name="report-filter-value-control-DELIVERY_ID">
							<select class="report-filter-select" name="value">
								<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
								<? foreach(CBaseSaleReportHelper::getDeliveryList() as $kID => $vName): ?>
								<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($vName)?></option>
								<? endforeach; ?>
							</select>
						</span>

						<span name="report-filter-value-control-Order:BUYER.LID">
							<select class="report-filter-select" name="value">
								<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
								<? foreach(CBaseSaleReportHelper::getSiteList() as $kID => $vSiteName): ?>
								<option value="<?=htmlspecialcharsbx($kID)?>"><?=htmlspecialcharsbx($vSiteName)?></option>
								<? endforeach; ?>
							</select>
						</span>

						<span name="report-filter-value-control-PRODUCT.GoodsSection:PRODUCT.SECT">
							<select class="report-filter-select" name="value">
								<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
								<?php
								$prevCatalog = -1;
								foreach(CBaseSaleReportHelper::getCatalogSections() as $kSectionID => $vSection):
									// Inserting catalogs headers in list of sections of goods.
									if ($vSection['catalog']['ID'] != $prevCatalog)
									{
										echo '<option value="c'.htmlspecialcharsbx($vSection['catalog']['ID']).'"> - '.GetMessage('SALE_REPORT_CONSTRUCT_CATALOG_NAME_LABEL').
											': '.htmlspecialcharsbx($vSection['catalog']['NAME']).'</option>';
									}
									$prevCatalog = $vSection['catalog']['ID'];
								?>
								<option value="<?=htmlspecialcharsbx($kSectionID)?>">&nbsp;<?=htmlspecialcharsbx($vSection['name'])?></option>
								<? endforeach; ?>
							</select>
						</span>

						<span name="report-filter-value-control-IBLOCK.SectionElement:IBLOCK_ELEMENT.IBLOCK_SECTION">
							<select class="report-filter-select" name="value">
								<option value=""><?=GetMessage('REPORT_IGNORE_FILTER_VALUE')?></option>
								<?php
								$prevCatalog = -1;
								foreach(CBaseSaleReportHelper::getCatalogSections() as $kSectionID => $vSection):
									// Inserting catalogs headers in list of sections of goods.
									if ($vSection['catalog']['ID'] != $prevCatalog)
									{
										echo '<option value="c'.htmlspecialcharsbx($vSection['catalog']['ID']).'"> - '.GetMessage('SALE_REPORT_CONSTRUCT_CATALOG_NAME_LABEL').
											': '.htmlspecialcharsbx($vSection['catalog']['NAME']).'</option>';
									}
									$prevCatalog = $vSection['catalog']['ID'];
								?>
								<option value="<?=htmlspecialcharsbx($kSectionID)?>">&nbsp;<?=htmlspecialcharsbx($vSection['name'])?></option>
								<? endforeach; ?>
							</select>
						</span>

						<style type="text/css">
							/* hide compares for User and Group */
							.report-filter-compare-Bitrix\\Main\\User {display: none;}
							.report-filter-compare-Bitrix\\Main\\Group {display: none;}
							.report-filter-compare-BUYER\.UserGroup\:USER\.GROUP {display: none;}
							.report-filter-compare-USER\.UserGroup\:USER\.GROUP {display: none;}
							.report-filter-compare-UserGroup\:USER\.GROUP {display: none;}
							.report-filter-compare-FUSER\.USER\.UserGroup\:USER\.GROUP {display: none;}
							.report-filter-value-control-Basket\:PRODUCT\.FUSER\.USER  {display: none;}
						</style>

						<span name="report-filter-value-control-Bitrix\Main\User" callback="RTFilter_chooseBUYER">
							<a class="report-select-popup-link" caller="true" style="cursor: pointer;"><?=GetMessage('REPORT_CHOOSE')?></a>
							<input type="hidden" name="value" />
						</span>
						<script type="text/javascript">
							var RTFilter_chooseBUYER_LAST_CALLER;
							function RTFilter_chooseBUYER(span)
							{
								var a = BX.findChild(span, {tag:'a'});

								BX.bind(a, 'click', RTFilter_showBUYERSelector);
								BX.bind(a, 'click', function(e){
									RTFilter_chooseBUYER_LAST_CALLER = this;
								});

							}
							function RTFilter_showBUYERSelector()
							{
								BX.Access.Init();
								BX.Access.SetSelected(null);
								BX.Access.ShowForm({callback: RTFilter_chooseBUYERCatch_fromBXAccess});
							}
							function RTFilter_chooseBUYERCatch_fromBXAccess(arSelected)
							{
								if (arSelected.user)
								{
									var user = null;
									for (var i in arSelected.user) { user = arSelected.user[i]; break; }
									if (user)
									{
										user.id = user.id.substr(1);
										RTFilter_chooseBUYERCatch(user);
									}
								}
							}
							function RTFilter_chooseBUYERCatch(user)
							{
								var userContainer = RTFilter_chooseBUYER_LAST_CALLER.parentNode;

								if (parseInt(user.id) > 0)
								{
									BX.findChild(userContainer, {tag:'a'}).innerHTML = user.name;
								}

								BX.addClass(BX.findChild(userContainer, {tag:'a'}), 'report-select-popup-link-active');
								BX.findChild(userContainer, {attr:{name:'value'}}).value = user.id;
								BX.Access.closeWait();
							}
						</script>

						<span name="report-filter-value-control-Bitrix\Main\Group" callback="RTFilter_chooseGroup">
							<a class="report-select-popup-link" caller="true" style="cursor: pointer;"><?=GetMessage('REPORT_CHOOSE')?></a>
							<input type="hidden" name="value" />
						</span>
						<script type="text/javascript">
							var RTFilter_chooseGroup_LAST_CALLER;
							function RTFilter_chooseGroup(span)
							{
								var a = BX.findChild(span, {tag:'a'});

								BX.bind(a, 'click', RTFilter_showGroupSelector);
								BX.bind(a, 'click', function(e){
									RTFilter_chooseGroup_LAST_CALLER = this;
								});

							}
							function RTFilter_showGroupSelector()
							{
								BX.Access.Init();
								BX.Access.SetSelected(null);
								BX.Access.ShowForm({callback: RTFilter_chooseGroupCatch_fromBXAccess});
							}
							function RTFilter_chooseGroupCatch_fromBXAccess(arSelected)
							{
								if (arSelected.group)
								{
									var group = null;
									for (var i in arSelected.group) { group = arSelected.group[i]; break; }
									if (group)
									{
										group.id = group.id.substr(1);
										RTFilter_chooseGroupCatch(group);
									}
								}
							}
							function RTFilter_chooseGroupCatch(group)
							{
								var groupContainer = RTFilter_chooseGroup_LAST_CALLER.parentNode;

								if (parseInt(group.id) > 0)
								{
									BX.findChild(groupContainer, {tag:'a'}).innerHTML = group.name;
								}

								BX.addClass(BX.findChild(groupContainer, {tag:'a'}), 'report-select-popup-link-active');
								BX.findChild(groupContainer, {attr:{name:'value'}}).value = group.id;
								BX.Access.closeWait();
							}
						</script>
					</div>
				</td>
			</tr>
			<?php endif; // if (!$fSelectHelperMode) : ?>
			</table>

			<?
			// <editor-fold defaultstate="collapsed" desc="-- Buttons --">
			?>
			<div id="sale-report-construct-buttons-block">
				<input id="report-save-button" class="adm-btn-save"
						type="submit" name="save"
						value="<?
							if ($fEditMode) echo GetMessage('SALE_REPORT_CONSTRUCT_BUTTON_SAVE_LABEL_ON_EDIT');
							elseif ($fSelectHelperMode) echo GetMessage('SALE_REPORT_CONSTRUCT_BUTTON_SAVE_LABEL_ON_SELECT_HELPER');
							else echo GetMessage('SALE_REPORT_CONSTRUCT_BUTTON_SAVE_LABEL');
						?>"
						title="<?
							if ($fEditMode) echo GetMessage('SALE_REPORT_CONSTRUCT_BUTTON_SAVE_TITLE_ON_EDIT');
							elseif ($fSelectHelperMode) echo GetMessage('SALE_REPORT_CONSTRUCT_BUTTON_SAVE_TITLE_ON_SELECT_HELPER');
							else echo GetMessage('SALE_REPORT_CONSTRUCT_BUTTON_SAVE_TITLE');
						?>" />&nbsp
				<input class="adm-btn"
						type="submit" name="cancel"
						value="<? echo GetMessage('SALE_REPORT_CONSTRUCT_BUTTON_CANCEL_LABEL'); ?>"
						title="<? echo GetMessage('SALE_REPORT_CONSTRUCT_BUTTON_CANCEL_TITLE'); ?>" />
			</div>
			<?
			// </editor-fold>
			?>

			</form>
		</div>
	</div>
	<div class="adm-detail-content-btns adm-detail-content-btns-empty"></div>
</div>
	<?
		// </editor-fold>
}// if (!$fCriticalError)

	// </editor-fold>



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>