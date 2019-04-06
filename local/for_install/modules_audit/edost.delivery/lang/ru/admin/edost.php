<?
$MESS ['EDOST_ADMIN_TITLE'] = 'eDost: печать бланков';

$MESS ['EDOST_ADMIN_MENU_MAIN'] = 'Заказы';
$MESS ['EDOST_ADMIN_MENU_SETTING'] = 'Настройки';
$MESS ['EDOST_ADMIN_HISTORY_HEAD'] = 'Последние операции:';
$MESS ['EDOST_ADMIN_ORDER_ALLOW_DELIVERY_HEAD'] = 'Заказы почтой, оформленные за последние ';
$MESS ['EDOST_ADMIN_ORDER_MANUAL_PRINT_HEAD'] = 'Ручная печать бланков:';
$MESS ['EDOST_ADMIN_ORDER_MANUAL_PRINT_HINT'] = 'При ручной печати бланк не проверяется на соответсвие способам доставки и оплаты заказа - печатается, как есть, по списку бланков с галочкой.';

$MESS ['EDOST_ADMIN_NO_ORDER_ACTIVE'] = 'Не выделено ни одного заказа!';
$MESS ['EDOST_ADMIN_NO_DOC'] = 'Не найдено подходящих бланков для выделенных заказов!';
$MESS ['EDOST_ADMIN_NO_ORDER'] = 'Не найдено заказов, соответствующих заданным условиям';

$MESS ['EDOST_ADMIN_FIND_HEAD'] = 'Поиск заказов по кодам: ';
$MESS ['EDOST_ADMIN_FIND_HINT'] = 'Коды можно указывать через запятую (1,2,10,20, ...),<br> интервал кодов через тире ("10-20" или "10-" или "-20")';
$MESS ['EDOST_ADMIN_FIND'] = 'Найти';


$MESS ['EDOST_ADMIN_SIGN'] = array(
	'msk' => 'Москва', 'spb' => 'Санкт-Петербург', 'rub' => ' руб.', 'kop' => ' коп.', 'list' => 'лист ', 'quantity' => ' шт.', 'order' => '№ ',
	'total' => 'всего: ', 'total2' => 'всего', 'delivery' => 'Доставка', 'loading' => 'Загрузка...', 'loading_history' => 'Обновление истории...',
	'change' => 'изменить',
);

$MESS ['EDOST_ADMIN_107_INFO'] = array(
	'посылку',
	'бандероль 1-го класса',
	'с объявленной ценностью',
	'с наложенным платежом',
);

$MESS ['EDOST_ADMIN_ORDER_FLAG'] = array(
	'PAYED' => array('name' => '<span style="color: #F70;"><b>Оплачен</b></span>', 'value' => 'Y'),
	'CANCELED' => array('name' => '<span style="color: #A00;"><b>Отменен</b></span>', 'value' => 'Y'),
	'ALLOW_DELIVERY' => array('name' => '<span style="color: #B122B5;"><b>Доставка разрешена</b></span>', 'value' => 'Y'),
	'MARKED' => array('name' => '<span style="color: #F00;"><b>Проблемы</b></span>', 'value' => 'Y'),
	'DEDUCTED' => array('name' => '<span style="color: #088;"><b>Отгружен</b></span>', 'value' => 'Y'),
);

$MESS ['EDOST_ADMIN_RENAME'] = array(
	array('Почта России (отправление 1-го класса)', 'Почта (1-й класс)'),
	array('Почта России (наземная посылка)', 'Почта (посылка)'),
);

$MESS ['EDOST_ADMIN_BUTTON'] = array(
	'print' => array('name' => 'Создать почтовые бланки', 'status' => ' и присвоить заказам статус', 'deducted' => ' и отгрузить заказы', 'status_deducted' => ', отгрузить и присвоить заказам статус'),
	'history' => array('name' => 'Показать заказы', 'print' => 'Распечатать'),
	'show_order' => 'Показать заказы почтой, оформленные за последние ',
	'update' => 'Обновить',
	'check' =>  array('Y' => 'Выделить', 'N' => 'Сбросить'),
);

