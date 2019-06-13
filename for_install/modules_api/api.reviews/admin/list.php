<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\Localization\Loc;

define("ADMIN_MODULE_NAME", "api.reviews");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION, $USER_FIELD_MANAGER;

$AR_RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($AR_RIGHT == 'D')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));


use \Api\Reviews\ReviewsTable;
use \Api\Reviews\Tools;
use \Api\Reviews\Agent;


//Лэнги полей
$arFieldTitle = array();
foreach(ReviewsTable::getMap() as $key => $value) {
	$arFieldTitle[ $key ] = $value['title'];
}


$conn    = Application::getConnection();
$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$lang    = $context->getLanguage();

$errorMsgs = null;


$ufEntityId = 'API_REVIEWS';
$sTableID   = ReviewsTable::getTableName();
$oSort      = new CAdminSorting($sTableID, 'ID', 'desc');
$lAdmin     = new CAdminList($sTableID, $oSort);

$filterFields = array(
	 'filter_title',
	 'filter_active',
	 'filter_site_id',
	 'filter_rating',
	 'filter_user_id',
	 'filter_iblock_id',
	 'filter_section_id',
	 'filter_element_id',
	 'filter_order_id',
	 'filter_url',
	 'filter_delivery',
	 'filter_city',
	 'filter_guest_name',
	 'filter_guest_email',
	 'filter_guest_phone',
	 'filter_ip',
	 'filter_reply_send',
	 'filter_subscribe_send',
);
$USER_FIELD_MANAGER->AdminListAddFilterFields($ufEntityId, $filterFields);

$lAdmin->InitFilter($filterFields);

$filter = array();
if($filter_name)
	$filter['?TITLE'] = $filter_name;
if($filter_active && $filter_active != 'NOT_REF')
	$filter['=ACTIVE'] = $filter_active;
if($filter_site_id && $filter_site_id != 'NOT_REF')
	$filter['=SITE_ID'] = $filter_site_id;
if($filter_rating && $filter_rating[0] != 'NOT_REF')
	$filter['=RATING'] = $filter_rating;
if($filter_user_id)
	$filter['=USER_ID'] = $filter_user_id;
if($filter_iblock_id)
	$filter['=IBLOCK_ID'] = $filter_iblock_id;
if($filter_section_id)
	$filter['=SECTION_ID'] = $filter_section_id;
if($filter_element_id)
	$filter['=ELEMENT_ID'] = $filter_element_id;
if($filter_order_id)
	$filter['=ORDER_ID'] = $filter_order_id;
if($filter_url)
	$filter['?URL'] = $filter_url;
if($filter_delivery)
	$filter['=DELIVERY'] = $filter_delivery;
if($filter_city)
	$filter['=CITY'] = $filter_city;
if($filter_city)
	$filter['=CITY'] = $filter_city;
if($filter_guest_name)
	$filter['=GUEST_NAME'] = $filter_guest_name;
if($filter_guest_email)
	$filter['=GUEST_EMAIL'] = $filter_guest_email;
if($filter_guest_phone)
	$filter['=GUEST_PHONE'] = $filter_guest_phone;
if($filter_ip)
	$filter['=IP'] = $filter_ip;
if($filter_reply_send && $filter_reply_send != 'NOT_REF')
	$filter['=REPLY_SEND'] = $filter_reply_send;
if($filter_subscribe_send && $filter_subscribe_send != 'NOT_REF')
	$filter['=SUBSCRIBE_SEND'] = $filter_subscribe_send;


$USER_FIELD_MANAGER->AdminListAddFilter($ufEntityId, $filter);

