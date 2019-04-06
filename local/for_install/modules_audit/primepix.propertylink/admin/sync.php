<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

if(!$GLOBALS['USER']->IsAdmin()) {
	return;
}

IncludeModuleLangFile(__FILE__);

$PXPL_MODULE_ID = 'primepix.propertylink';

CModule::IncludeModule($PXPL_MODULE_ID);
CModule::IncludeModule('iblock');

// handle POST request
$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

$exepts = new Primepix\CPxGetExepts;

//$exeptions = $exepts->getExpList();

 if ($REQUEST_METHOD === 'POST' && check_bitrix_sessid()) {
$exepts->removeAll();
 	foreach ($_POST['iblocks'] as $key => $val) {

		$exeptionsAll[ $key ][0] = $_POST['iblocks'][ $key ];
		$exeptionsAll[ $key ][1] = $_POST['props'][ $key ];

		$empty   = empty($exeptionsAll[ $key ][0]) && empty($exeptionsAll[ $key ][1]);

		if (!$empty) {

			$exepts->addExept($exeptionsAll[ $key ][0], $exeptionsAll[ $key ][1]);
		}
		
 	}
}

$exeptions = $exepts->getExpList();

// get iblocks
$arIblocks = array();
$dbIblocks = CIBlock::GetList(array('sort' => 'asc'), array('ACTIVE' => 'Y'));

while ($arIblock = $dbIblocks->Fetch()) {

	$ibid = $arIblock['ID'];
	$arFilter  = array('IBLOCK_ID' => $ibid, 'ACTIVE' => 'Y');

	$dbIBlockProperties = CIBlockProperty::GetList(
			array(),
			$arFilter
		);

	if (!$dbIBlockProperties->Fetch()) {
		continue;
	}

	$dbIBlockSections = CIBlockSection::GetList(
			array(),
			$arFilter
		);

	if (!$dbIBlockSections->Fetch()) {
		continue;
	}


	$dbIBlockElements = CIBlockElement::GetList(
			array(),
			$arFilter
		);


	if (!$dbIBlockElements->Fetch()) {
		continue;
	}

	$arIblocks[$ibid] = $arIblock;
}


// init scripts
CJSCore::RegisterExt(
	'pxpl_ext',
	array(
		'js'   => sprintf('/bitrix/js/%s/scripts.js', $PXPL_MODULE_ID),
		'css' => sprintf('/bitrix/panel/%s/styles.css', $PXPL_MODULE_ID),
		'lang' => sprintf('/bitrix/modules/%s/lang/%s/js.php', $PXPL_MODULE_ID, LANGUAGE_ID),
		'rel'  => array('jquery')
	)
);
CJSCore::Init('pxpl_ext');


$aTabs = array(
	array(
		"DIV"   => "step0",
		"TAB"   => GetMessage($PXPL_MODULE_ID . '_NAME_SYNC_TAB'),
		"ICON"  => "",
		"TITLE" => GetMessage($PXPL_MODULE_ID . '_TITLE_SYNC_TAB'),
		"ONSELECT" => "app.PropertyLink.events.showButton('step0')"
	),
	array(
		"DIV"   => "step1",
		"TAB"   => GetMessage($PXPL_MODULE_ID . '_NAME_PARAM_TAB'),
		"ICON"  => "",
		"TITLE" => GetMessage($PXPL_MODULE_ID . '_NAME_PARAM_TAB'),
		"ONSELECT" => "app.PropertyLink.events.showButton('step1');"
	)
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, TRUE, TRUE);

$APPLICATION->SetTitle( GetMessage($PXPL_MODULE_ID . '_NAME_SYNC_TAB') );
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
?>

<form method="post" class="settings-form -settings-form"
	action="<?=$APPLICATION->GetCurPage()?>">

