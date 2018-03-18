<?php
/**
 * Работа с фильтром логов на странице панели управления.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */

// подключим все необходимые файлы:
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php'); // первый общий пролог

if (!CModule::IncludeModule('uniteller.sale')) {
	return;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/include.php'); // инициализация модуля
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/prolog.php'); // пролог модуля

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight('uniteller.sale');
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == 'D') {
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

$sTableID = 'tbl_uniteller_agent'; // ID таблицы

$oSort = new CAdminSorting($sTableID, 'ID', 'desc'); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка

// ******************************************************************** //
//                           ФИЛЬТР                                     //
// ******************************************************************** //

// *********************** CheckFilter ******************************** //
// проверку значений фильтра для удобства вынесем в отдельную функцию
function CheckFilter() {
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) {
		global $$f;
	}

	// В данном случае проверять нечего.
	// В общем случае нужно проверять значения переменных $find_имя
	// и в случае возниконовения ошибки передавать ее обработчику
	// посредством $lAdmin->AddFilterError('текст_ошибки').

	return count($lAdmin->arFilterErrors) == 0; // если ошибки есть, вернем false;
}
// *********************** /CheckFilter ******************************* //

// опишем элементы фильтра
$FilterArr = Array(
	'find',
	'find_type',
	'find_id',
	'find_order_id',
	'find_insert_datatime',
	'find_type_error',
	'find_text_error',
);

// инициализируем фильтр
$lAdmin->InitFilter($FilterArr);

// если все значения фильтра корректны, обработаем его
if (CheckFilter()) {
	// создадим массив фильтрации для выборки CUnitellerAgentLog::GetList() на основе значений фильтра
	$arFilter = array(
		'ID'              => ($find != '' && $find_type == 'id' ? $find : $find_id),
		'ORDER_ID'        => ($find != '' && $find_type == 'order_id' ? $find: $find_order_id),
		'INSERT_DATATIME' => $find_insert_datatime,
		'TYPE_ERROR'      => ($find != '' && $find_type == 'type_error' ? $find: $find_type_error),
		'TEXT_ERROR'      => $find_text_error,
	);
}

// ******************************************************************** //
//                ОБРАБОТКА ДЕЙСТВИЙ НАД ЭЛЕМЕНТАМИ СПИСКА              //
// ******************************************************************** //

// обработка одиночных и групповых действий
if (($arID = $lAdmin->GroupAction()) && $POST_RIGHT == 'W') {
	// если выбрано 'Для всех элементов'
	if ($_REQUEST['action_target'] == 'selected') {
		$cData = new CUnitellerAgentLog;
		$rsData = $cData->GetList(array($by => $order), $arFilter);
		while ($arRes = $rsData->Fetch()) {
			$arID[] = $arRes['ID'];
		}
	}

	// пройдем по списку элементов
	foreach ($arID as $ID) {
		if (strlen($ID) <= 0) {
			continue;
		}
		$ID = IntVal($ID);

		// для каждого элемента совершим требуемое действие
		switch ($_REQUEST['action']) {
			// удаление
			case 'delete':
				@set_time_limit(0);
				$DB->StartTransaction();
				if (!CUnitellerAgentLog::Delete($ID)) {
					$DB->Rollback();
					$lAdmin->AddGroupError(GetMessage('UNITELLER.AGENT_DEL_ERROR'), $ID);
				}
				$DB->Commit();
				break;
		}
	}
}

// ******************************************************************** //
//                ВЫБОРКА ЭЛЕМЕНТОВ СПИСКА                              //
// ******************************************************************** //

// выберем список рассылок
$cData = new CUnitellerAgentLog;
$rsData = $cData->GetList(array($by => $order), $arFilter);

// преобразуем список в экземпляр класса CAdminResult
$rsData = new CAdminResult($rsData, $sTableID);

// аналогично CDBResult инициализируем постраничную навигацию.
$rsData->NavStart();

// отправим вывод переключателя страниц в основной объект $lAdmin
$lAdmin->NavText($rsData->GetNavPrint(GetMessage('UNITELLER.AGENT_NAV')));

// ******************************************************************** //
//                ПОДГОТОВКА СПИСКА К ВЫВОДУ                            //
// ******************************************************************** //

$lAdmin->AddHeaders(array(
array(
		'id'      => 'ID',
		'content' => 'ID',
		'sort'    => 'id',
		'align'   => 'left',
		'default' => true,
),
array(
		'id'      => 'ORDER_ID',
		'content' => GetMessage('UNITELLER.AGENT_ORDER_ID'),
		'sort'    => 'order_id',
		'align'   => 'left',
		'default' => true,
),
array(
		'id'      => 'INSERT_DATATIME',
		'content' => GetMessage('UNITELLER.AGENT_INSERT_DATATIME'),
		'sort'    => 'insert_datatime',
		'align'   => 'left',
		'default' => true,
),
array(
		'id'      => 'TYPE_ERROR',
		'content' => GetMessage('UNITELLER.AGENT_TYPE_ERROR'),
		'sort'    => 'type_error',
		'align'   => 'left',
		'default' => true,
),
array(
		'id'      => 'TEXT_ERROR',
		'content' => GetMessage('UNITELLER.AGENT_TEXT_ERROR'),
		'sort'    => 'text_error',
		'align'   => 'left',
		'default' => true,
),
));

