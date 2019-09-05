<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$title = "users.detail (Пользователь детально)";
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


    <h3>Описание</h3><p>Детальная страница пользователя.</p>
    <hr/><h3>Пример вызова</h3>
    <pre>
    $APPLICATION->IncludeComponent(
        "site:users.detail",
        "",
        Array(
            "ELEMENT_ID" => "1348",
            "SEF_FOLDER" => "/tests-dev/users/",
            "SET_TITLE" => "N",
            "ADD_ELEMENT_CHAIN" => "N",
            "FIELDS" => array(
                0 => "ID",
                1 => "NAME",
                2 => "LAST_NAME",
                3 => "PERSONAL_PHOTO",
                4 => "PERSONAL_CITY",
                5 => "PERSONAL_BIRTHDAY",
                6 => "",
            ),
            "CACHE_TIME" => "3600"
        )
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
            <td>Id Элемемента</td>
            <td class="center">
                <b>ELEMENT_ID</b>
            </td>
            <td>
                Указывается числовой код, в котором передается идентификатор пользователя.
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
            <td>Устанавливать заголовок страницы</td>
            <td class="center">
                <b>SET_TITLE</b>
            </td>
            <td>
                [Y|N] При отмеченной опции в качестве заголовка страницы будет установлено имя пользователя.
            </td>
        </tr>

        <tr>
            <td>Включать имя пользователя в цепочку навигации</td>
            <td class="center">
                <b>ADD_ELEMENT_CHAIN</b>
            </td>
            <td>
                [Y|N] При отмеченной опции название или заголовок (если задан в настройках SEO) элемента будет добавлен в цепочку навигации.
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
            <td>Включать имя пользователя в цепочку навигации</td>
            <td class="center">
                <b>ADD_ELEMENT_CHAIN</b>
            </td>
            <td>
                [Y|N] При отмеченной опции название или заголовок (если задан в настройках SEO) элемента будет добавлен в цепочку навигации.
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