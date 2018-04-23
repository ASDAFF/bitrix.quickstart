<?

if(!$GLOBALS['USER']->IsAdmin()) {
	return;
}

$MODULE_ID = 'primepix.propertylink';

CModule::IncludeModule($MODULE_ID);

IncludeModuleLangFile(__FILE__);

// handle POST request
$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

$exepts = new Primepix\CPxGetExepts;

$exeptions = $exepts->getExpList();

if ($REQUEST_METHOD === 'POST' && check_bitrix_sessid()) {

	foreach ($_POST['iblocks'] as $key => $val) {

		$exeptionsAll[ $key ][] = $_POST['iblocks'][ $key ];
		$exeptionsAll[ $key ][] = $_POST['props'][ $key ];

		$removed = array_diff_assoc($exeptions[ $key ], $exeptionsAll[ $key ]);
		$added   = array_diff_assoc($exeptionsAll[ $key ], $exeptions[ $key ]);
		$empty   = empty($exeptionsAll[ $key ][0]) && empty($exeptionsAll[ $key ][1]);

		if ($removed || $empty) {

			$exepts->removeExept($removed[0], $removed[1]);
		}

		if ($added) {

			$exepts->addExept($added[0], $added[1]);
		}

		if (empty($exeptions[ $key ]) && !$empty) {

			$exepts->addExept($exeptionsAll[ $key ][0], $exeptionsAll[ $key ][1]);
		}

		if (!empty($exeptions[ $key ]) && $empty) {

			$exepts->removeExept($exeptions[ $key ][0], $exeptions[ $key ][1]);
		}
		
	}

	$exeptions = $exepts->getExpList();
}

CJSCore::RegisterExt(
    'options_ext', 
    array(
        'js'   => '/bitrix/js/' . $MODULE_ID . '/options/options.js',
        'css'   => '/bitrix/js/' . $MODULE_ID . '/options/styles.css',
        'rel'  => array('jquery')  
    )
);


CJSCore::Init('options_ext');

// set main tab
$aTabs = array(
	array(
		'DIV' => 'edit1', 
		'TAB' => GetMessage('MAIN_TAB_SET'), 
		'ICON' => 'ib_settings', 
		'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')
	),
);

$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>



<? $tabControl->Begin(); ?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($MODULE_ID)?>&amp;lang=<?=LANGUAGE_ID?>" class="settings-form -settings-form">
<? $tabControl->BeginNextTab(); ?>

	<tr>
		<td>
			<label for="exeptions">
				<?= ShowJsHint(GetMessage($MODULE_ID . '_EXEPTIONS_HINT')) ?>
				<?= GetMessage($MODULE_ID . '_EXEPTIONS') ?>
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
					value="<?=$exeptions[$key][0]?>">
				<input
					type="text"
					id="exeptions"
					name="props[<?=$key?>]"
					value="<?=$exeptions[$key][1]?>">
			</td>
		</tr>
	<?endforeach?>
	<input type="hidden" class="-last-id" value="<?=++$key?>">
	<tr>
		<td>
			<input
					type="text"
					id="exeptions"
					name="iblocks[<?=$key?>]">
				<input
					type="text"
					id="exeptions"
					name="props[<?=$key?>]">
		</td>
	</tr>
	<tr class="-insert">

		<td>
			<input type="button" value="<?= GetMessage($MODULE_ID . '_ADD_EXT') ?>" class="-add-ext">
		</td>
	</tr>

<? $tabControl->Buttons(); ?>
	<input type="submit" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
	<?=bitrix_sessid_post();?>
<? $tabControl->End(); ?>
</form>