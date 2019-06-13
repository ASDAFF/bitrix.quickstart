<?
$MESS ['MYSTERY_THUMBS_DOC_TAB_SET'] = "Документация";
$MESS ['MYSTERY_THUMBS_MAIN_TAB_SET'] = "Общие параметры";
$MESS ['MYSTERY_THUMBS_HEADING_MAIN'] = "Общие параметры";
$MESS ['MYSTERY_THUMBS_JPG_QUALITY'] = "Качество сжатия для <b>*.jpg</b>";
$MESS ['MYSTERY_THUMBS_BACKGROUND_COLOR'] = "HEX-код фонового цвета";
$MESS ['MYSTERY_THUMBS_BACKGROUND_MESSAGE'] = 'Это цвет фона "добавленных" частей изображения';
$MESS ['MYSTERY_THUMBS_PNG_TRANSPARENT'] = "Прозрачный фон для <b>*.png</b>";
$MESS ['MYSTERY_THUMBS_HEADING_WATERMARK'] = 'Параметры наложения водного знака';
$MESS ['MYSTERY_THUMBS_WATERMARK_ENABLE'] = 'Добавить водный знак на изображения';
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION'] = 'Положение водного знака на изображении';
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_LT'] = "Левый верхний угол";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_CT'] = "По центру вверху";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_RT'] = "Правый верхний угол";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_LM'] = "Слева по центру";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_CM'] = "По центру";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_RM'] = "Справа по центру";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_LB'] = "Левый нижний угол";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_CB'] = "По центру внизу";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_RB'] = "Правый нижний угол";
$MESS ['MYSTERY_THUMBS_WATERMARK_MIN_WIDTH_PICTURE'] = 'Минимальная ширина картинки для наложения водного знака';
$MESS ['MYSTERY_THUMBS_WATERMARK_EXCEPTION'] = 'Список разделов-исключений для наложения водного знака';
$MESS ['MYSTERY_THUMBS_WATERMARK_EXCEPTION_DESC'] = 'Вводите разделы через точку с запятой без указания конкретных страниц.<br /><i>Например: /about/; /articles/</i>';
$MESS ['MYSTERY_THUMBS_WATERMARK_IMG'] = 'Изображение водного знака (только <b>*.png</b>, название <b>copyright.png</b>)';
$MESS ['MYSTERY_THUMBS_WATERMARK_IMG_DESC'] = '<a href="/bitrix/admin/fileman_admin.php?path=/bitrix/images/mystery.thumbs&show_perms_for=0" target="_blank">'.MYSTERY_THUMBS_WATERMARK_IMG.'</a>';
$MESS ['MYSTERY_THUMBS_WATERMARK_ALT'] = 'Водный знак';
$MESS ['MYSTERY_THUMBS_COLOR_PICKER'] = 'Выбор цвета';
$MESS ['MYSTERY_THUMBS_FORM_SAVE'] = "Сохранить";
$MESS ['MYSTERY_THUMBS_FORM_RESET'] = "Сбросить";
$MESS ['MYSTERY_THUMBS_MAIN_RESTORE_DEFAULTS'] = "Востановить значения по умолчанию";
$MESS ['MYSTERY_THUMBS_HEADING_ADDITIONAL_PARAMS'] = "Дополнительные параметры";
$MESS ['MYSTERY_THUMBS_DELETE_OLD_THUMBS'] = "Удалить ранее созданные изображения";
$MESS ['MYSTERY_THUMBS_DELETE_OLD_THUMBS_DESC'] = "Если вы только что включили или выключили использование водного знака, отметьте эту опцию, чтобы все измененные картинки создались заново, с учетом новых настроек.";
$MESS ['MYSTERY_THUMBS_DOCUMENTATION'] = '

