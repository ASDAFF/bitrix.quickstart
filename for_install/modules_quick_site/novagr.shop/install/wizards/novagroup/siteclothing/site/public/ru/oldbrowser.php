<style>
    /***************************
		CSS RESET
***************************/

    html, body, div, span, applet, object, iframe,
    h1, h2, h3, h4, h5, h6, p, blockquote, pre,
    a, abbr, acronym, address, big, cite, code,
    del, dfn, em, font, img, ins, kbd, q, s, samp,
    small, strike, strong, sub, sup, tt, var,
    dl, dt, dd, ol, ul, li,
    fieldset, form, label, legend,
    table, caption, tbody, tfoot, thead, tr, th, td
    {
        margin: 0;
        padding: 0;
        border: 0;
        outline: 0;
        font-weight: inherit;
        font-style: inherit;
        font-family: inherit;
        vertical-align: baseline;
    }

    body
    {
        line-height: 1;
        color: black;
        background: white;
    }

    #old ol, #old ul
    {
        list-style: none;
    }

    #old h1, #old h2
    {
        font-family: Tahoma, Segoe UI, Verdana, Helvetica, Arial, sans-serif; /* Follows MSCOM Typography Guidelines */
    }

    html
    {
        background: #e6e6e6 url('/local/templates/demoshop/images/old/bkg-html.jpg') repeat-x;
        height: 100%;
        /*width: 100%;*/
    }

    /***************************
        END CSS RESET
    ***************************/

    body
    {
        background: transparent url('/local/templates/demoshop/images/old/bkg-body.jpg') no-repeat right top;
        height: 100%;
        /*width: 100%;*/
        font-family: Tahoma, Verdana, Helvetica, Arial, sans-serif; /* Follows MSCOM Typography Guidelines */
        color: #4b4b4b;
        font-size: 0.78em;
    }

    /* Links */
    #old a:link, #old a:visited
    {
        color: #1f5ca1;
        text-decoration: none;
    }

    #old a:hover
    {
        color: #0062A0;
        text-decoration: underline;
    }

    #old
    {
        width:783px;
        margin: auto;
    }

    #header
    {
        text-align:justify;
        width:744px;
        padding:10px 20px 20px;
    }

    #header h1
    {
        text-align: center;
        font-weight:normal;
        margin:0 0 10px;
        font-size: 20pt;
    }

    #header font
    {
        font-size: 10pt;
    }

    #modern-browser
    {
        margin:0;
        padding:10px 20px 20px;
        list-style:none;
    }

    #modern-browser li
    {
        padding-left:130px;
        margin-top:20px;
    }

    #modern-browser li.ie8{background:url('/local/templates/demoshop/images/old/big-ie8.gif') no-repeat 5px top;}
    #modern-browser li.chrome{background:url('/local/templates/demoshop/images/old/big-chrome.gif') no-repeat 15px top;}
    #modern-browser li.opera{background:url('/local/templates/demoshop/images/old/big-opera.gif') no-repeat left top;}
    #modern-browser li.firefox{background:url('/local/templates/demoshop/images/old/big-firefox.gif') no-repeat 10px top;}

    #modern-browser h2
    {
        margin:0 0 10px 10px;
        font-weight:bold;
    }

    #modern-browser .file
    {
        padding:0;
        margin:10px 0 0;
        list-style:none;
    }

    #modern-browser .file li
    {
        padding:2px 0 2px 22px;
        margin:0;
    }

    #modern-browser .file li.microsoft{background:url('/local/templates/demoshop/images/old/fav-ie.gif') no-repeat left;}
    #modern-browser .chrome .file li{background:url('/local/templates/demoshop/images/old/fav-chrome.gif') no-repeat left;}
    #modern-browser .opera .file li{background:url('/local/templates/demoshop/images/old/fav-opera.gif') no-repeat left;}
    #modern-browser .firefox .file li{background:url('/local/templates/demoshop/images/old/fav-firefox.gif') no-repeat left;}

    #old .descriptiontext
    {
        text-align: justify;  /* Выравнивание по ширине */
        font-size: 10pt; /* Размер шрифта в пунктах */
        margin: 15px;
    }

    #old .descriptiontext .file
    {
        text-indent: 1.5em; /* Отступ первой строки */

    }

    #old .delim
    {
        background-color: silver;
        height: 1px;
        border-width:0px; /* Убрать рамки вокруг элемента */
    }
    #not-old-browser {
        display: none;
    }
</style>
<div id="old">
    <div id="header">
        <h1>Ваш браузер устарел!</h1>
        <font>Вы пользуетесь устаревшей версией браузера Internet Explorer.
            Данная версия браузера не поддерживает многие современные технологии,
            из-за чего многие страницы отображаются некорректно, а главное — на
            сайтах могут работать не все функции. В связи с этим на Ваш суд
            представляются более современные браузеры. Все они бесплатны, легко
            устанавливаются и просты в использовании. При переходе на любой
            нижеуказанный браузер все ваши закладки и пароли будут перенесены из
            текущего браузера, вы ничего не потеряете.</font>
    </div>

    <ul id="modern-browser">
        <li class="firefox">
            <h2>Mozilla Firefox</h2>
            <div class="descriptiontext">
                Один из самых распространенных и гибких браузеров. Браузер может
                быть настроен под себя на любой вкус при помощи огромного числа
                дополнений на все случаи жизни и тем оформления, которые вы найдете на
                официальном сайте дополнений.
                <ul class="file">
                    <li style="padding-left: 0px;"><a href="http://www.mozilla.org/firefox/">Перейти к загрузке Mozilla Firefox</a></li>
                </ul>
            </div>
        </li>

        <hr class="delim">

        <li class="chrome">
            <h2>Google Chrome</h2>
            <div class="descriptiontext">
                Новый, но уже достаточно популярный браузер от гиганта поисковой
                индустрии, компании Google. Обладает очень простым и удобным
                интерфейсом. Если вам нужен просто браузер без специальных функций — для
                вас Google Chrome станет лучшим выбором.
                <ul class="file">
                    <li style="padding-left: 0px;"><a href="http://www.google.com/chrome/">Перейти к загрузке Google Chrome</a></li>
                </ul>
            </div>
        </li>

        <hr class="delim">

        <li class="opera">
            <h2>Opera</h2>
            <div class="descriptiontext">
                Браузер Opera всегда позиционировался, как очень удобный и быстрый.
                Имеет внутренние утилиты для ускорения загрузки страниц, особенно
                актуально для пользователей с медленным интернетом. Хотя отлично
                подойдет и любым другим пользователям.
                <ul class="file">
                    <li class="v9" style="padding-left: 0px;"><a href="http://www.opera.com/">Перейти к загрузке Opera</a></li>
                </ul>
            </div>
        </li>

        <hr class="delim">

        <li class="ie8">
            <h2>Новый Internet Explorer</h2>
            <div class="descriptiontext">
					<span>Современная версия браузера от компании Microsoft. Бесплатно 
предоставляется всем желающим и свободен для распространения. Если слова
 «браузер» и «Internet Explorer» для вас незнакомы, установите эту 
программу.</span>
                <ul class="file">
                    <li class="microsoft" style="padding-left: 0px;"><a href="http://www.microsoft.com/rus/windows/internet-explorer/">Перейти к загрузке Internet Explorer</a></li>
                </ul>
            </div>
        </li>
        <hr class="delim">
    </ul>
    <center><a style="font-size: 10px;" href="http://phpbbex.com/forum/viewtopic.php?t=60">Установить уведомление об устаревшем браузере на ваш сайт</a></center>
</div>
