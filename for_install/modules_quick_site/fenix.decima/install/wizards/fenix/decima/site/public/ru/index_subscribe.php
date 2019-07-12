<?$APPLICATION->IncludeComponent("bitrix:subscribe.form", "subscribe", Array(
	"USE_PERSONALIZATION" => "N",	// Определять подписку текущего пользователя
	"SHOW_HIDDEN" => "Y",	// Показать скрытые рубрики подписки
	"PAGE" => "#SITE_DIR#personal/subscribe/subscr_edit.php",	// Страница редактирования подписки (доступен макрос #SITE_DIR#)
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "3600",	// Время кеширования (сек.)
	),
	false
);?>