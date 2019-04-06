<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */
//start
$MESS ['WZ_ONLINEDENGI_START_TITLE'] = 'Мастер получения адреса скрипта, принимающего запрос на зачисление средств в системе OnlineDengi.';
$MESS ['WZ_ONLINEDENGI_START_CONTENT_1'] = 'Для интеграции проекта с платежной системой OnlineDengi необходимо сообщить адрес к скрипту, который будет принимать запрос на зачисление средств. Этот мастер поможет получить этот адрес.';

//GetPaymentParamsStep
$MESS ['WZ_ONLINEDENGI_GetPaymentParamsStep_TITLE'] = 'Выбор обработчика платежной системы';
$MESS ['WZ_ONLINEDENGI_GetPaymentParamsStep_H1_TITLE'] = 'Выберите платежную систему и тип плательщика, для которых нужно получить адрес к скрипту.';
$MESS ['WZ_ONLINEDENGI_GetPaymentParamsStep_ERR1'] = 'Обработчик платежной системы не выбран';

$MESS ['WZ_ONLINEDENGI_REQUIRED'] = ' - поле обязательное для заполнения';
$MESS ['WZ_ONLINEDENGI_SALEMODULE_ERR'] = 'Модуль "Интернет-магазин" не установлен. Продолжение не возможно.';
$MESS ['WZ_ONLINEDENGI_MODULE_ERR'] = 'Модуль платежной системы не установлен. Продолжение не возможно.';
$MESS ['WZ_ONLINEDENGI_MODULE_FILE_ERR'] = 'Служебный файл обработчика платежной системы не найден. Продолжение не возможно.';
$MESS ['WZ_ONLINEDENGI_ACCESS_DENIED_ERR'] = 'Доступ запрещен.';

$MESS ['WZ_ONLINEDENGI_SELECT'] = '-- выберите --';

//Report Step
$MESS ['WZ_ONLINEDENGI_ReportStep_TITLE'] = 'Информация для передачи системе OnlineDengi';
$MESS ['WZ_ONLINEDENGI_ReportStep_CONTENT_1'] = 'Адрес скрипта:<br /> #VALUE#';
$MESS ['WZ_ONLINEDENGI_ReportStep_CONTENT_2'] = 'Идентификатор проекта в системе OnlineDengi:<br /> #VALUE#';
$MESS ['WZ_ONLINEDENGI_ReportStep_CONTENT_3'] = 'Идентификатор владельца внешней формы OnlineDengi:<br /> #VALUE#';
$MESS ['WZ_ONLINEDENGI_ReportStep_CONTENT_4'] = 'Секретный ключ:<br /> #VALUE#';
$MESS ['WZ_ONLINEDENGI_ReportStep_CONTENT_5'] = 'Адрес скрипта успешной оплаты:<br /> #VALUE#';
$MESS ['WZ_ONLINEDENGI_ReportStep_CONTENT_6'] = 'Адрес скрипта неудачной оплаты:<br /> #VALUE#';
$MESS ['WZ_ONLINEDENGI_ReportStep_ERR2'] = 'Сайт не найден.';

//buttons
$MESS ['WZ_ONLINEDENGI_CANCEL_BUTTON_TITLE'] = 'Закрыть';
$MESS ['WZ_ONLINEDENGI_AGAIN_BUTTON_TITLE'] = 'Повторить';

//Cancel step
$MESS ['WZ_ONLINEDENGI_CANCEL_TITLE'] = 'Работа мастера прервана';
$MESS ['WZ_ONLINEDENGI_CANCEL_CONTENT'] = 'Работа мастера прервана.';
$MESS ['WZ_ONLINEDENGI_FINAL_BUTTON_TITLE'] = 'Завершить';

//Final Step
$MESS ['WZ_ONLINEDENGI_FINALSTEP_TITLE'] = 'Работа мастера завершена';
$MESS ['WZ_ONLINEDENGI_FINALSTEP_CONTENT_1'] = 'Работа мастера завершена';

