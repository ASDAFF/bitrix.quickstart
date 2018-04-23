<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

if(!$GLOBALS['USER']->IsAdmin()) {
	return;
}

IncludeModuleLangFile(__FILE__);

$APL_MODULE_ID = 'asdaff.proplink';

CModule::IncludeModule($APL_MODULE_ID);
CModule::IncludeModule('iblock');

// handle POST request
$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

$exepts = new ASDAFF\CGetExepts;

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
	'apl_ext',
	array(
		'js'   => sprintf('/bitrix/js/%s/scripts.js', $APL_MODULE_ID),
		'css' => sprintf('/bitrix/panel/%s/styles.css', $APL_MODULE_ID),
		'lang' => sprintf('/bitrix/modules/%s/lang/%s/js.php', $APL_MODULE_ID, LANGUAGE_ID),
		'rel'  => array('jquery')
	)
);
CJSCore::Init('apl_ext');


$aTabs = array(
	array(
		"DIV"   => "step0",
		"TAB"   => GetMessage($APL_MODULE_ID . '_NAME_SYNC_TAB'),
		"ICON"  => "",
		"TITLE" => GetMessage($APL_MODULE_ID . '_TITLE_SYNC_TAB'),
		"ONSELECT" => "app.PropLink.events.showButton('step0')"
	),
	array(
		"DIV"   => "step1",
		"TAB"   => GetMessage($APL_MODULE_ID . '_NAME_PARAM_TAB'),
		"ICON"  => "",
		"TITLE" => GetMessage($APL_MODULE_ID . '_NAME_PARAM_TAB'),
		"ONSELECT" => "app.PropLink.events.showButton('step1');"
	)
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, TRUE, TRUE);

$APPLICATION->SetTitle( GetMessage($APL_MODULE_ID . '_NAME_SYNC_TAB') );
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
?>

<form method="post" class="settings-form -settings-form"
	action="<?=$APPLICATION->GetCurPage()?>">

<div class="pxpl-wrap">
	<? $tabControl->Begin() ?>
	<? $tabControl->BeginNextTab() ?>
		<tr><td colspan="2">
			<?= BeginNote() ?>
				<?= GetMessage($APL_MODULE_ID . '_LINK_INSTRUCTION') ?>
			<?= EndNote() ?>
		</td></tr>
		<tr>
			<td width="40%" class="adm-detail-content-cell-l">
				<?= ShowJsHint(GetMessage($APL_MODULE_ID . '_IBLOCK_HINT')) ?>
				<?= GetMessage($APL_MODULE_ID . '_IBLOCK_SELECT') ?>
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
				<a type="button" class="-openStat open-stat"><?= GetMessage($APL_MODULE_ID . '_STAT') ?></a>
			</td>
		</tr>

		<tr>
			<td width="40%" class="adm-detail-content-cell-l">
				<label><?= GetMessage($APL_MODULE_ID . '_SAVE_EXIST_LINKS') ?></label>
			</td>
			<td width="60%" class="adm-detail-content-cell-r">
				<input type="checkbox" name="save_exist_links" class="-save-links" checked="checked" value="Y" />
			</td>
		</tr>

    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <label><?= GetMessage($APL_MODULE_ID . '_ERASE_FROM_ROOT') ?></label>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="checkbox" name="erase_from_root" class="-erase-links" checked="checked" value="Y" />
        </td>
    </tr>
	<? $tabControl->BeginNextTab() ?>


	<tr>
		<td>
			<label for="exeptions">
				<?= ShowJsHint(GetMessage($APL_MODULE_ID . '_EXEPTIONS_HINT')) ?>
				<?= GetMessage($APL_MODULE_ID . '_EXEPTIONS') ?>
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
					placeholder="<?= GetMessage($APL_MODULE_ID . '_PROPERTY') ?>"
					value="<?=$exeptions[$key][0]?>">
				<input
					type="text"
					id="exeptions"
					name="props[<?=$key?>]"
					placeholder="<?= GetMessage($APL_MODULE_ID . '_SECTION') ?>"
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
						placeholder="<?= GetMessage($APL_MODULE_ID . '_PROPERTY') ?>"
						name="iblocks[<?=$key?>]">
					<input
						type="text"
						id="exeptions"
						placeholder="<?= GetMessage($APL_MODULE_ID . '_SECTION') ?>"
						name="props[<?=$key?>]">
			</td>
		</tr>


	<tr class="-insert">

		<td><br>
			<input type="button" value="<?= GetMessage($APL_MODULE_ID . '_ADD_EXT') ?>" class="-add-ext">
		</td>
	</tr>
	<tr>
		<td><?=bitrix_sessid_post();?></td>
	</tr>
	<? $tabControl->Buttons() ?>
		<div class="-tab-btns" data-tab-id="step0">
			<input type="submit" class="adm-btn-save -link-properties"
				   value="<?=GetMessage($APL_MODULE_ID . "_SUBMIT")?>"
				   title="<?=GetMessage($APL_MODULE_ID . "_SUBMIT")?>">
			<input type="button" class="adm-btn-clear -clear"
					value="<?=GetMessage($APL_MODULE_ID . "_CLEAR")?>"
					title="<?=GetMessage($APL_MODULE_ID . "_CLEAR")?>">
			<span class="-result-message result"></span>
		</div>
		<div class="-tab-btns hdn" data-tab-id="step1">
			<input type="button"
				value="<?=GetMessage($APL_MODULE_ID . "_SAVE")?>"
				title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>"
				class="adm-btn-save -save-settings">
		</div>
	<? $tabControl->End() ?>

</div>
</form>

<script>
	(function(w) {
		var app = w.app || {};

		app.PropLink.init();

	})(window);
</script>


<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>
