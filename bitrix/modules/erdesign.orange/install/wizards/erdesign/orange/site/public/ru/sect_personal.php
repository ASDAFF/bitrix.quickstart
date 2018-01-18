

<?$APPLICATION->IncludeComponent("bitrix:news.line", "personal", Array(
										"IBLOCK_TYPE" => "service",	// Тип информационного блока
										"IBLOCKS" => "#PERSONAL_IBLOCK_ID#",										
										"NEWS_COUNT" => "10",	// Количество новостей на странице
										"FIELD_CODE" => array(	// Поля
											0 => "NAME",
											1 => "PREVIEW_PICTURE",
										),
										"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
										"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
										"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
										"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
										"DETAIL_URL" => "",	// URL, ведущий на страницу с содержимым элемента раздела
										"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
										"CACHE_TYPE" => "A",	// Тип кеширования
										"CACHE_TIME" => "300",	// Время кеширования (сек.)
										"CACHE_GROUPS" => "Y",	// Учитывать права доступа
										),
										false
									);?>