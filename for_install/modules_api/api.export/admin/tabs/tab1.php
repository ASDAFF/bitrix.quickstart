<?
/**
 * Bitrix vars
 *
 * @var array      $arFieldTitle
 * @var array      $profile
 * @var CAdminForm $tabControl
 *
 * @var CUser      $USER
 * @var CMain      $APPLICATION
 *
 */
use \Bitrix\Main\Localization\Loc;
use \Api\Export\Tools;

Loc::loadMessages(__FILE__);

$tabControl->AddViewField('PROFILE[ID]', $arFieldTitle['ID'] . ':', $profile['ID']);
$tabControl->AddCheckBoxField('PROFILE[ACTIVE]', $arFieldTitle['ACTIVE'], false, array('Y','N'), $profile['ACTIVE'] != 'N');
$tabControl->AddEditField('PROFILE[SORT]', $arFieldTitle['SORT'], true, array('size' => 5), $profile['SORT']);

if($profile['DATE_CREATE'])
	$tabControl->AddViewField('PROFILE[DATE_CREATE]', $arFieldTitle['DATE_CREATE'] . ':', $profile['DATE_CREATE']);
if($profile['TIMESTAMP_X'])
	$tabControl->AddViewField('PROFILE[TIMESTAMP_X]', $arFieldTitle['TIMESTAMP_X'] . ':', $profile['TIMESTAMP_X']);
if($profile['MODIFIED_BY'])
	$tabControl->AddViewField('PROFILE[MODIFIED_BY]', $arFieldTitle['MODIFIED_BY'] . ':', Tools::getFormatedUserName($profile['MODIFIED_BY'], false, true));

$tabControl->AddEditField('PROFILE[NAME]', $arFieldTitle['NAME'], true, array('size' => 60), $profile['NAME']);

?>
<? $tabControl->BeginCustomField('PROFILE[SITE_ID]', $arFieldTitle['SITE_ID'], true); ?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td><?=\CSite::SelectBox('PROFILE[SITE_ID]', $profile['SITE_ID'])?></td>
	</tr>
<? $tabControl->EndCustomField('PROFILE[SITE_ID]'); ?>

<? $tabControl->BeginCustomField('PROFILE[CHARSET]', $arFieldTitle['CHARSET']); ?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<select name="PROFILE[CHARSET]">
				<? foreach($arCharset as $charset => $name): ?>
					<?
					$selected = ($charset == $profile['CHARSET'] ? ' selected' : '');
					?>
					<option value="<?=$charset?>"<?=$selected?>><?=$name?></option>
				<? endforeach ?>
			</select>
		</td>
	</tr>
<? $tabControl->EndCustomField('PROFILE[CHARSET]'); ?>

<?
$tabControl->AddEditField('PROFILE[STEP_LIMIT]', $arFieldTitle['STEP_LIMIT'], true, array('size' => 5), $profile['STEP_LIMIT']);

if(strlen($profile['FILE_PATH'])>0)
	$tabControl->AddViewField('PROFILE[FILE_PATH]', $arFieldTitle['FILE_PATH'], '<a href="'. $profile['FILE_PATH'] .'" target="_blank">'. $profile['FILE_PATH'] .'</a>');
else
	$tabControl->AddViewField('PROFILE[FILE_PATH]', $arFieldTitle['FILE_PATH'], $profile['FILE_PATH']);

$tabControl->AddViewField('PROFILE[LAST_START]', $arFieldTitle['LAST_START'], $profile['LAST_START']);
$tabControl->AddViewField('PROFILE[LAST_END]', $arFieldTitle['LAST_END'], $profile['LAST_END']);
$tabControl->AddViewField('PROFILE[TOTAL_ITEMS]', $arFieldTitle['TOTAL_ITEMS'], $profile['TOTAL_ITEMS']);
$tabControl->AddViewField('PROFILE[TOTAL_ELEMENTS]', $arFieldTitle['TOTAL_ELEMENTS'], $profile['TOTAL_ELEMENTS']);
$tabControl->AddViewField('PROFILE[TOTAL_OFFERS]', $arFieldTitle['TOTAL_OFFERS'], $profile['TOTAL_OFFERS']);
$tabControl->AddViewField('PROFILE[TOTAL_SECTIONS]', $arFieldTitle['TOTAL_SECTIONS'], $profile['TOTAL_SECTIONS']);
$tabControl->AddViewField('PROFILE[TOTAL_RUN_TIME]', $arFieldTitle['TOTAL_RUN_TIME'], $profile['TOTAL_RUN_TIME']);
$tabControl->AddViewField('PROFILE[TOTAL_MEMORY]', $arFieldTitle['TOTAL_MEMORY'], $profile['TOTAL_MEMORY']);
?>