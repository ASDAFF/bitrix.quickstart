<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */
use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\Type\DateTime,
	 Bitrix\Main\Localization\Loc;

define("ADMIN_MODULE_NAME", "api.qa");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;

$rights = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($rights < 'W')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

use \Api\QA\QuestionTable;


//Лэнги полей
$arHeaders    = array();
$arFieldTitle = array();

$columns = QuestionTable::getEntity()->getFields();
/** @var \Bitrix\Main\Entity\Field $field */
foreach($columns as $code => $column) {
	$arFieldTitle[ $column->getName() ] = $column->getTitle();

	$arHeaders[] = array('id' => $code, 'content' => $column->getTitle(), 'sort' => $code, 'default' => true);
}


$context      = Application::getInstance()->getContext();
$documentRoot = Application::getDocumentRoot();
$lang         = $context->getLanguage();
$request      = $context->getRequest();
$id           = intval($request->get('ID'));
$bCopy        = ($action == "copy");
$bUpdate      = ($request->get('update') == "Y");
$bSale        = Loader::includeModule('sale');
$bIblock      = Loader::includeModule('iblock');
$arQuestion   = array();
$errorMessage = '';


if($request->isPost() && $bUpdate && check_bitrix_sessid()) {
	$postFields = $request->getPostList()->toArray();

	//Prepare post fields
	foreach($postFields as $key => $val) {
		if(!array_key_exists($key, $arFieldTitle))
			unset($postFields[ $key ]);
	}

	$postFields['ACTIVE'] = ($postFields['ACTIVE'] == 'Y' ? 'Y' : 'N');
	$postFields['NOTIFY'] = ($postFields['NOTIFY'] == 'Y' ? 'Y' : 'N');

	if($postFields['DATE_CREATE'])
		$postFields['DATE_CREATE'] = new DateTime();

	//$postFields['TEXT'] = $postFields['TEXT'];

	if(empty($postFields['SITE_ID']))
		$errorMessage .= Loc::getMessage('AQAA_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['SITE_ID'])) . "\n";


	//Write data to db
	if(empty($errorMessage)) {

		$result = null;
		if($id && !$bCopy) {
			$result = QuestionTable::update($id, $postFields);
		}
		else {
			$result = QuestionTable::add($postFields);
		}

		if($result && $result->isSuccess()) {
			$id = $result->getId();

			//Delete components cache
			//BXClearCache(true, '/' . $fields['SITE_ID'] . '/api/reviews.list');
			//BXClearCache(true, '/' . $fields['SITE_ID'] . '/api/reviews.stat');


			//Отправим ответ клиенту
			/*if($request->get('SEND_EVENT') == 'Y') {
				Event::sendReply($id, $fields);
			}*/

			if(strlen($request->getPost("apply")) == 0)
				LocalRedirect("/bitrix/admin/api_qa_list.php?lang=" . $lang . "&" . GetFilterParams("filter_", false));
			else
				LocalRedirect("/bitrix/admin/api_qa_edit.php?lang=" . $lang . "&ID=" . $id . "&" . GetFilterParams("filter_", false));
		}
		else {
			$errorMessage .= join("\n", $result->getErrorMessages());
		}
	}

	unset($fields);
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
//START VIEW

if($errorMessage)
	CAdminMessage::ShowMessage($errorMessage);


if($id > 0) {
	$select     = array('*');
	$params     = array(
		 'select' => $select,
		 'filter' => array('=ID' => $id),
	);
	$arQuestion = QuestionTable::getRow($params);

	$APPLICATION->SetTitle('ID(' . $id . ')');
}
else {
	$APPLICATION->SetTitle(Loc::getMessage("AQAA_TITLE"));
}


//Кнопки = Добавить/Копировать/Удалить
$aContext = array(
	 array(
			"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_LIST'),
			"LINK" => "api_qa_list.php?lang=" . $lang,
			"ICON" => "btn_list",
	 ),
);
if($id && !$bCopy && $rights == 'W') {
	$aContext[] = array("SEPARATOR" => "Y");
	$aContext[] = array(
		 "TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_ADD'),
		 "LINK" => "api_qa_edit.php?lang=" . $lang,
		 "ICON" => "btn_new",
	);
	$aContext[] = array(
		 "TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
		 "LINK" => "javascript:if(confirm('" . Loc::getMessage('AQAA_DELETE_CONFIRM') . "'))window.location='api_qa_list.php?ID=" . $id . "&action=delete&lang=" . $lang . "&" . bitrix_sessid_get() . "';",
		 "ICON" => "btn_delete",
	);
}
$context = new CAdminContextMenu($aContext);
$context->Show();


$aTabs      = array(
	 array(
			"DIV"   => "review",
			"TAB"   => Loc::getMessage('AQAA_TAB_NAME_'. $arQuestion['TYPE']),
			"ICON"  => "",
			"TITLE" => Loc::getMessage('AQAA_TAB_NAME_'. $arQuestion['TYPE']),
	 ),
);
$tabControl = new CAdminForm("review_edit", $aTabs);


//---------- Все данные по отзыву ----------//
$fields = ($request->isPost()) ? $request->getPostList()->toArray() : $arQuestion;


//---------- Все данные по инфоблоку/разделу/элементу ----------//
$arIblock  = array();
$arElement = array();
if($bIblock) {
	if($elId = $fields['ELEMENT_ID']) {
		$arElement = CIBlockElement::GetList(false, array('=ID' => $elId), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'DETAIL_PAGE_URL'))->GetNext(false, false);
	}

	$rsIblock = CIBlock::GetList(array('ID' => 'ASC'));
	while($iblock = $rsIblock->Fetch())
		$arIblock[ $iblock['ID'] ] = $iblock['NAME'];
}


$tabControl->BeginPrologContent();
?>
	<style type="text/css">
		#review textarea{ width: 100%; min-height: 80px }
		#review input[size*=]{ width: 100%; }
	</style>
<?
//echo BeginNote();
//echo Loc::getMessage('AQAA_NOTE_1');
//echo EndNote();

$tabControl->EndPrologContent();


$tabControl->BeginEpilogContent();
?>
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="update" value="Y">
	<input type="hidden" name="lang" value="<?=$lang;?>">

<? if(!$fields['DATE_CREATE'] || $bCopy): ?>
	<input type="hidden" name="DATE_CREATE" value="Y">
<? endif ?>

<? if(!$bCopy): ?>
	<input type="hidden" name="ID" value="<?=$id;?>">
<? endif ?>
<?
$tabControl->EndEpilogContent();

//заголовки закладок
$tabControl->Begin(array('FORM_ACTION' => $APPLICATION->GetCurPage() . "?lang=" . $lang));


//*********************************************************
//                   первая закладка
//*********************************************************
$tabControl->BeginNextFormTab();

$tabControl->AddViewField('ID', $arFieldTitle['ID'] . ':', $fields['ID']);
$tabControl->AddViewField('PARENT_ID', $arFieldTitle['PARENT_ID'], '<a href="api_qa_edit.php?ID=' . $fields['PARENT_ID'] . '&lang=' . $lang . GetFilterParams("filter_") . '">' . $fields['PARENT_ID'] . '</a>', false);
$tabControl->AddViewField('LEVEL', $arFieldTitle['LEVEL'], $fields['LEVEL'], false);
$tabControl->AddViewField('DATE_CREATE', $arFieldTitle['DATE_CREATE'] . ':', $fields['DATE_CREATE']);
$tabControl->AddCheckBoxField('ACTIVE', $arFieldTitle['ACTIVE'], false, 'Y', $fields['ACTIVE'] != 'N');

$tabControl->BeginCustomField('TYPE', $arFieldTitle['TYPE'], true);
$arTypes = Loc::getMessage('AQAA_EDIT_TYPES');
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<select name="FIELDS[TYPE]">
				<? foreach($arTypes as $key => $val): ?>
					<option value="<?=$key?>"<?=($fields['TYPE'] == $key ? ' selected' : '')?>><?=$val?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
<?
$tabControl->EndCustomField('TYPE');

$tabControl->BeginCustomField('SITE_ID', $arFieldTitle['SITE_ID'], true);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?=CSite::SelectBox('SITE_ID',$fields['SITE_ID']);?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('SITE_ID');


$tabControl->BeginCustomField('IBLOCK_ID', $arFieldTitle['IBLOCK_ID']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<select name="IBLOCK_ID">
				<option value="0"><?=Loc::getMessage('AQAA_OPTION_EMPTY')?></option>
				<? foreach($arIblock as $key => $val): ?>
					<option value="<?=$key?>"<?=($fields['IBLOCK_ID'] == $key ? ' selected' : '')?>>[<?=$key?>] <?=$val?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
<?
$tabControl->EndCustomField('IBLOCK_ID');

$tabControl->BeginCustomField('ELEMENT_ID', $arFieldTitle['ELEMENT_ID']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<input type="text"
			       name="ELEMENT_ID"
			       id="ELEMENT_ID_VALUE"
			       value="<?=$fields['ELEMENT_ID']?>">
			<input type="button"
			       value="..."
			       class="OpenWindow"
			       onclick="jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=<?=$lang;?>&IBLOCK_ID=<?=$fields['IBLOCK_ID']?>&n=ELEMENT_ID_VALUE&k=0', 900, 700);">
			<? if($arElement['ID']): ?>
				<?
				$publicName = ($fields['PAGE_TITLE'] ? $fields['PAGE_TITLE'] : $arElement['NAME']);
				$publicLink = ($fields['PAGE_URL'] ? $fields['PAGE_URL'] : $arElement['DETAIL_PAGE_URL']);
				$adminLink  = "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID={$arElement['IBLOCK_ID']}&type={$arElement['IBLOCK_TYPE_ID']}&ID={$arElement['ID']}&lang={$lang}&find_section_section={$arElement['IBLOCK_SECTION_ID']}&WF=Y";
				?>
				(
				<a href="<?=$adminLink;?>" target="_blank" title="<?=Loc::getMessage('AQAA_ADMIN_LINK');?>"><?=Loc::getMessage('AQAA_ADMIN_LINK');?></a> |
				<a href="<?=$publicLink;?>" target="_blank" title="<?=Loc::getMessage('AQAA_PUBLIC_LINK');?>"><?=Loc::getMessage('AQAA_PUBLIC_LINK');?></a>
				)
				<?=$publicName?>
			<? endif ?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('ELEMENT_ID');

$tabControl->AddEditField('XML_ID', $arFieldTitle['XML_ID'], false, array('size' => 100), $fields['XML_ID']);
$tabControl->AddEditField('CODE', $arFieldTitle['CODE'], false, array('size' => 100), $fields['CODE']);

$tabControl->AddEditField('GUEST_NAME', $arFieldTitle['GUEST_NAME'], false, array('size' => 100), $fields['GUEST_NAME']);
$tabControl->AddEditField('GUEST_EMAIL', $arFieldTitle['GUEST_EMAIL'], false, array('size' => 100), $fields['GUEST_EMAIL']);
$tabControl->AddTextField('TEXT', $arFieldTitle['TEXT'], $fields['TEXT'], array('rows'=>15));

$tabControl->BeginCustomField('USER_ID', $arFieldTitle['USER_ID']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?=FindUserID("USER_ID", $fields['USER_ID'], "", "review_edit_form", "", "", "...", "", "");?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('USER_ID');

$tabControl->AddEditField('PAGE_TITLE', $arFieldTitle['PAGE_TITLE'], false, array('size' => 100), $fields['PAGE_TITLE']);
$tabControl->AddEditField('PAGE_URL', $arFieldTitle['PAGE_URL'], false, array('size' => 100), $fields['PAGE_URL']);

$tabControl->AddCheckBoxField('NOTIFY', $arFieldTitle['NOTIFY'], false, 'Y', $fields['NOTIFY'] == 'Y');
$tabControl->AddViewField('IP', $arFieldTitle['IP'], $fields['IP']);

//$tabControl->AddEditField('VOTE_UP', $arFieldTitle['VOTE_UP'], false, array(), $fields['VOTE_UP']);
//$tabControl->AddEditField('VOTE_DO', $arFieldTitle['VOTE_DO'], false, array(), $fields['VOTE_DO']);

/*if($bSale) {
	$tabControl->BeginCustomField('LOCATION', $arFieldTitle['LOCATION']);
	?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?
			CSaleLocation::proxySaleAjaxLocationsComponent(
				 array(
						"LOCATION_VALUE"  => $fields['LOCATION'],
						"CITY_INPUT_NAME" => 'LOCATION',
						"SITE_ID"         => $fields['SITE_ID'],
				 ),
				 array(),
				 '',
				 true,
				 'api-location'
			);
			?>
		</td>
	</tr>
	<?
	$tabControl->EndCustomField('LOCATION');
}
else {
	$tabControl->AddEditField('LOCATION', $arFieldTitle['LOCATION'], false, array('size' => 100), $fields['LOCATION']);
}*/

/*
$tabControl->BeginCustomField('TEXT', $arFieldTitle['TEXT'], true);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?
			CFileMan::AddHTMLEditorFrame(
				 "TEXT",
				 $fields["TEXT"],
				 "TEXT_TYPE",
				 (isset($_REQUEST["TEXT_TYPE"]) ? $_REQUEST["TEXT_TYPE"] : 'html'),
				 array(
						'height' => 200,
						'width'  => '100%',
				 )
			); ?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('TEXT');
*/

//---------- HEADING_REPLY ----------//
//$tabControl->AddSection('HEADING_ANSWER', Loc::getMessage('AQAA_HEADING_ANSWER'));
//$tabControl->AddTextField('ANSWER', $arFieldTitle['REPLY'], $fields['REPLY']);


$tabControl->Buttons(array(
	 "disabled" => ($rights < "W"),
	 "back_url" => "api_qa_list.php?lang=" . $lang,
));

$tabControl->Show();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>