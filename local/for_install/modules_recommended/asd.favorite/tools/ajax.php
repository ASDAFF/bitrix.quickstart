<?
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$key = Coption::GetOptionString('asd.favorite', 'js_key');

if (in_array($_REQUEST['action'], array('add', 'upd', 'default', 'fav_add', 'like', 'unlike')) && !strlen($_REQUEST['key']))
	die();
if (in_array($_REQUEST['action'], array('like', 'unlike')) && md5($_REQUEST['type'] . $_REQUEST['id'] . $key) != $_REQUEST['key'])
	die();
elseif (!in_array($_REQUEST['action'], array('like', 'unlike')) && strlen($_REQUEST['key']) > 0 && md5($_REQUEST['maxchars'] . $_REQUEST['type'] . $key) != $_REQUEST['key'])
	die();
if ($_REQUEST['action'] == 'getlike')
	$bNotCheckSess = true;
else
	$bNotCheckSess = false;

//$_REQUEST['id'] = intval($_REQUEST['id']);

if ((check_bitrix_sessid() || $bNotCheckSess) && CModule::IncludeModule('asd.favorite')) {
	if (!defined('BX_UTF')) {
		$_REQUEST['name'] = $APPLICATION->ConvertCharset($_REQUEST['name'], 'UTF-8', $_REQUEST['charset']);
		for ($i = 0; $i < $_REQUEST['count']; $i++)
			$_REQUEST['folder_' . $i] = $APPLICATION->ConvertCharset($_REQUEST['folder_' . $i], 'UTF-8', $_REQUEST['charset']);
	}
	if ($_REQUEST['action'] == 'add')
		echo intval(CASDfavorite::AddFolder(array('NAME' => substr($_REQUEST['name'], 0, $_REQUEST['maxchars']), 'CODE' => $_REQUEST['type'])));
	elseif ($_REQUEST['action'] == 'del')
		CASDfavorite::DeleteFolder($_REQUEST['id']);
	elseif ($_REQUEST['action'] == 'default')
		CASDfavorite::SetFolderDefault($USER->GetID(), $_REQUEST['type'], $_REQUEST['id']);
	elseif ($_REQUEST['action'] == 'like') {
		if (CModule::IncludeModule('statistic')) {
			if (!isset($_SESSION['ASD_FAVS_LIKES'])) {
				$_SESSION['ASD_FAVS_LIKES'] = array();
			}
			if (!in_array($_REQUEST['id'], $_SESSION['ASD_FAVS_LIKES'])) {
				$_SESSION['ASD_FAVS_LIKES'][] = $_REQUEST['id'];
				CStatEvent::AddCurrent('favotite', 'like', $_REQUEST['id']);
			}
		}
		CASDfavorite::Like($_REQUEST['id'], $_REQUEST['type']);
		$arLikes = CASDfavorite::GetLikes(array('ELEMENT_ID' => $_REQUEST['id'], 'CODE' => $_REQUEST['type']), 'ELEMENT_ID')->GetNext();
		header('Content-type: application/json');
		echo json_encode(array('COUNT' => intval($arLikes['CNT'])));
	} elseif ($_REQUEST['action'] == 'unlike') {
		if (CModule::IncludeModule('statistic')) {
			if (!isset($_SESSION['ASD_FAVS_UNLIKES'])) {
				$_SESSION['ASD_FAVS_UNLIKES'] = array();
			}
			if (!in_array($_REQUEST['id'], $_SESSION['ASD_FAVS_UNLIKES'])) {
				$_SESSION['ASD_FAVS_UNLIKES'][] = $_REQUEST['id'];
				CStatEvent::AddCurrent('favotite', 'unlike', $_REQUEST['id']);
			}
		}
		CASDfavorite::UnLike($_REQUEST['id'], $_REQUEST['type']);
		$arLikes = CASDfavorite::GetLikes(array('ELEMENT_ID' => $_REQUEST['id'], 'CODE' => $_REQUEST['type']), 'ELEMENT_ID')->GetNext();
		header('Content-type: application/json');
		echo json_encode(array('COUNT' => intval($arLikes['CNT'])));
	} elseif ($_REQUEST['action'] == 'getlike') {
		$arResult = array(
			'ELEMENTS' => array(),
			'OPTIONS' => array(
				'BGUEST' => !$USER->IsAuthorized(),
				'SESSID' => bitrix_sessid()
			)
		);
		foreach ($_REQUEST['id'] as $v) {
			$arResult['ELEMENTS'][$v] = array(
				'ELEMENT_ID' => $v,
				'COUNT' => 0,
				'FAVED' => false,
			);
		}
		$rsLikes = CASDfavorite::GetLikes(array('ELEMENT_ID' => $_REQUEST['id'], 'CODE' => $_REQUEST['type']), 'ELEMENT_ID');
		while ($arLike = $rsLikes->GetNext()) {
			$arResult['ELEMENTS'][$arLike['ELEMENT_ID']]['COUNT'] = $arLike['CNT'];
		}
		if ($USER->IsAuthorized()) {
			$rsList = CASDfavorite::GetLikes(array('ELEMENT_ID' => $_REQUEST['id'], 'CODE' => $_REQUEST['type'], 'USER_ID' => $USER->GetID()));
			while ($arLike = $rsList->GetNext()) {
				$arResult['ELEMENTS'][$arLike['ELEMENT_ID']]['FAVED'] = true;
			}
		}
		header('Content-type: application/json');
		echo json_encode($arResult);
	} elseif ($_REQUEST['action'] == 'upd') {
		for ($i = 0; $i < $_REQUEST['count']; $i++) {
			list($id, $name) = explode('|', $_REQUEST['folder_' . $i]);
			CASDfavorite::UpdateFolder($id, array('NAME' => substr($name, 0, $_REQUEST['maxchars'])));
		}
	}
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
?>