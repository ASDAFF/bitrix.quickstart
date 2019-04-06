<?
$MESS['SOOBWA_COMMENTS_OPTIONS_AUTH'] = 'Вы не администратор!';
$MESS['SOOBWA_COMMENTS_OPTIONS_TAB_NAME'] = 'Инструкция';
$MESS['SOOBWA_COMMENTS_OPTIONS_TAB_TITLE'] = 'Инструкция';
$MESS['SOOBWA_COMMENTS_OPTIONS_TAB_TEXT'] = '
    <h3>Как пользоватся:</h3>
    <p>
        Добавляем компонент на страницу и настраиваем его
        <pre style="background-color: #424242; color: #ffffff; border-radius: 5px">

    $APPLICATION->IncludeComponent(
        "soobwa:soobwa.comments",
        ".default",
        Array(
            "AUTH" => "N",
            "CACHE" => "Y",
            "CACHE_TIMES" => "36000000",
            "COUNT" => "2",
            "ID_CHAT" => "1",
            "MODERATION" => "Y",
            "ENTRY_URL" => "/login/?login=yes",
            "AUTH_URL"=>"/login/?register=yes"
        )
    );
        </pre>
    </p>
    <h3>Параметры компонента:</h3>
    <p>
        <ul>
            <li><b>AUTH</b> - может ли не авторизованый пользователь оставлять коментарий (Y/N)</li>
            <li><b>CACHE</b> - кешироваение (Y/N)</li>
            <li><b>CACHE_TIMES</b> - время кеширования (int)</li>
            <li><b>COUNT</b> - колличество комментариев на странице (int)</li>
            <li><b>ID_CHAT</b> - идентификатор чата (string)</li>
            <li><b>MODERATION</b> - включена ли модерация (Y/N)</li>
            <li><b>ENTRY_URL</b> - ссылка на страницу авторизации (string)</li>
            <li><b>AUTH_URL</b> - ссылка на страницу регистрации (string)</li>
        </ul>
    </p>';


?>