if($AR_RIGHT == 'W' && $lAdmin->EditAction()) {
	foreach($request->getPost('FIELDS') as $id => $arFields) {
		$error = false;
		$id    = intval($id);

		if($id <= 0 || !$lAdmin->IsUpdated($id))
			continue;

		/*$reqFields = array('RATING');
		foreach($reqFields as $reqField) {
			if(empty($arFields[ $reqField ])) {
				$error = true;
				$lAdmin->AddUpdateError('#' . $id . ' : ' . Loc::getMessage('ARAL_FIELD_ERROR', array('#FIELD#' => $arFieldTitle[ $reqField ])), $id);
			}
		}*/

		if(!$error) {
			$arFields['ACTIVE'] = ($arFields['ACTIVE'] == 'Y' ? 'Y' : 'N');

			if($arFields['ACTIVE_FROM'])
				$arFields['ACTIVE_FROM'] = new \Bitrix\Main\Type\DateTime($arFields['ACTIVE_FROM']);

			if($arFields['DATE_CREATE'])
				$arFields['DATE_CREATE'] = new \Bitrix\Main\Type\DateTime($arFields['DATE_CREATE']);

			//$arFields['TIMESTAMP_X'] = new \Bitrix\Main\Type\DateTime();
			//$arFields['MODIFIED_BY'] = $USER->GetID();

			$conn->startTransaction();
			$res = ReviewsTable::update($id, $arFields);
			if(!$res->isSuccess()) {
				$conn->rollbackTransaction();
				$lAdmin->AddUpdateError(join("\n", $res->getErrorMessages()), $id);
				continue;
			}
			$conn->commitTransaction();
		}
	}
}

if($AR_RIGHT == 'W' && $ids = $lAdmin->GroupAction()) {
	if($_REQUEST['action_target'] == 'selected') {
		$ids          = array();
		$params       = array(
			 'select' => array('ID'),
			 'filter' => $filter,
		);
		$dbResultList = ReviewsTable::getList($params);

		while($result = $dbResultList->fetch())
			$ids[] = $result['ID'];
	}

	foreach($ids as $id) {
		if(empty($id))
			continue;

		//Обязательно смотрим $_REQUEST
		$action = $_REQUEST['action'];

		switch($action) {
			case "delete":
				@set_time_limit(0);

				$result = ReviewsTable::delete($id);
				if(!$result->isSuccess()) {
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('ARAL_ERROR_DELETE'), $id);
				}
				else {
					Agent::delete($id);
				}
				break;

			case 'activate':
			case 'deactivate':
			case 'moderation':

				$arFields['ACTIVE']      = ($action == 'activate' ? 'Y' : 'N');
				$arFields['ACTIVE_FROM'] = new \Bitrix\Main\Type\DateTime($arFields['ACTIVE_FROM']);
				//$arFields['TIMESTAMP_X'] = new \Bitrix\Main\Type\DateTime();
				//$arFields['MODIFIED_BY'] = $USER->GetID();

				if($action == 'moderation') {
					$arFields['ACTIVE']      = 'N';
					$arFields['ACTIVE_FROM'] = null;
				}

				$result = ReviewsTable::update($id, $arFields);
				if(!$result->isSuccess()) {
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('ARAL_ERROR_SAVE'), $id);
				}
				break;
		}
	}
}


$userFields = $USER_FIELD_MANAGER->GetUserFields($ufEntityId);
$select     = array('*');
foreach($userFields as $field)
	$select[] = $field['FIELD_NAME'];

$params = array(
	 'select' => $select,
	 'filter' => $filter,
	 'order'  => array($by => $order),
);


$arReviews    = ReviewsTable::getList($params);
$dbResultList = new CAdminResult($arReviews, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage('ARAL_NAV_TITLE')));

