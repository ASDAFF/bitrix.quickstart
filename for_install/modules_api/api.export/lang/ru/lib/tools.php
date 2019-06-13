<?php

//tab2
$MESS['AELT_STOP_WORDS'] = 'скидка|распродажа|дешевый|подарок|бесплатно|акция|специальная цена|только|новинка|new|аналог|заказ|хит|предупреждение';
$MESS['AELT_CURRENCIES'] = array(
	 'RUB' => 'Российский рубль',
	 'USD' => 'Доллар США',
	 'EUR' => 'Евро',
	 'UAH' => 'Гривна',
	 'BYR' => 'Белорусский рубль',
	 'KZT' => 'Тенге',
);

$MESS['AELT_CURRENCY_RATES'] = array(
	 '1'    => '(SITE) внутренний курс сайта',
	 'CBRF' => '(CBRF) курс Центрального банка РФ',
	 'NBU'  => '(NBU) курс Национального банка Украины',
	 'NBK'  => '(NBK) курс Национального банка Казахстана',
	 'СВ'   => '(СВ) курс банка в партнерском интерфейсе',
);

$MESS['AELT_OPTIMAL_RICE'] = array(
	 0 => 'Оптимальная',
);


$MESS['AYI_OFFER_FIELDS_LANG'] = array(
	 'ID'                  => 'Уникальный идентификатор',
	 'XML_ID'              => 'Внешний код из 1С',
	 'CODE'                => 'Символьный код',
	 'NAME'                => 'Название элемента',
	 'IBLOCK_ID'           => 'ID инфоблока',
	 'IBLOCK_CODE'         => 'Символический код инфоблока',
	 'ACTIVE'              => 'Флаг активности (Y|N).',
	 'DATE_CREATE'         => 'Дата создания элемента',
	 'ACTIVE_FROM'         => 'Дата начала активности',
	 'ACTIVE_TO'           => 'Дата окончания активности',
	 'SORT'                => 'Сортировка',
	 'SEARCHABLE_CONTENT'  => 'Содержимое для поиска',
	 //'CREATED_USER_NAME'  => 'Имя пользователя, создавшего элемент',
	 'TIMESTAMP_X'         => 'Время последнего изменения',
	 'MODIFIED_BY'         => 'Код пользователя, изменившего элемент',
	 //'USER_NAME'        => 'Имя пользователя, в последний раз изменившего элемент',
	 'PREVIEW_TEXT'        => 'Описание анонса',
	 'PREVIEW_PICTURE'     => 'Изображение анонса',
	 'DETAIL_TEXT'         => 'Детальное описание',
	 'DETAIL_PICTURE'      => 'Детальное изображение',
	 'IBLOCK_SECTION_ID'   => 'ID раздела',
	 'IBLOCK_SECTION_NAME' => 'Название раздела',
	 'LIST_PAGE_URL'       => 'Ссылка на страницу списка',
	 'DETAIL_PAGE_URL'     => 'Ссылка на детальную страницу',
	 'SHOW_COUNTER'        => 'Количество показов',
	 'TAGS'                => 'Теги',
	 //'LOCK_STATUS'     => 'Текущее состояние блокированности на редактирование элемента',
	 //'WF_STATUS_ID'    => 'Код статуса элемента в документообороте',
	 //'WF_COMMENTS'     => 'Комментарий администратора документооборота',
	 //'LINK_ELEMENT_ID' => 'Идентификатор товара',
);

$MESS['AYI_IPROPERTY_FIELDS_LANG'] = array(
	//Настройки для разделов
	//'SECTION_META_TITLE'                 => '',
	//'SECTION_META_KEYWORDS'              => '',
	//'SECTION_META_DESCRIPTION'           => '',
	//'SECTION_PAGE_TITLE'                 => '',

	//'SECTION_PICTURE_FILE_ALT'   => '',
	//'SECTION_PICTURE_FILE_TITLE' => '',
	//'SECTION_PICTURE_FILE_NAME'  => '',

	//'SECTION_DETAIL_PICTURE_FILE_ALT'   => '',
	//'SECTION_DETAIL_PICTURE_FILE_TITLE' => '',
	//'SECTION_DETAIL_PICTURE_FILE_NAME'  => '',

	array(
		 'NAME'   => 'Настройки для элементов',
		 'VALUES' => array(
				'ELEMENT_META_TITLE'       => 'Шаблон META TITLE',
				'ELEMENT_META_KEYWORDS'    => 'Шаблон META KEYWORDS',
				'ELEMENT_META_DESCRIPTION' => 'Шаблон META DESCRIPTION',
				'ELEMENT_PAGE_TITLE'       => 'Заголовок товара',
		 ),
	),

	array(
		 'NAME'   => 'Настройки для картинок анонса элементов',
		 'VALUES' => array(
				'ELEMENT_PREVIEW_PICTURE_FILE_ALT'   => 'Шаблон ALT',
				'ELEMENT_PREVIEW_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
				'ELEMENT_PREVIEW_PICTURE_FILE_NAME'  => 'Шаблон имени файла',
		 ),
	),

	array(
		 'NAME'   => 'Настройки для детальных картинок элементов',
		 'VALUES' => array(
				'ELEMENT_DETAIL_PICTURE_FILE_ALT'   => 'Шаблон ALT',
				'ELEMENT_DETAIL_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
				'ELEMENT_DETAIL_PICTURE_FILE_NAME'  => 'Шаблон имени файла',
		 ),
	),
);