<tr class="heading">
    <td colspan="2">Основная задача</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            Модуль <b>mystery.thumbs</b> расширяет возможности управления изображениями, загруженными на сайт:
            <ol>
                <li>Требуемый размер изображения создается "на лету".</li>
                <li>Можно указать метод обработки изображения.</li>
                <li>Можно наложить водный знак.</li>
            </ol>
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">Как использовать</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            Для получения требуемого размера изображения достаточно в путь к картинке добавить (в аттрибут src тега &lt;img&gt;) /thumb/ и указать требуемые параметры.
        </p>
        <p>
            Например:
            <ul>
                <li>
                    &lt;img src="<b>/thumb/150x120xin</b>/upload/iblock/jd8/jd8kdk19dn2j29d8jspssv.jpg"&gt;<br />
                    <i>исходное изображение /upload/iblock/jd8/jd8kdk19dn2j29d8jspssv.jpg</i>
                </li>
                <li>
                    &lt;img src="<b>/thumb/450x840xcut</b>//www.yousite.images.yousite.ru/upload/iblock/5bd/5db9cefbc414a902a46f1b8fae16.png?anyparam=true"&gt;<br />
                    <i>исходное изображение //www.yousite.images.yousite.ru/upload/iblock/5bd/5db9cefbc414a902a46f1b8fae16.png находится в облаке</i>
                </li>
            </ul>
        </p>
        <p>
            Общий формат добавляемых параметров следующий <b>/thumb/W</b>x<b>H</b>x<b>METHOD<span style="color:red">IMAGE</span></b>:
            <ul>
                <li><b>W</b> - ширина требуемого изображения</li>
                <li><b>H</b> - высота требуемого изображения</li>
                <li><b>METHOD</b> - метод обработки исходного изображения</li>
                <li><b>IMAGE</b> - путь до исходного изображения</li>
            </ul>
            Особенности:
            <ul>
                <li>
                    Созданное изображение <b>ВСЕГДА</b> требуемого размера.<br />
                    <i>Если исходная картинка меньше по обеим сторонам, чем требуемая, то она будет размещена по центру полученного изображения без изменения размера.</i>
                </li>
                <li>
                    <b>W</b> и <b>H</b> могут быть указаны как <b>0</b> (при условии, что второй параметр задан больше нуля).<br />
                    <i>В этом случае не указанный размер будет определен пропорционально исходя из размеров исходного изображения.</i>
                </li>
                <li>
                    <b>IMAGE</b> - может быть указан как локальный (путь на том же сервере, где и сайт), так и глобальный (со стороннего сайта или из облака)
                </li>
                <li>
                    Все созданные изображения хранятся в папке <b>'.MYSTERY_THUMBS_CHACHE_IMG_PATH.'</b> от корня сайта.<br />
                    <i>При повторном запросе выводится ранее созданное изображение.</i>
                </li>
            </ul>
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">Варианты ресайза</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            Существуют следующие методы обработки изображений:
            <ul>
                <li>
                    <b>IN</b> - исходная картинка помещается "внутрь" указанного контейнера целиком в пропорционально уменьшенном виде.<br />
                    "Пустые" поля конечного изображения "заливаются" указанным в настройках модуля цветом.<br />
                    <i>(для <b>*.png</b> изображений возможно создание прозрачных полей)</i>
                </li>
                <li class="noPoint">
                    <img src="/thumb/150x200xin/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                    <img src="/thumb/250x200xin/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                    <img src="/thumb/350x200xin/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                </li>
                <li>
                    <b>CUT</b> - исходная картинка полностью заполняет указанный контейнер. Пропорционально уменьшенное изображение размещается по центру контейнера.<br />
                    <i>"Лишние" поля исходной картинки, который выходят за пределы указанного контейнера обрезаются.</i>
                </li>
                <li class="noPoint">
                    <img src="/thumb/150x200xcut/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                    <img src="/thumb/250x200xcut/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                    <img src="/thumb/350x200xcut/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                </li>
                <li>
                    <b>CUTT</b> - <i>(читай "CUT TOP")</i> метод аналогичен методу CUT с одним НО: пропорционально уменьшенное изображение располагается в указанном контейнере начиная с верхней своей точки.<br />
                    <i>По ширине картинка также как и в CUT располагается по центру.</i>
                </li>
                <li class="noPoint">
                    <img src="/thumb/150x200xcutt/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                    <img src="/thumb/250x200xcutt/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                    <img src="/thumb/350x200xcutt/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                    <br />
                    <i>В этом случае изображение имеет сверху большой пустой слой, который мы и видим.</i>
                </li>
                <li>
                    <b>TRIM</b> - к исходной картинке добавляются поля со всех сторон: слева и справа - равные W, снизу и сверху - равные H.<br />
                    Цвет добавленных полей указывается в настройках модуля.<br />
                    <i>(для <b>*.png</b> изображений возможно добавление прозрачных полей)</i>
                </li>
                <li class="noPoint">
                    <img src="/thumb/25x20xtrim/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="Тестовое изображение">
                    <br />
                    <i>Снизу и сверху - по 20 пикселей, справа и слева - по 25.</i>
                </li>
            </ul>
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">Обработка картинок из облака</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            При использовании "Ускорения сайта (CDN)" или облачного сервиса для хранения изображений путь до картинок является путем на внешний сервер. <br />
            В параметр <b>IMAGE</b> можно указать как абсолютный (внешний) путь до изображения, так и относительный (локальный).
        </p>
        <p>
            В любом случае конечная картинка будет располагаеться в папке '.MYSTERY_THUMBS_CHACHE_IMG_PATH.'.
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">Наложение водного знака</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            При необходимости в настройках модуля можно включить наложение водного знака и настроить параметры его расположения на конечном изображении.<br />
            Водный знак должен быть расположен точно по указанному пути: <b>'.MYSTERY_THUMBS_WATERMARK_IMG.'</b>.<br />
            Настройка данного параметра в текущей версии модуля невозможна.
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">Как это работает</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            При установке модуля, создается запись в <a href="/bitrix/admin/urlrewrite_list.php?lang=ru" target="_blank">"Обработке адресов"</a>, с помощью которой все запросы начинающиеся с /thumb/ переадресуются сначала на файл mystery_thumbs.php в корне сайта, а потом на системные файлы, которые непосредственно создают требуемое изображение.
        </p>
    </td>
</tr>
';
?>