$arHeaders = array(
	 array(
			'id'      => 'ID',
			'content' => $arFieldTitle['ID'],
			'sort'    => 'ID',
			'default' => true,
	 ),
	 array(
			'id'      => 'ACTIVE',
			'content' => $arFieldTitle['ACTIVE'],
			'sort'    => 'ACTIVE',
			'default' => true,
	 ),
	 array(
			'id'      => 'ACTIVE_FROM',
			'content' => $arFieldTitle['ACTIVE_FROM'],
			'sort'    => 'ACTIVE_FROM',
			'default' => true,
	 ),
	 array(
			'id'      => 'DATE_CREATE',
			'content' => $arFieldTitle['DATE_CREATE'],
			'sort'    => 'DATE_CREATE',
			'default' => true,
	 ),
	 array(
			'id'      => 'TIMESTAMP_X',
			'content' => $arFieldTitle['TIMESTAMP_X'],
			'sort'    => 'TIMESTAMP_X',
			'default' => true,
	 ),
	 array(
			'id'      => 'SITE_ID',
			'content' => $arFieldTitle['SITE_ID'],
			'sort'    => 'SITE_ID',
			'default' => true,
	 ),
	 array(
			'id'      => 'IBLOCK_ID',
			'content' => $arFieldTitle['IBLOCK_ID'],
			'sort'    => 'IBLOCK_ID',
			'default' => true,
	 ),
	 array(
			'id'      => 'SECTION_ID',
			'content' => $arFieldTitle['SECTION_ID'],
			'sort'    => 'SECTION_ID',
			'default' => true,
	 ),
	 array(
			'id'      => 'ELEMENT_ID',
			'content' => $arFieldTitle['ELEMENT_ID'],
			'sort'    => 'ELEMENT_ID',
			'default' => true,
	 ),
	 array(
			'id'      => 'ORDER_ID',
			'content' => $arFieldTitle['ORDER_ID'],
			'sort'    => 'ORDER_ID',
			'default' => true,
	 ),
	 array(
			'id'      => 'URL',
			'content' => $arFieldTitle['URL'],
			'sort'    => 'URL',
			'default' => true,
	 ),
	 array(
			'id'      => 'RATING',
			'content' => $arFieldTitle['RATING'],
			'sort'    => 'RATING',
			'default' => true,
	 ),
	 array(
			'id'      => 'THUMBS_UP',
			'content' => $arFieldTitle['THUMBS_UP'],
			'sort'    => 'THUMBS_UP',
			'default' => true,
	 ),
	 array(
			'id'      => 'THUMBS_DOWN',
			'content' => $arFieldTitle['THUMBS_DOWN'],
			'sort'    => 'THUMBS_DOWN',
			'default' => true,
	 ),
	 array(
			'id'      => 'TITLE',
			'content' => $arFieldTitle['TITLE'],
			'sort'    => 'TITLE',
			'default' => true,
	 ),
	 /*
	 array(
		 'id'      => 'ADVANTAGE',
		 'content' => $arFieldTitle['ADVANTAGE'],
		 'sort'    => 'ADVANTAGE',
		 'default' => true,
	 ),
	 array(
		 'id'      => 'DISADVANTAGE',
		 'content' => $arFieldTitle['DISADVANTAGE'],
		 'sort'    => 'DISADVANTAGE',
		 'default' => true,
	 ),
	 array(
		 'id'      => 'ANNOTATION',
		 'content' => $arFieldTitle['ANNOTATION'],
		 'sort'    => 'ANNOTATION',
		 'default' => true,
	 ),
	 array(
		 'id'      => 'REPLY',
		 'content' => $arFieldTitle['REPLY'],
		 'sort'    => 'REPLY',
		 'default' => true,
	 ),
	 */
	 array(
			'id'      => 'USER_ID',
			'content' => $arFieldTitle['USER_ID'],
			'sort'    => 'USER_ID',
			'default' => true,
	 ),
	 array(
			'id'      => 'GUEST_NAME',
			'content' => $arFieldTitle['GUEST_NAME'],
			'sort'    => 'GUEST_NAME',
			'default' => true,
	 ),
	 array(
			'id'      => 'GUEST_EMAIL',
			'content' => $arFieldTitle['GUEST_EMAIL'],
			'sort'    => 'GUEST_EMAIL',
			'default' => true,
	 ),
	 array(
			'id'      => 'GUEST_PHONE',
			'content' => $arFieldTitle['GUEST_PHONE'],
			'sort'    => 'GUEST_PHONE',
			'default' => true,
	 ),
	 array(
			'id'      => 'COMPANY',
			'content' => $arFieldTitle['COMPANY'],
			'sort'    => 'COMPANY',
			'default' => true,
	 ),
	 array(
			'id'      => 'WEBSITE',
			'content' => $arFieldTitle['WEBSITE'],
			'sort'    => 'WEBSITE',
			'default' => true,
	 ),
	 array(
			'id'      => 'PAGE_TITLE',
			'content' => $arFieldTitle['PAGE_TITLE'],
			'sort'    => 'PAGE_TITLE',
			'default' => true,
	 ),
	 array(
			'id'      => 'PAGE_URL',
			'content' => $arFieldTitle['PAGE_URL'],
			'sort'    => 'PAGE_URL',
			'default' => true,
	 ),
	 array(
			'id'      => 'REPLY_SEND',
			'content' => $arFieldTitle['REPLY_SEND'],
			'sort'    => 'REPLY_SEND',
			'default' => true,
	 ),
	 array(
			'id'      => 'SUBSCRIBE_SEND',
			'content' => $arFieldTitle['SUBSCRIBE_SEND'],
			'sort'    => 'SUBSCRIBE_SEND',
			'default' => true,
	 ),
	 array(
			'id'      => 'IP',
			'content' => $arFieldTitle['IP'],
			'sort'    => 'IP',
			'default' => true,
	 ),
);
$USER_FIELD_MANAGER->AdminListAddHeaders($ufEntityId, $headers);
$lAdmin->AddHeaders($arHeaders);