$MESS['AYI_CATALOG_FIELDS_LANG'] = array(
	//'TYPE'       => 'Тип товара',
	'AVAILABLE'    => 'Доступен для покупки (Y/N)',
	//'MEASURE'    => 'Единица измерения (5)',
	//'VAT_ID'     => 'Код ставки НДС (ID)',
	'VAT_RATE'     => 'Ставка НДС (18.00)',
	'VAT_INCLUDED' => 'НДС включен в цену (Y/N)',
	'QUANTITY'     => 'Доступное количество (100)',
	//'QUANTITY_TRACE'  => 'Флаг "уменьшать ли количество при заказе" (Y/N)',
	//'CAN_BUY_ZERO'    => 'Разрешена ли покупка при отсутствии товара (Y/N)',
	//'SUBSCRIPTION'    => 'Разрешение/запрет подписки при отсутствии товара (Y/N/D)',
	'WEIGHT'       => 'Вес (кг)',
	'LENGTH'       => 'Длина (мм)',
	'WIDTH'        => 'Ширина (мм)',
	'HEIGHT'       => 'Высота (мм)',
	'DIMENSIONS'   => 'Габариты (Д/Ш/В)',
	'GROUP_ID'     => 'Идентификатор группы товаров',
);

$MESS['AYI_PRICE_FIELDS_LANG'] = Array(
	//'PRICE_TYPE_ID'        => 1,
	//'QUANTITY_FROM'        => 'Минимальное количество товара, на которое распространяется предложение',
	//'QUANTITY_TO'          => 'Максимальное количество товара, на которое распространяется предложение',
	//'QUANTITY_HASH'        => 'ZERO-INF',
	//'CURRENCY'             => 'RUB',
	//'BASE_PRICE'           => 2500,
	//'UNROUND_PRICE'        => 1250,
	//'PRICE'                => 1250,
	//'DISCOUNT'             => 1250,
	'PERCENT'                => 'Процент скидки (50)',
	//'PRINT_BASE_PRICE'     => '2 500 ?',
	'RATIO_BASE_PRICE'       => 'Базовая цена (2500)',
	'PRINT_RATIO_BASE_PRICE' => 'Базовая цена (2 500 руб)',
	//'PRINT_PRICE'          => '1 250 ?',
	'RATIO_PRICE'            => 'Цена со скидкой (1250) +++',
	'PRINT_RATIO_PRICE'      => 'Цена со скидкой (1250 руб)',
	//'PRINT_DISCOUNT'       => '1 250 ?',
	'RATIO_DISCOUNT'         => 'Сумма скидки (1250)',
	'PRINT_RATIO_DISCOUNT'   => 'Сумма скидки (1250 руб)',
	//'PRINT_VAT'            => 0 ?,
	//'RATIO_VAT'            => 0,
	//'PRINT_RATIO_VAT'      => 1,
	//'MIN_QUANTITY'         => 1,
	'OLD_PRICE'              => 'Старая цена (2500) +++',
	'PRINT_OLD_PRICE'        => 'Старая цена (2 500 руб)',
);

$MESS['AELT_FIELD_TYPE_SELECT'] = array(
	 'NONE'           => '(не выбрано)',
	 'FIELD'          => 'Поле элемента',
	 'PROPERTY'       => 'Свойство элемента',
	 'OFFER_FIELD'    => 'Поле ТП',
	 'OFFER_PROPERTY' => 'Свойство ТП',
	 'PRODUCT'        => 'Товар',
	 'PRICE'          => 'Цена',
	 'CURRENCY'       => 'Валюта',
	 'IPROPERTY'      => 'Мета-тег',
);