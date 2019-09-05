<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$title = "users.list (Список пользователей)";
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


    <h3>Описание</h3><p>Список пользователей.</p>
    <hr/><h3>Пример вызова</h3>
    <pre>
    $APPLICATION->IncludeComponent(
        "site:users.list",
        ".default",
        array(
            "PAGE_ELEMENT_COUNT" => "18",
            "FILTER_NAME" => "",
            "SORT_BY" => "ID",
            "SORT_ORDER" => "desc",
            "FIELDS" => array(
                0 => "ID",
                1 => "NAME",
                2 => "LAST_NAME",
                3 => "PERSONAL_PHOTO",
                4 => "PERSONAL_CITY",
                5 => "PERSONAL_BIRTHDAY",
                6 => "",
            ),
            "LIST_SHOW_PHOTO" => "Y",
            "LIST_LINK_DETAIL" => "N",
            "SHOW_PAGER" => "Y",
            "AJAX_ID" => "",
            "USERS_COUNT" => "999",
            "LIST_TITLE" => "",
            "SEF_MODE" => "Y",
            "SEF_FOLDER" => "/tests-dev/users/"
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000",
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
            <td>Количество пользователей, которые будут отображены на одной странице</td>
            <td class="center">
                <b>PAGE_ELEMENT_COUNT</b>
            </td>
            <td>
                Указывается количество пользователей, которые будут отображены на одной странице.
            </td>
        </tr>

        <tr>
            <td>Фильтр</td>
            <td class="center">
                <b>FILTER_NAME</b>
            </td>
            <td>
                Указывается имя переменной, в которой передается массив параметров из фильтра. Служит для определения выходящих из фильтра элементов. Если поле оставлено пустым, то используется значение по умолчанию.
            </td>
        </tr>

        <tr>
            <td>Поле сортировки пользователей</td>
            <td class="center">
                <b>SORT_BY</b>
            </td>
            <td>
                Поле сортировки пользователей
                <ul>
                    <li>
                        <b>ID</b>
                    </li>
                    <li>
                        <b>NAME</b>
                    </li>
                    <li>
                        <b>Другое</b>
                    </li>
                </ul>
            </td>
        </tr>

        <tr>
            <td>Направление сортировки пользователей</td>
            <td class="center">
                <b>SORT_ORDER</b>
            </td>
            <td>
                Направление сортировки пользователей
                <ul>
                    <li>
                        <b>asc</b>
                    </li>
                    <li>
                        <b>desc</b>
                    </li>
                </ul>
            </td>
        </tr>

        <tr>
            <td>Поля</td>
            <td class="center">
                <b>FIELDS</b>
            </td>
            <td>
                Указываются поля, которые будут отображены на странице.
            </td>
        </tr>

        <tr>
            <td>Выводмть фото пользователя</td>
            <td class="center">
                <b>LIST_SHOW_PHOTO</b>
            </td>
            <td>
                [Y|N] При отмеченной опции в списке пользователей будет отображено фото. Берется поле <b>PERSONAL_PHOTO</b>.
            </td>
        </tr>

        <tr>
            <td>Ссылки на детальную страницу пользователя</td>
            <td class="center">
                <b>LIST_LINK_DETAIL</b>
            </td>
            <td>
                [Y|N] При отмеченной опции в списке будет ссылка на детальную страницу пользователя.
            </td>
        </tr>

        <tr>
            <td>Постраничная навигация</td>
            <td class="center">
                <b>SHOW_PAGER</b>
            </td>
            <td>
                [Y|N] При отмеченной опции будет отображена постраничная навигация.
            </td>
        </tr>

        <tr>
            <td>Идентификатор компонента</td>
            <td class="center">
                <b>AJAX_ID</b>
            </td>
            <td>
                Задается символьный код-идентификатор компонента. Нужен для работы постраничной навигации в режиме AJAX. Если указана пустая строка, то режим AJAX будет отключен.
            </td>
        </tr>

        <tr>
            <td>Максимальное число пользователей</td>
            <td class="center">
                <b>USERS_COUNT</b>
            </td>
            <td>
                Указывается максимальное количество пользователей которые будут выбраны из БД.
            </td>
        </tr>

        <tr>
            <td>Заголовок списка пользователей</td>
            <td class="center">
                <b>LIST_TITLE</b>
            </td>
            <td>
                Указывается какой заголовок будет отображен над списком пользователей.
            </td>
        </tr>

        <tr>
            <td>Включить поддержку ЧПУ</td>
            <td class="center">
                <b>SEF_MODE</b>
            </td>
            <td>
                [Y|N] При отмеченной опции будет включена поддержка ЧПУ.
            </td>
        </tr>

        <tr>
            <td>Каталог ЧПУ (относительно корня сайта)</td>
            <td class="center">
                <b>SEF_FOLDER</b>
            </td>
            <td>
                Каталог ЧПУ: путь до папки, с которой работает компонент. Этот путь может как совпадать с физическим путём, так и не совпадать. Все остальные настройки из этой секции дописываются к каталогу ЧПУ.
            </td>
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
                        - Авто + Управляемое: автоматически обновляет кеш компонентов в течение заданного времени или при изменении данных;
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