while ($arRes = $rsData->NavNext(true, 'f_')) {
	// создаем строку. результат - экземпляр класса CAdminListRow
	$row =& $lAdmin->AddRow($f_ID, $arRes);

	// сформируем контекстное меню
	$arActions = Array();

	// удаление элемента
	if ($POST_RIGHT >= 'W') {
		$arActions[] = array(
			'ICON'   => 'delete',
			'TEXT'   => GetMessage('UNITELLER.AGENT_LOGS_DEL'),
			'ACTION' => "if(confirm('" . GetMessage('UNITELLER.AGENT_DEL_CONF') . "')) " . $lAdmin->ActionDoGroup($f_ID, 'delete'),
		);
	}

	// вставим разделитель
	$arActions[] = array('SEPARATOR' => true);

	// если последний элемент - разделитель, почистим мусор.
	if(is_set($arActions[count($arActions) - 1], 'SEPARATOR')) {
		unset($arActions[count($arActions) - 1]);
	}

	// применим контекстное меню к строке
	$row->AddActions($arActions);
}

// резюме таблицы
$lAdmin->AddFooter(array(
	array('title' => GetMessage('MAIN_ADMIN_LIST_SELECTED'), 'value' => $rsData->SelectedRowsCount()), // кол-во элементов
	array('counter' => true, 'title' => GetMessage('MAIN_ADMIN_LIST_CHECKED'), 'value' => '0'), // счетчик выбранных элементов
));

// групповые действия
$lAdmin->AddGroupActionTable(array(
	'delete' => GetMessage('MAIN_ADMIN_LIST_DELETE'), // удалить выбранные элементы
));

// ******************************************************************** //
//                АДМИНИСТРАТИВНОЕ МЕНЮ                                 //
// ******************************************************************** //

// прикрепим его к списку
$lAdmin->AddAdminContextMenu();

// добавим ссылку на помощь.
$APPLICATION->SetAdditionalCSS('/bitrix/themes/' . ADMIN_THEME_ID . '/sysupdate.css'); // картинка для логов
$arMenu = array(
	array(
		'TEXT' => GetMessage('UNITELLER.SALE_BTN_HELP'),
		'LINK' => '/bitrix/admin/settings.php?lang=' . LANGUAGE_ID . '&mid=uniteller.sale&mid_menu=1',
		'ICON' => 'btn_update_log',
	),
);
$lAdmin->AddAdminContextMenu($arMenu);


// ******************************************************************** //
//                ВЫВОД                                                 //
// ******************************************************************** //

// альтернативный вывод
$lAdmin->CheckListMode();

// установим заголовок страницы
$APPLICATION->SetTitle(GetMessage('UNITELLER.AGENT_LOGS_TITLE'));

// не забудем разделить подготовку данных и вывод
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

// ******************************************************************** //
//                ВЫВОД ФИЛЬТРА                                         //
// ******************************************************************** //

// создадим объект фильтра
$oFilter = new CAdminFilter(
	$sTableID . '_filter',
	array(
		'ID',
		GetMessage('UNITELLER.AGENT_F_ORDER_ID'),
		GetMessage('UNITELLER.AGENT_F_INSERT_DATATIME'),
		GetMessage('UNITELLER.AGENT_F_TYPE_ERROR'),
		GetMessage('UNITELLER.AGENT_F_TEXT_ERROR'),
	)
);

?>
<form name="find_form" method="get" action="<?= $APPLICATION->GetCurPage() ?>">
	<? $oFilter->Begin(); ?>
	<tr>
		<td><b><?= GetMessage('UNITELLER.AGENT_FIND') ?>:</b></td>
		<td><input type="text" size="25" name="find" value="<?= htmlspecialchars($find) ?>" title="<?= GetMessage('UNITELLER.AGENT_F_FIND_TYTLE') ?>">
<?php

$arr = array(
	'reference' => array(
		'ID',
		GetMessage('UNITELLER.AGENT_F_ORDER_ID'),
		GetMessage('UNITELLER.AGENT_F_TYPE_ERROR'),
	),
	'reference_id' => array(
		'id',
		'order_id',
		'type_error',
	),
);
echo SelectBoxFromArray('find_type', $arr, $find_type, '', '');

?>
		</td>
	</tr>
	<tr>
		<td><?= 'ID' ?>:</td>
		<td><input type="text" name="find_id" size="47" value="<?= htmlspecialchars($find_id) ?>"></td>
	</tr>
	<tr>
		<td><?= GetMessage('UNITELLER.AGENT_F_ORDER_ID') ?>:</td>
		<td><input type="text" name="find_order_id" size="47" value="<?= htmlspecialchars($find_order_id) ?>"></td>
	</tr>
	<tr>
		<td><?= GetMessage('UNITELLER.AGENT_F_INSERT_DATATIME') ?>:</td>
		<td><input type="text" name="find_insert_datatime" size="47" value="<?= htmlspecialchars($find_insert_datatime) ?>"></td>
	</tr>
	<tr>
		<td><?= GetMessage('UNITELLER.AGENT_F_TYPE_ERROR') ?>:</td>
		<td>
<?php

$arr = array();
$rsDataType = $cData->GetTypeList();
while ($ar = $rsDataType->Fetch()) {
	$arr['reference'][] = $ar['TYPE_ERROR'];
	$arr['reference_id'][] = $ar['TYPE_ERROR'];
}
echo SelectBoxFromArray('find_type_error', $arr, $find_type_error, GetMessage('POST_ALL'), '');

?>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage('UNITELLER.AGENT_F_TEXT_ERROR') ?>:</td>
		<td><input type="text" name="find_text_error" size="47" value="<?= htmlspecialchars($find_text_error) ?>"></td>
	</tr>
<?php

$oFilter->Buttons(array('table_id' => $sTableID, 'url' => $APPLICATION->GetCurPage(), 'form' => 'find_form'));
$oFilter->End();

?>
</form>
<?php

// выведем таблицу списка элементов
$lAdmin->DisplayList();

// завершение страницы
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');

?>