$MESS ['EDOST_ADMIN_SETTING'] = array(
	'status_no_change' => 'Не изменять',
	'status' => 'После печати бланков, присвоить заказам статус:',
	'cod' => 'Способ оплаты, соотвествующий наложенному платежу:',

	'insurance_107' => 'Печатать опись (ф.107) для отправлений "со страховкой" <span style="color: #888; font-weight: normal;">(по умолчанию опись печатается только при наложке)</span>',
	'duplex' => 'Для двухсторонних документов вторую сторону печатать в обратном порядке <span style="color: #888; font-weight: normal;">(без галочки [1,2,3], с галочкой [3,2,1])</span>',

	'show_order_id' => 'Добавить номер заказа в правый-верхний угол бланков',
	'info_color_head' => 'цвет: ',
	'info_color' => array(array('Черный', '000'), array('Серый', '888'), array('Светло-серый', 'AAA'), array('Почти белый', 'DDD'), array('Желтый', 'FF0'), array('Зеленый', '0F0'), array('Голубой', '0AF')),

	'browser_head' => 'Ваш интернет браузер:',
	'browser' => array('ie' => 'Internet Explorer', 'firefox' => 'Firefox', 'opera' => 'Opera', 'chrome' => 'chrome', 'yandex' => 'Яндекс.Браузер'),

	'filter_days' => array('1' => '24 часа', '2' => '2 дня', '5' => '5 дней', '10' => '10 дней', '30' => '30 дней', '60' => '2 месяца'),
	'duplex_x' => array('Поправка для двухсторонних документов:', ' мм', 'Эту поправку необходимо указывать, когда не сходится линия разреза у двухсторонних бланков (причина в различной ширине бумаги).<br><br>Значение может быть отрицательным.<br><br>Также учитывайте, что бывают "перекосы" в печати, вызванные техническими ограничениями принтера, поэтому обеспечить 100% точность зачастую невозможно.'),

	'passport_head' => 'Документ, предъявляемый при отправке посыки (для ф.116):',
	'passport' => array(
		array('name' => 'Название: ', 'width' => '160', 'max' => '10', 'default' => 'паспорт'),
		array('name' => ', серия ', 'width' => '45', 'max' => '8'),
		array('name' => ' № ', 'width' => '65', 'max' => '8'),
		array('name' => ', дата выдачи: ', 'width' => '100', 'max' => '11'),
		array('name' => ' 20', 'width' => '30', 'max' => '2'),
		array('name' => ' г.<div style="height: 3px;"></div>Наименование учреждения, выдавшего документ: ', 'width' => '400', 'max' => '55'),
	),

	'show_allow_delivery' => 'Показывать только разрешенные к доставке заказы',
	'hide_deducted' => 'Скрывать откруженные заказы',
	'deducted' => 'После печати бланков, отгрузить заказы',
	'hide_unpaid' => 'Скрывать заказы без наложенного платежа, если они не оплачены',
	'hide_without_doc' => array('name' => 'Скрывать заказы, для которых не найдено подходящих бланков', 'mark' => '<span style="color: #F00; font-weight: bold;">Нет подходящих бланков</span>'),
	'show_status' => 'Показывать заказы только для отмеченных статусов:',
	'docs_disable' => 'Заблокировать печать отмеченных документов:',

	'save' => 'Сохранить настройки',
);


$MESS ['EDOST_ADMIN_SHOP_WARNING'] = '<b style="color: #F00;">Предупреждение!!!</b><br> <b>Для печати бланков должны быть указаны "Данные магазина"
в <a href="sale_report_edit.php">Настройках печатных форм</a></b><br>
Подробнее смотрите <a href="http://edost.ru/kln/help-bitrix11.html#10" target="_blank">здесь</a>
';

$MESS ['EDOST_ADMIN_DOC_WARNING'] = '<b style="color: #F00;">Предупреждение!!!</b><br> <b>Не найдено ни одного бланка - печать документов невозможна!<br>
Проверьте, чтобы в настройках <a href="sale_delivery_handler_edit.php?SID=edost">модуля eDost</a> были заданы ид и пароль, а также оплачено продление сервиса.</b><br>
';

?>