while($arRecord = $dbResultList->NavNext(true, 'f_')) {
	//$row = &$lAdmin->AddRow($f_ID, $arRecord, "api_reviews_edit.php?ID=".$f_ID."&lang=".$lang, Loc::getMessage('SALE_COMPANY_EDIT_DESCR'));
	$row = &$lAdmin->AddRow($f_ID, $arRecord);


	if($arRecord['ACTIVE_FROM'] == null && $arRecord['ACTIVE'] == 'N') {
		$lamp     = '/bitrix/images/api.reviews/yellow.gif';
		$lamp_alt = Loc::getMessage("ARAL_YELLOW_ALT");
	}
	elseif($arRecord['ACTIVE'] == 'N') {
		$lamp     = '/bitrix/images/api.reviews/red.gif';
		$lamp_alt = Loc::getMessage("ARAL_RED_ALT");
	}
	else {
		$lamp     = '/bitrix/images/api.reviews/green.gif';
		$lamp_alt = Loc::getMessage("ARAL_GREEN_ALT");
	}
	$idTmp = "<img src='" . $lamp . "' hspace='4' alt='" . htmlspecialcharsbx($lamp_alt) . "' title='" . htmlspecialcharsbx($lamp_alt) . "'>";

	$row->AddViewField('ID', '<a style="white-space:nowrap;margin-right:8px;font-weight:bold;" href="api_reviews_edit.php?ID=' . $f_ID . '&lang=' . $lang . GetFilterParams("filter_") . '">' . $idTmp . Loc::getMessage('ARAL_REVIEW_NUMBER', array('#ID#' => $f_ID)) . '</a>');
	//$row->AddField('ACTIVE', '<a href="api_reviews_edit.php?ID=' . $f_ID . '&lang=' . $lang . GetFilterParams("filter_") . '">' . $idTmp . '№'. $f_ID . '</a>');

	$row->AddCheckField('ACTIVE');
	$row->AddCalendarField('ACTIVE_FROM', $F_ACTIVE_FROM, true);
	$row->AddCalendarField('DATE_CREATE', $F_DATE_CREATE, true);

	if($row->bEditMode)
		$row->AddInputField('TITLE', array('size' => 20));
	else
		$row->AddField('TITLE', "<a href=\"api_reviews_edit.php?ID=" . $f_ID . "&lang=" . $lang . GetFilterParams("filter_") . "\">" . $f_TITLE . "</a>");

	$row->AddEditField("SITE_ID", CLang::SelectBox("SITE_ID", $f_SITE_ID));

	$row->AddSelectField('RATING', Loc::getMessage('ARAL_RATING_VALUES'));
	//$row->AddSelectField('RATING', $f_RATING);
	$row->AddCheckField('SUBSCRIBE_SEND');
	$row->AddCheckField('REPLY_SEND');

	if($row->bEditMode)
		$row->AddInputField('USER_ID', $f_USER_ID);
	else
		$row->AddField('USER_ID', Tools::getFormatedUserName($f_USER_ID));

	if($row->bEditMode && $f_MESSAGE_TYPE == 'text')
		$row->AddInputField('MESSAGE', array('size' => 80));
	else
		$row->AddField('MESSAGE', $f_MESSAGE);

	$row->AddInputField('IBLOCK_ID', $f_IBLOCK_ID);
	$row->AddInputField('SECTION_ID', $f_SECTION_ID);
	$row->AddInputField('ELEMENT_ID', $f_ELEMENT_ID);
	$row->AddInputField('ORDER_ID', $f_ORDER_ID);
	$row->AddInputField('URL', $f_URL);
	$row->AddInputField('PAGE_URL', $f_PAGE_URL);
	$row->AddInputField('PAGE_TITLE', $f_PAGE_TITLE);
	$row->AddInputField('COMPANY', $f_COMPANY);
	$row->AddInputField('WEBSITE', $f_WEBSITE);
	$row->AddInputField('THUMBS_UP', $f_THUMBS_UP);
	$row->AddInputField('THUMBS_DOWN', $f_THUMBS_DOWN);

	$row->AddInputField('GUEST_NAME', $f_GUEST_NAME);
	$row->AddInputField('GUEST_EMAIL', $f_GUEST_EMAIL);
	$row->AddInputField('GUEST_PHONE', $f_GUEST_PHONE);

	$row->AddField('TIMESTAMP_X', $f_TIMESTAMP_X);

	$USER_FIELD_MANAGER->AddUserFields($ufEntityId, $arRecord, $row);

	$arActions   = array();
	$arActions[] = array(
		 'ICON'    => 'edit',
		 'TEXT'    => Loc::getMessage('MAIN_ADMIN_MENU_EDIT'),
		 'ACTION'  => $lAdmin->ActionRedirect('api_reviews_edit.php?ID=' . $f_ID . '&lang=' . $lang),
		 'DEFAULT' => true,
	);

	if($AR_RIGHT == 'W') {
		$arActions[] = array(
			 'ICON'   => 'copy',
			 'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
			 'ACTION' => $lAdmin->ActionRedirect('api_reviews_edit.php?ID=' . $f_ID . '&action=copy&lang=' . $lang),
		);
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array(
			 'ICON'   => 'delete',
			 'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
			 'ACTION' => "if(confirm('" . Loc::getMessage('ARAL_DELETE_CONFIRM') . "')) " . $lAdmin->ActionDoGroup($f_ID, 'delete'),
		);
	}

	$row->AddActions($arActions);
}


