<?
	namespace Webprofy\Bitrix\IBlock;

    use Webprofy\Bitrix\Getter;
	use Webprofy\Bitrix\IBlockEntity;
	use WP;

	class IBlock extends IBlockEntity{
		function __construct($id){
			parent::__construct(intval($id), null);
		}

		protected function createData(){
			return WP::bit(array(
				'of' => 'iblock',
				'f' => array(
					'ID' => $this->id
				),
				'one' => 'f'
			));
		}

		function isDefault(){
			return $this->f('ID') == 300;
		}

		function getName(){
			return $this->f('NAME');
		}

		function getAttributes($of, $type){
			$class = Getter::get(array(
				'of' => $of,
			))->getEntityGetter()->getObjectClass();

			$object = new $class(null, $this);
			return $object->getAttributes($type);
		}

		function itemsByAttributes($attributes, $moreBit = null, $type = 'elements'){
			$bit = array(
				'of' => 'e',
				'f' => array()
			);

			$bit['f'] = $attributes->getSelectFields();
			$bit['f']['iblock'] = $this->id;

			if(empty($moreBit)){
				$moreBit = array(
					'sort' => 'ID',
					'object' => true,
					'map' => function($d, $f, $p){
						$element = new Element($f['ID']);
						$element->setData($f, $p);
						return $element;
					}
				);
			}
			$bit = array_merge($bit, $moreBit);
			return WP::bit($bit);
		}

// NEXT SHIT NEEDS TO BE REFACTORED:

		// public static function totalElements($id, $sectionID = 0){
		// 	return 0 + \CIBlockElement::GetList(array(), array(
		// 		'IBLOCK_ID' => $iblock,
		// 		'SECTION_ID' => $section
		// 	), array());
		// }

	   // private static $default = array(
	   //    'fields' => array(
	   //       "ACTIVE" => "Y",
	   //       "NAME" => "Каталог товаров",
	   //       "CODE" => "catalog",
	   //       "IBLOCK_TYPE_ID" => "s1", // Тип инфоблока
	   //       "SITE_ID" => "s1", // ID сайта
	   //       "SORT" => "5",
	   //       "GROUP_ID" => array( // Права доступа
	   //          "2" => "R", // Все пользователи RWX
	   //       ),
	   //       "FIELDS" => array(
	   //          "DETAIL_PICTURE" => array(
	   //             "IS_REQUIRED" => "N", // не обязательное
	   //             "DEFAULT_VALUE" => array(
	   //                "SCALE" => "Y", // возможные значения: Y|N. Если равно "Y", то изображение будет отмасштабировано. 
	   //                "WIDTH" => "600", // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля. 
	   //                "HEIGHT" => "600", // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
	   //                "IGNORE_ERRORS" => "Y", // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении "N" будет сгенерирована ошибка. 
	   //                "METHOD" => "resample", // возможные значения: resample или пусто. Значение поля равное "resample" приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов. 
	   //                "COMPRESSION" => "95", // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
	   //             ),
	   //          ),
	   //          "PREVIEW_PICTURE" => array(
	   //             "IS_REQUIRED" => "N", // не обязательное
	   //             "DEFAULT_VALUE" => array(
	   //                "SCALE" => "Y", // возможные значения: Y|N. Если равно "Y", то изображение будет отмасштабировано. 
	   //                "WIDTH" => "140", // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля. 
	   //                "HEIGHT" => "140", // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
	   //                "IGNORE_ERRORS" => "Y", // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении "N" будет сгенерирована ошибка. 
	   //                "METHOD" => "resample", // возможные значения: resample или пусто. Значение поля равное "resample" приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов. 
	   //                "COMPRESSION" => "95", // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
	   //                "FROM_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость генерации картинки предварительного просмотра из детальной. 
	   //                "DELETE_WITH_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость удаления картинки предварительного просмотра при удалении детальной.
	   //                "UPDATE_WITH_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость обновления картинки предварительного просмотра при изменении детальной.
	   //             ),
	   //          ),
	   //          "SECTION_PICTURE" => array(
	   //             "IS_REQUIRED" => "N", // не обязательное
	   //             "DEFAULT_VALUE" => array(
	   //                "SCALE" => "Y", // возможные значения: Y|N. Если равно "Y", то изображение будет отмасштабировано. 
	   //                "WIDTH" => "235", // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля. 
	   //                "HEIGHT" => "235", // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
	   //                "IGNORE_ERRORS" => "Y", // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении "N" будет сгенерирована ошибка. 
	   //                "METHOD" => "resample", // возможные значения: resample или пусто. Значение поля равное "resample" приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов. 
	   //                "COMPRESSION" => "95", // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
	   //                "FROM_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость генерации картинки предварительного просмотра из детальной. 
	   //                "DELETE_WITH_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость удаления картинки предварительного просмотра при удалении детальной.
	   //                "UPDATE_WITH_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость обновления картинки предварительного просмотра при изменении детальной.
	   //             ),
	   //          ),
	   //          // Символьный код элементов
	   //          "CODE" => array(
	   //             "IS_REQUIRED" => "Y", // Обязательное
	   //             "DEFAULT_VALUE" => array(
	   //                "UNIQUE" => "Y", // Проверять на уникальность
	   //                "TRANSLITERATION" => "Y", // Транслитерировать
	   //                "TRANS_LEN" => "30", // Максмальная длина транслитерации
	   //                "TRANS_CASE" => "L", // Приводить к нижнему регистру
	   //                "TRANS_SPACE" => "-", // Символы для замены
	   //                "TRANS_OTHER" => "-",
	   //                "TRANS_EAT" => "Y",
	   //                "USE_GOOGLE" => "N",
	   //                ),
	   //             ),
	   //          // Символьный код разделов
	   //          "SECTION_CODE" => array(
	   //             "IS_REQUIRED" => "Y",
	   //             "DEFAULT_VALUE" => array(
	   //                "UNIQUE" => "Y",
	   //                "TRANSLITERATION" => "Y",
	   //                "TRANS_LEN" => "30",
	   //                "TRANS_CASE" => "L",
	   //                "TRANS_SPACE" => "-",
	   //                "TRANS_OTHER" => "-",
	   //                "TRANS_EAT" => "Y",
	   //                "USE_GOOGLE" => "N",
	   //                ),
	   //             ),
	   //          "DETAIL_TEXT_TYPE" => array(      // Тип детального описания
	   //             "DEFAULT_VALUE" => "html",
	   //             ),
	   //          "SECTION_DESCRIPTION_TYPE" => array(
	   //             "DEFAULT_VALUE" => "html",
	   //             ),
	   //          "IBLOCK_SECTION" => array(         // Привязка к разделам обязательноа
	   //             "IS_REQUIRED" => "Y",
	   //             ),            
	   //          "LOG_SECTION_ADD" => array("IS_REQUIRED" => "Y"), // Журналирование
	   //          "LOG_SECTION_EDIT" => array("IS_REQUIRED" => "Y"),
	   //          "LOG_SECTION_DELETE" => array("IS_REQUIRED" => "Y"),
	   //          "LOG_ELEMENT_ADD" => array("IS_REQUIRED" => "Y"),
	   //          "LOG_ELEMENT_EDIT" => array("IS_REQUIRED" => "Y"),
	   //          "LOG_ELEMENT_DELETE" => array("IS_REQUIRED" => "Y"),
	   //       ),
	         
	   //       // Шаблоны страниц
	   //       "LIST_PAGE_URL" => "#SITE_DIR#/catalog/",
	   //       "SECTION_PAGE_URL" => "#SITE_DIR#/catalog/#SECTION_CODE#/",
	   //       "DETAIL_PAGE_URL" => "#SITE_DIR#/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",         

	   //       "INDEX_SECTION" => "Y", // Индексировать разделы для модуля поиска
	   //       "INDEX_ELEMENT" => "Y", // Индексировать элементы для модуля поиска

	   //       "VERSION" => 1, // Хранение элементов в общей таблице

	   //       "ELEMENT_NAME" => "Товар",
	   //       "ELEMENTS_NAME" => "Товары",
	   //       "ELEMENT_ADD" => "Добавить товар",
	   //       "ELEMENT_EDIT" => "Изменить товар",
	   //       "ELEMENT_DELETE" => "Удалить товар",
	   //       "SECTION_NAME" => "Категории",
	   //       "SECTIONS_NAME" => "Категория",
	   //       "SECTION_ADD" => "Добавить категорию",
	   //       "SECTION_EDIT" => "Изменить категорию",
	   //       "SECTION_DELETE" => "Удалить категорию",

	   //       "SECTION_PROPERTY" => "Y", // Разделы каталога имеют свои свойства (нужно для модуля интернет-магазина)
	   //    ),
	   //    'properties' => array(
	   //       'list' => Array(
	   //          "NAME" => "(список)",
	   //          "ACTIVE" => "Y",
	   //          "SORT" => 500, // Сортировка
	   //          "CODE" => "LIST",
	   //          "PROPERTY_TYPE" => "L", // Список
	   //          "LIST_TYPE" => "C", // Тип списка - "флажки"
	   //          "FILTRABLE" => "Y", // Выводить на странице списка элементов поле для фильтрации по этому свойству
	   //          "VALUES" => array(
	   //             "VALUE" => "да",
	   //          ),
	   //       ),
	   //       'file' => Array(
	   //          "NAME" => "(файл)",
	   //          "ACTIVE" => "Y",
	   //          "MULTIPLE" => "Y",
	   //          "SORT" => 500,
	   //          "CODE" => "PHOTO",
	   //          "PROPERTY_TYPE" => "F", // Файл
	   //          // "FILE_TYPE" => "jpg, gif, bmp, png, jpeg",   
	   //          "HINT" => "Допускается произвольное число дополнительных фотографий. Добавьте одну, и появится поле для добавленя следующей.",
	   //       ),
	   //       'element' => Array(
	   //          "NAME" => "(элемент)",
	   //          "ACTIVE" => "Y",
	   //          "MULTIPLE" => "Y",
	   //          "SORT" => 500,
	   //          "CODE" => "ELEMENT",
	   //          "MULTIPLE_CNT" => 2, // Количество свойств, предлагаемых по умолчанию
	   //          "PROPERTY_TYPE" => "E", // Привязка к элементам инфоблока
	   //          "LINK_IBLOCK_ID" => 0,
	   //          "HINT" => "Данные товары будут показываться дла этого товара как рекомендуемые на странице просмотра товара",
	   //       ),
	   //       'string' => Array(
	   //          "NAME" => "(строка)",
	   //          "ACTIVE" => "Y",
	   //          "SORT" => 500,
	   //          "CODE" => "STRING",
	   //          "PROPERTY_TYPE" => "S", // Строка
	   //          "ROW_COUNT" => 1, // Количество строк
	   //          "COL_COUNT" => 60, // Количество столбцов
	   //          "HINT" => "Если задан - то заголовок для товара будет подставляться из этой строчки",
	   //       )
	   //    ),
	   //    'sectionProperties' => array(
	   //       'string' => array(
	   //          "FIELD_NAME" => "UF_SEO_TITLE",
	   //          "USER_TYPE_ID" => "string",
	   //          "XML_ID" => "",
	   //          "SORT" => 500,
	   //          "MULTIPLE" => "N", // Множественное
	   //          "MANDATORY" => "N", // Обязательное 
	   //          "SHOW_FILTER" => "S",
	   //          "SHOW_IN_LIST" => "Y",
	   //          "EDIT_IN_LIST" => "Y",
	   //          "IS_SEARCHABLE" => "N",
	   //          "SETTINGS" => array(
	   //                "SIZE" => "70", // длина поля ввода
	   //                "ROWS" => "1" // высота поля ввода
	   //             ),
	   //       )
	   //    )
	   // );

	   // static function getPropertiesCount($ID){
	   //    return \CIBlockProperty::GetList(
	   //       array(),
	   //       array(
	   //          "IBLOCK_ID" => $ID
	   //       )
	   //    )->SelectedRowsCount();
	   // }

	   // private static function addSectionProperty($ID, $data){
	   //    $o = new CUserTypeEntity();
	   //    $type = 'string'; //$data['type'];
	   //    if($fields_ = self::$default['sectionProperties'][$type]){
	   //       $fields = array_merge($fields_, $data['values']);
	   //    }
	   //    else{
	   //       $fields = $data['values'];
	   //    }
	   //    $name = $data['name'];
	   //    $fields = array_merge($fields, array(
	   //       "ENTITY_ID" => "IBLOCK_".$ID."_SECTION",
	   //       "EDIT_FORM_LABEL" => array("ru" => $name, "en" => ""),
	   //       "LIST_COLUMN_LABEL" => array("ru" => $name, "en" => ""),
	   //       "LIST_FILTER_LABEL" => array("ru" => $name, "en" => ""),
	   //    ));
	   //    return $o->Add($fields);
	   // }
	   // private static function addSectionProperties($ID, $data){
	   //    foreach($data as $data_){
	   //       self::addSectionProperty($ID, $data_);
	   //    }
	   // }

	   // private static function activateCatalog($ID){
	   //    return CCatalog::GetByID($ID) ? 0 : CCatalog::Add(array(
	   //       "IBLOCK_ID" => $ID,       // код (ID) инфоблока товаров
	   //    // "YANDEX_EXPORT" => "Y",   // экспортировать в Яндекс.Товары с помощью агента
	   //    ));

	   //    /*
	   //       if($ex = $APPLICATION->GetException()){
	   //          $strError = $ex->GetString();
	   //          ShowError($strError);
	   //       }
	   //    */
	   // }

	   // private static function addProperty($ID, $data){
	   //    if($fields_ = self::$default['properties'][$data['type']]){
	   //       $fields = array_merge($fields_, $data['values']);
	   //    }
	   //    else{
	   //       $fields = $data['values'];
	   //    }

	   //    $fields['IBLOCK_ID'] = $ID;
	   //    if(isset($fields['LINK_IBLOCK_ID'])){
	   //       $fields['LINK_IBLOCK_ID'] = $ID;
	   //    }

	   //    $o = new \CIBlockProperty;
	   //    return $o->Add($fields);
	   // }

	   // private static function addProperties($ID, $data){
	   //    foreach($data as $data_){
	   //       self::addProperty($ID, $data_);
	   //    }
	   // }

	   // private static function setEditForm(){
	   //    return;
	   //    // I DONT UNDERSTAND IT.

	   //    $arFormFields = array();
	   //    $strings = array();

	   //    foreach(array(
	   //       array(
	   //          array("edit1", "Товар"), // Название вкладки
	   //          array("ACTIVE", "Активность"),
	   //          array("NAME", "*Название"), // Свойство со звездочкой - помечается как обязательное
	   //          array("CODE", "*Символьный код"),
	   //          array("DETAIL_PICTURE", "Изображение"),
	   //          array("CATALOG", "*Торговый каталог"),
	   //       ),
	   //       array(
	   //          array("edit2", "Подробно"),
	   //          array("IBLOCK_ELEMENT_PROPERTY", "Значения свойств"), // Свойства, которые не выводятся явно
	   //          array("IBLOCK_ELEMENT_PROP_VALUE", "--Особые отличия"), // Для заголовков блоков вначале нужно писать две черточки (--)
	   //          array("PROPERTY_5", "Специальное предолжение"), // Свойство, которое выводится явно
	   //          array("PROPERTY_6", "Новинка"), // Вывод определенного свойства
	   //          array("DETAIL_TEXT", "Детальное описание"),
	   //       ),
	   //       array(
	   //          array("edit3", "Категория"),
	   //          array("SECTIONS", "*Разделы"),
	   //       ),
	   //       array(
	   //          array("edit4", "Торговые предложения"), 
	   //          array("OFFERS", "Торговые предложения"), // Используется в модуле торгового каталога (Интернет-магазин)

	   //       ),
	   //       array(
	   //          array("edit5", "Дополнительно"),
	   //          array("ACTIVE_FROM", "Начало активности"),
	   //          array("ACTIVE_TO", "Окончание активности"),
	   //          array("SORT", "Сортировка"),
	   //          array("TAGS", "Теги"),
	   //          array("PREVIEW_TEXT", "Описание для анонса"),
	   //       ),
	   //    ) as $fields){
	   //       $items = array();
	   //       foreach($fields as $str){
	   //          $items[] = implode('--#--', $str);
	   //       }
	   //       $strings[] = implode('--,--', $items);
	   //    }
	   //    $arSettings = array("tabs" => implode('--;--', $strings));

	   //    return CUserOptions::SetOption(
	   //       "form",
	   //       "form_element_".$ID,
	   //       $arSettings,
	   //       $bCommon=true,
	   //       $userId=false
	   //    );
	   // }

	   // static function create($data = array()){
	   //    $fields = self::$default['fields'];

	   //    if(isset($data['fields'])){
	   //       foreach(self::$default['fields'] as $i => $v){
	   //          if(!isset($data['fields'][$i])){
	   //             continue;
	   //          }
	   //          $fields[$i] = $data['fields'][$i];
	   //       }
	   //    }

	   //    \CModule::IncludeModule('iblock');
	   //    $iblock = new \CIBlock;
	   //    $ID = $iblock->Add($fields);
	   //    if(!$ID){
	   //       return 0;
	   //    }

	   //    if($data['properties'] && self::getPropertiesCount($ID) <= 0){
	   //       self::addProperties($ID, $data['properties']);
	   //    } 

	   //    self::addSectionProperties($ID);
	            
	   //    if($data['catalog-on']){
	   //       self::activateCatalog($ID);
	   //    }

	   //    // self::setEditForm();

	   //    return $ID;
	   // }
	}
?>