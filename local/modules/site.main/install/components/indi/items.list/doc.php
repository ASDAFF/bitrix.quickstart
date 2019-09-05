<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$title = "items.list (Список элементов инфоблока)";
$APPLICATION->SetTitle($title);
?>
	<style>
		table {
			font-size: 14px;
			border: 1px solid #333333;
		}

		table .center {
			border-left: 1px solid #333333;
			border-right: 1px solid #333333;
		}

		table td {
			padding: 10px;
		}

		table tr {
			border-bottom: 1px solid #333333;
		}
	</style>


	<h3>Описание</h3><p>Компонент в точности наследующий всю функциональность news.list c меньшим количеством запросов SQL за счет выборки только указанных в параметрах свойств и полей</p>
	<hr/><h3>Пример вызова</h3>
	<pre>
    $APPLICATION->IncludeComponent(
			"site:items.list",
			".default",
			array(
				"COMPONENT_TEMPLATE" => ".default",
				"IBLOCK_TYPE" => "books",
				"IBLOCK_ID" => "6",
				"NEWS_COUNT" => "200",
				"SORT_BY1" => "ACTIVE_FROM",
				"SORT_ORDER1" => "DESC",
				"SORT_BY2" => "SORT",
				"SORT_ORDER2" => "ASC",
				"FILTER_NAME" => "",
				"FIELD_CODE" => array(
					0 => "NAME",
					1 => "PREVIEW_TEXT",
					2 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "FORUM_MESSAGE_CNT",
					1 => "",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"CACHE_TYPE" => "Y",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "Y",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"SET_TITLE" => "Y",
				"SET_BROWSER_TITLE" => "Y",
				"SET_META_KEYWORDS" => "Y",
				"SET_META_DESCRIPTION" => "Y",
				"SET_LAST_MODIFIED" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
				"ADD_SECTIONS_CHAIN" => "Y",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "Новости",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"PAGER_BASE_LINK_ENABLE" => "N",
				"SET_STATUS_404" => "N",
				"SHOW_404" => "N",
				"MESSAGE_404" => "",
			),
			false
		);
    </pre>
	<hr/><h3>Описание параметров</h3>

	<table>
		<tr class="header">
			<td align="center">
				<b>Поле</b>
			</td>
			<td align="center" class="center">
				<b>Параметр</b>
			</td>
			<td align="center">
				<b>Описание</b>
			</td>
		</tr>

		<tr>
			<td>Тип инфоблока</td>
			<td class="center">
				<b>IBLOCK_TYPE</b>
			</td>
			<td>Тип инфоблока</td>
		</tr>
		<tr>
			<td>ID инфоблока</td>
			<td class="center">
				<b>IBLOCK_ID</b>
			</td>
			<td>ID инфоблока.</td>
		</tr>
		<tr>
			<td>Количество элементов</td>
			<td class="center">
				<b>NEWS_COUNT</b>
			</td>
			<td>Количество элементов, на одной странице</td>
		</tr>
		<tr>
			<td>Поле сортировки</td>
			<td class="center">
				<b>SORT_BY1</b>
			</td>
			<td>Поле сортировки для первой сортировки</td>
		</tr>
		<tr>
			<td>Порядок сортировки по первому полю</td>
			<td class="center">
				<b>SORT_ORDER1</b>
			</td>
			<td>Порядок сортировки по первому полю (по возрастанию, убыванию)</td>
		</tr>
		<tr>
			<td>Поле сортировки</td>
			<td class="center">
				<b>SORT_BY2</b>
			</td>
			<td>Поле сортировки для второй сортировки</td>
		</tr>
		<tr>
			<td>орядок сортировки по второму полю</td>
			<td class="center">
				<b>SORT_ORDER2</b>
			</td>
			<td>Порядок сортировки по второму полю (по возрастанию, убыванию)</td>
		</tr>
		<tr>
			<td>Фильтр</td>
			<td class="center">
				<b>FILTER_NAME</b>
			</td>
			<td>Имя переменной, в которой передается фильтр</td>
		</tr>
		<tr>
			<td>Поля</td>
			<td class="center">
				<b>FIELD_CODE</b>
			</td>
			<td>Поля элементов, которые попадут в arResult</td>
		</tr>
		<tr>
			<td>Свойства</td>
			<td class="center">
				<b>PROPERTY_CODE</b>
			</td>
			<td>Свойства элементов, которые попадут в arResult</td>
		</tr>
		<tr>
			<td>Проверять даты</td>
			<td class="center">
				<b>CHECK_DATES</b>
			</td>
			<td>Да/нет будут выведены только элементы с началом активности меньше или равной текущей</td>
		</tr>
		<tr>
			<td>ID раздела</td>
			<td class="center">
				<b>PARENT_SECTION</b>
			</td>
			<td>Указывается числовой код раздела инфоблока, из которого будут выбраны новости. Поле может быть оставлено пустым, если указан Код раздела.</td>
		</tr>
		<tr>
			<td>Код раздела</td>
			<td class="center">
				<b>PARENT_SECTION_CODE</b>
			</td>
			<td>Указывается символьный код раздела инфоблока, из которого будут выбраны новости. Поле может быть оставлено пустым, если указан ID раздела.</td>
		</tr>
		<tr>
			<td>Показывать элементы подразделов раздела</td>
			<td class="center">
				<b>INCLUDE_SUBSECTIONS</b>
			</td>
			<td>[Y|N] При отмеченной опции будут отображены элементы подразделов раздела.</td>
		</tr>
		<tr>
			<td>Детальный url</td>
			<td class="center">
				<b>DETAIL_URL</b>
			</td>
			<td>Url детальной страницы элемента</td>
		</tr>
		<tr>
			<td>Длина описания</td>
			<td class="center">
				<b>PREVIEW_TRUNCATE_LEN</b>
			</td>
			<td>Количество символов, после которого описание будет обрезано при выводе</td>
		</tr>
		<tr>
			<td>Формат вывода даты активности</td>
			<td class="center">
				<b>ACTIVE_DATE_FORMAT</b>
			</td>
			<td>Формат вывода даты активности</td>
		</tr>
		<tr>
			<td>Устанавливать заголовок</td>
			<td class="center">
				<b>SET_TITLE</b>
			</td>
			<td>Устанавливать заголовок</td>
		</tr>
		<tr>
			<td>Устанавливать заголовок браузера</td>
			<td class="center">
				<b>SET_BROWSER_TITLE</b>
			</td>
			<td>Y|N] При отмеченной опции будет установлен заголовок окна браузера по заданному SEO-шаблону META TITLE
				(см. закладку "SEO" в инфоблоке).
			</td>
		</tr>
		<tr>
			<td>Устанавливать ключевые слова</td>
			<td class="center">
				<b>SET_META_KEYWORDS</b>
			</td>
			<td>[Y|N] При отмеченной опции будут установлены ключевые слова страницы по заданному SEO-шаблону META
				KEYWORDS (см. закладку "SEO" в инфоблоке).
			</td>
		</tr>
		<tr>
			<td>Устанавливать описание страницы</td>
			<td class="center">
				<b>SET_META_DESCRIPTION</b>
			</td>
			<td>[Y|N] При отмеченной опции будет установлено описание страницы по заданному SEO-шаблону META DESCRIPTION
				(см. закладку "SEO" в инфоблоке).
			</td>
		</tr>
		<tr>
			<td>Устанавливать в заголовках ответа время модификации страницы</td>
			<td class="center">
				<b>SET_LAST_MODIFIED</b>
			</td>
			<td>[Y|N] При отмеченной опции http-ответ сервера будет содержать время последнего изменения страницы
				(заголовок Last-Modified).
			</td>
		</tr>
		<tr>
			<td>Включать инфоблок в цепочку навигации</td>
			<td class="center">
				<b>INCLUDE_IBLOCK_INTO_CHAIN</b>
			</td>
			<td>[Y|N] При отмеченной опции в цепочку навигации будет добавлено имя инфоблока.</td>
		</tr>
		<tr>
			<td>Включать раздел в цепочку навигации</td>
			<td class="center">
				<b>ADD_SECTIONS_CHAIN</b>
			</td>
			<td>[Y|N] При отмеченной опции при переходе по разделам ифоблока в цепочку навигации будут добавлены
				названия разделов.
			</td>
		</tr>
		<tr>
			<td>Шаблон постраничной навигации</td>
			<td class="center">
				<b>PAGER_TEMPLATE</b>
			</td>
			<td>Указывается название шаблона постраничной навигации.</td>
		</tr>

		<tr>
			<td>Выводить над списком</td>
			<td class="center">
				<b>DISPLAY_TOP_PAGER</b>
			</td>
			<td>[Y|N] При отмеченной опции постраничная навигация будет выведена вверху страницы, над списком.</td>
		</tr>
		<tr>
			<td>Выводить под списком</td>
			<td class="center">
				<b>DISPLAY_BOTTOM_PAGER</b>
			</td>
			<td>[Y|N] При отмеченной опции постраничная навигация будет выведена внизу страницы, под списком.</td>
		</tr>
		<tr>
			<td>Название категорий</td>
			<td class="center">
				<b>PAGER_TITLE</b>
			</td>
			<td>Задается название категорий, по которым происходит перемещение при детальном просмотре (например,
				страница, глава и др.).
			</td>
		</tr>
		<tr>
			<td>Выводить всегда</td>
			<td class="center">
				<b>PAGER_SHOW_ALWAYS</b>
			</td>
			<td>[Y|N] При отмеченной опции постраничная навигация будет выводиться всегда. По умолчанию выключено.</td>
		</tr>
		<tr>
			<td>Использовать обратную навигацию</td>
			<td class="center">
				<b>PAGER_DESC_NUMBERING</b>
			</td>
			<td>[Y|N] При отмеченной опции будет использоваться обратная навигация. Для обратной навигации в системе
				происходит обратный отсчет страниц (последняя страница считается первой). Таким образом, постоянно
				меняется лишь последняя страница при добавлении нового элемента. Это верно, если новые элементы попадают
				всегда вверх списка (отсортированы по дате начала активности по убыванию).
			</td>
		</tr>
		<tr>
			<td>Время кеширования страниц для обратной навигации</td>
			<td class="center">
				<b>PAGER_DESC_NUMBERING_CACHE_TIME</b>
			</td>
			<td>Задается время кеширования страниц для обратной навигации.</td>
		</tr>
		<tr>
			<td>Включить обработку ссылок</td>
			<td class="center">
				<b>PAGER_BASE_LINK_ENABLE</b>
			</td>
			<td>[Y|N] При отмеченной опции доступна обработка ссылок для постраничной навигации.</td>
		</tr>
		<tr>
			<td>Устанавливать статус 404</td>
			<td class="center">
				<b>SET_STATUS_404</b>
			</td>
			<td>[Y|N] Опция служит для включения обработки ошибки 404 в компоненте.</td>
		</tr>
		<tr>
			<td>Показ специальной страницы</td>
			<td class="center">
				<b>SHOW_404</b>
			</td>
			<td>[Y|N] При отмеченной опции будет показана специальная страница в случае возникновения ошибки 404, в
				противном случае - будет отображено специальное сообщение.
			</td>
		</tr>
		<tr>
			<td>Сообщение для показа (по умолчанию из компонента)</td>
			<td class="center">
				<b>MESSAGE_404</b>
			</td>
			<td>Задается сообщение, которое будет показано в случае возникновения ошибки 404. Если ничего не указывать,
				то будет использоваться стандартное сообщение из компонента.

				Параметр настраивается, если опция Показ специальной страницы не отмечена.
			</td>
		</tr>
		<tr>
			<td>Страница для показа (по умолчанию /404.php)</td>
			<td class="center">
				<b>FILE_404</b>
			</td>
			<td> Задается адрес страницы, которая будет отображаться при возникновении ошибки 404.
				Параметр настраивается, если отмечена опция Показ специальной страницы.
			</td>
		</tr>
		<tr>
			<td>Учитывать права доступа</td>
			<td class="center">
				<b>CACHE_GROUPS</b>
			</td>
			<td>[Y|N] При отмеченной опции будут учитываться права доступа при кешировании.</td>
		</tr>


		<tr>
			<td>Кэшировать при установленном фильтре</td>
			<td class="center">
				<b>CACHE_FILTER</b>
			</td>
			<td>[Y|N] При отмеченной опции каждый результат, полученный из фильтра, будет кешироваться.</td>
		</tr>

		<tr>
			<td>Тип кеширования</td>
			<td class="center">
				<b>CACHE_TYPE</b>
			</td>
			<td>Тип кеширования:
				<ul>
					<li>
						<b>A</b>
						- Авто + Управляемое: автоматически обновляет кеш компонентов в течение заданного времени или
						при изменении данных;
					</li>
					<li>
						<b>Y</b>
						- Кешировать: для кеширования необходимо определить время кеширования;
					</li>
					<li>
						<b>N</b>
						- Не кешировать: кеширования нет в любом случае.
					</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td>Время кеширования (сек.)</td>
			<td class="center">
				<b>CACHE_TIME</b>
			</td>
			<td>Время кеширования, указанное в секундах.</td>
		</tr>
	</table>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");