$lAdmin->AddFooter(
	 array(
			array(
				 'title' => Loc::getMessage('MAIN_ADMIN_LIST_SELECTED'),
				 'value' => $dbResultList->SelectedRowsCount(),
			),
			array(
				 'counter' => true,
				 'title'   => Loc::getMessage('MAIN_ADMIN_LIST_CHECKED'),
				 'value'   => '0',
			),
	 )
);


//Массовые операции
if($AR_RIGHT == 'W') {
	$lAdmin->AddGroupActionTable(Array(
		 'delete'     => Loc::getMessage('MAIN_ADMIN_LIST_DELETE'),
		 'activate'   => Loc::getMessage('MAIN_ADMIN_LIST_ACTIVATE'),
		 'deactivate' => Loc::getMessage('MAIN_ADMIN_LIST_DEACTIVATE'),
		 'moderation' => Loc::getMessage('ARAL_ADMIN_LIST_MODERATION'),
	));
}


//Кнопка Добавить
$lAdmin->AddAdminContextMenu(array(
	 array(
			'TEXT' => Loc::getMessage('MAIN_ADD'),
			'LINK' => 'api_reviews_edit.php?lang=' . $lang,
			'ICON' => 'btn_new',
	 ),
));

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('ARAL_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
?>
	<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?
		$arFindFields = array(
			 $arFieldTitle['TITLE'],
			 $arFieldTitle['ACTIVE'],
			 $arFieldTitle['SITE_ID'],
			 $arFieldTitle['RATING'],
			 $arFieldTitle['USER_ID'],
			 $arFieldTitle['IBLOCK_ID'],
			 $arFieldTitle['SECTION_ID'],
			 $arFieldTitle['ELEMENT_ID'],
			 $arFieldTitle['ORDER_ID'],
			 $arFieldTitle['URL'],
			 $arFieldTitle['DELIVERY'],
			 $arFieldTitle['CITY'],
			 $arFieldTitle['GUEST_NAME'],
			 $arFieldTitle['GUEST_EMAIL'],
			 $arFieldTitle['GUEST_PHONE'],
			 $arFieldTitle['IP'],
			 $arFieldTitle['REPLY_SEND'],
			 $arFieldTitle['SUBSCRIBE_SEND'],
		);
		$USER_FIELD_MANAGER->AddFindFields($ufEntityId, $arFindFields);
		$oFilter = new CAdminFilter(
			 $sTableID . "_filter",
			 $arFindFields
		);

		$oFilter->Begin();
		?>
		<tr>
			<td><?=$arFieldTitle['TITLE'];?>:</td>
			<td>
				<input type="text" name="filter_name" value="<?=htmlspecialcharsbx($filter_title)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['ACTIVE']?>:</td>
			<td>
				<select name="filter_active">
					<option value="NOT_REF">(<?=Loc::getMessage('ARAL_OPTION_ALL');?>)</option>
					<option value="Y"<? if($filter_active == 'Y')
						echo " selected" ?>><?=Loc::getMessage('ARAL_OPTION_YES');?></option>
					<option value="N"<? if($filter_active == 'N')
						echo " selected" ?>><?=Loc::getMessage('ARAL_OPTION_NO');?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['SITE_ID'];?>:</td>
			<td><?=CLang::SelectBox('filter_site_id', htmlspecialcharsbx($filter_site_id), Loc::getMessage('MAIN_ALL'));?></td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['RATING'];?>:</td>
			<td>
				<?
				$arRating = Loc::getMessage('ARAL_RATING_VALUES');
				echo SelectBoxMFromArray(
					 'filter_rating[]',
					 array(
							'reference'    => array_values($arRating),
							'reference_id' => array_keys($arRating),
					 ),
					 $filter_rating,
					 Loc::getMessage('MAIN_ALL'),
					 false,
					 6
				);
				?>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['USER_ID'];?>:</td>
			<td>
				<input type="text" name="filter_user_id" value="<?=htmlspecialcharsbx($filter_user_id)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['IBLOCK_ID'];?>:</td>
			<td>
				<input type="text" name="filter_iblock_id" value="<?=htmlspecialcharsbx($filter_iblock_id)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['SECTION_ID'];?>:</td>
			<td>
				<input type="text" name="filter_section_id" value="<?=htmlspecialcharsbx($filter_section_id)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['ELEMENT_ID'];?>:</td>
			<td>
				<input type="text" name="filter_element_id" value="<?=htmlspecialcharsbx($filter_element_id)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['ORDER_ID'];?>:</td>
			<td>
				<input type="text" name="filter_order_id" value="<?=htmlspecialcharsbx($filter_order_id)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['URL'];?>:</td>
			<td>
				<input type="text" name="filter_url" value="<?=htmlspecialcharsbx($filter_url)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['DELIVERY'];?>:</td>
			<td>
				<input type="text" name="filter_delivery" value="<?=htmlspecialcharsbx($filter_delivery)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['CITY'];?>:</td>
			<td>
				<input type="text" name="filter_city" value="<?=htmlspecialcharsbx($filter_city)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['GUEST_NAME'];?>:</td>
			<td>
				<input type="text" name="filter_guest_name" value="<?=htmlspecialcharsbx($filter_guest_name)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['GUEST_EMAIL'];?>:</td>
			<td>
				<input type="text" name="filter_guest_email" value="<?=htmlspecialcharsbx($filter_guest_email)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['GUEST_PHONE'];?>:</td>
			<td>
				<input type="text" name="filter_guest_phone" value="<?=htmlspecialcharsbx($filter_guest_phone)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['IP'];?>:</td>
			<td>
				<input type="text" name="filter_ip" value="<?=htmlspecialcharsbx($filter_ip)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['REPLY_SEND'];?>:</td>
			<td>
				<select name="filter_reply_send">
					<option value="NOT_REF">(<?=Loc::getMessage('ARAL_OPTION_ALL');?>)</option>
					<option value="Y"<? if($filter_reply_send == 'Y')
						echo " selected" ?>><?=Loc::getMessage('ARAL_OPTION_YES');?></option>
					<option value="N"<? if($filter_reply_send == 'N')
						echo " selected" ?>><?=Loc::getMessage('ARAL_OPTION_NO');?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['SUBSCRIBE_SEND'];?>:</td>
			<td>
				<select name="filter_subscribe_send">
					<option value="NOT_REF">(<?=Loc::getMessage('ARAL_OPTION_ALL');?>)</option>
					<option value="Y"<? if($filter_subscribe_send == 'Y')
						echo " selected" ?>><?=Loc::getMessage('ARAL_OPTION_YES');?></option>
					<option value="N"<? if($filter_subscribe_send == 'N')
						echo " selected" ?>><?=Loc::getMessage('ARAL_OPTION_NO');?></option>
				</select>
			</td>
		</tr>
		<?
		$USER_FIELD_MANAGER->AdminListShowFilter($ufEntityId);
		$oFilter->Buttons(
			 array(
					"table_id" => $sTableID,
					"url"      => $APPLICATION->GetCurPage(),
					"form"     => "find_form",
			 )
		);
		$oFilter->End();
		?>
	</form>
<?
$lAdmin->DisplayList();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>