<div class="pxpl-wrap">
	<? $tabControl->Begin() ?>
	<? $tabControl->BeginNextTab() ?>
		<tr><td colspan="2">
			<?= BeginNote() ?>
				<?= GetMessage($PXPL_MODULE_ID . '_LINK_INSTRUCTION') ?>
			<?= EndNote() ?>
		</td></tr>
		<tr>
			<td width="40%" class="adm-detail-content-cell-l">
				<?= ShowJsHint(GetMessage($PXPL_MODULE_ID . '_IBLOCK_HINT')) ?>
				<?= GetMessage($PXPL_MODULE_ID . '_IBLOCK_SELECT') ?>
			</td>
			<td width="60%" class="adm-detail-content-cell-r">	
				<select class="-iblock-select" name="id">
					<? if (!empty($arIblocks)): ?>
						<? foreach ($arIblocks as $key => $arBlock): ?>
							<option data="[<?= $arBlock['ID'] ?>] <?= $arBlock['NAME'] ?>" value="<?=$arBlock["ID"]?>">
								[<?= $arBlock['ID'] ?>] <?= $arBlock['NAME'] ?>
							</option>
						<? endforeach ?>
					<? endif; ?>
				</select>
				<a type="button" class="-openStat open-stat"><?= GetMessage($PXPL_MODULE_ID . '_STAT') ?></a>
			</td>
		</tr>

		<tr>
			<td width="40%" class="adm-detail-content-cell-l">
				<label><?= GetMessage($PXPL_MODULE_ID . '_SAVE_EXIST_LINKS') ?></label>
			</td>
			<td width="60%" class="adm-detail-content-cell-r">	
				<input type="checkbox" name="save_exist_links" class="-save-links" checked="checked" value="Y" />
			</td>
		</tr>

    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <label><?= GetMessage($PXPL_MODULE_ID . '_ERASE_FROM_ROOT') ?></label>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="checkbox" name="erase_from_root" class="-erase-links" checked="checked" value="Y" />
        </td>
    </tr>
	<? $tabControl->BeginNextTab() ?>
	

	<tr>
		<td>
			<label for="exeptions">
				<?= ShowJsHint(GetMessage($PXPL_MODULE_ID . '_EXEPTIONS_HINT')) ?>
				<?= GetMessage($PXPL_MODULE_ID . '_EXEPTIONS') ?>
			</label>
		</td>
	</tr>

		<?foreach ($exeptions as $key => $val):?>
		<tr>
			<td>
				<input
					type="text"
					id="exeptions"
					name="iblocks[<?=$key?>]"
					placeholder="<?= GetMessage($PXPL_MODULE_ID . '_PROPERTY') ?>"
					value="<?=$exeptions[$key][0]?>">
				<input
					type="text"
					id="exeptions"
					name="props[<?=$key?>]"
					placeholder="<?= GetMessage($PXPL_MODULE_ID . '_SECTION') ?>"
					value="<?=$exeptions[$key][1]?>">
			</td>
		</tr>
		<?endforeach?>

		<tr>
			<td>
				<input type="hidden" class="-last-id" value="<?=++$key?>">
				<input
						type="text"
						id="exeptions"
						placeholder="<?= GetMessage($PXPL_MODULE_ID . '_PROPERTY') ?>"
						name="iblocks[<?=$key?>]">
					<input
						type="text"
						id="exeptions"
						placeholder="<?= GetMessage($PXPL_MODULE_ID . '_SECTION') ?>"
						name="props[<?=$key?>]">
			</td>
		</tr>
	

	<tr class="-insert">

		<td><br>
			<input type="button" value="<?= GetMessage($PXPL_MODULE_ID . '_ADD_EXT') ?>" class="-add-ext">
		</td>
	</tr>
	<tr>
		<td><?=bitrix_sessid_post();?></td>
	</tr>
	<? $tabControl->Buttons() ?>
		<div class="-tab-btns" data-tab-id="step0">
			<input type="submit" class="adm-btn-save -link-properties" 
				   value="<?=GetMessage($PXPL_MODULE_ID . "_SUBMIT")?>" 
				   title="<?=GetMessage($PXPL_MODULE_ID . "_SUBMIT")?>">
			<input type="button" class="adm-btn-clear -clear"
					value="<?=GetMessage($PXPL_MODULE_ID . "_CLEAR")?>" 
					title="<?=GetMessage($PXPL_MODULE_ID . "_CLEAR")?>">
			<span class="-result-message result"></span>
		</div>
		<div class="-tab-btns ppx-hdn" data-tab-id="step1">
			<input type="button" 
				value="<?=GetMessage($PXPL_MODULE_ID . "_SAVE")?>" 
				title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" 
				class="adm-btn-save -save-settings">
		</div>
	<? $tabControl->End() ?>

</div>
</form>

<script>
	(function(w) {
		var app = w.app || {};

		app.PropertyLink.init();

	})(window);
</script>


<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>