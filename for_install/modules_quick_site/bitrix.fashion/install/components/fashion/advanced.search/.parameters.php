<?
    CModule::IncludeModule('iblock');

        $arComponentParameters = array(
        'GROUPS' => array(
            /*'CACHE_PARAMS' => array(
                'NAME' => 'Кеширование',
            ),*/
            'NAV_PARAMS' => array(
                'NAME' => 'Постраничная навигация',
            ),
        ),
        'PARAMETERS' => array(
			'SEARCH_IN_CATALOG' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Искать по каталогам',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            'SEARCH_IN_CONTENT' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Искать по контенту',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            'SEARCH_SEPARATELY' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Разбить результаты поиска на группы',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            
            'CHECK_IN_BASKET' => array(
                'PARENT' => 'BASE',
                'NAME' => 'При выборке товара из каталога проверять его наличие в корзине',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            
            'NEED_NAV' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Разбивать результат поиска на страницы',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            'NAV_CATALOG' => array(
                'PARENT' => 'NAV_PARAMS',
                'NAME' => 'Число результатов на странице каталога',
                'TYPE' => 'STRING',
                'DEFAULT' => '12',
            ),
            'NAV_CONTENT' => array(
                'PARENT' => 'NAV_PARAMS',
                'NAME' => 'Число результатов на странице контента',
                'TYPE' => 'STRING',
                'DEFAULT' => '12',
            ),
            'NAV_TEMPLATE' => array(
                'PARENT' => 'NAV_PARAMS',
                'NAME' => 'Шаблон ссылок для постраничной навигации',
                'TYPE' => 'STRING',
                'DEFAULT' => '',
            ),
            'SORT_CATALOG' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Поле для сортировки по каталогу',
                'TYPE' => 'STRING',
                'DEFAULT' => '',
            ),
            'SORT_DIRECT' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Направление сортировки',
                'TYPE' => 'STRING',
                'DEFAULT' => 'asc',
            ),
            
            'SORT_CONTENT' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Поле для сортировки по контенту',
                'TYPE' => 'STRING',
                'DEFAULT' => '',
            ),
            'SORT_CONTENT_DIRECT' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Направление сортировки',
                'TYPE' => 'STRING',
                'DEFAULT' => '',
            ),
            
            'CATALOG_LIMIT' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Количество элементов каталога на первой странице выдачи',
                'TYPE' => 'STRING',
                'DEFAULT' => '4',
            ),
            'CONTENT_LIMIT' => array(
                'PARENT' => 'BASE',
                'NAME' => 'Количество элементов контента на первой странице выдачи',
                'TYPE' => 'STRING',
                'DEFAULT' => '5',
            ),
        ),
    );
?>
