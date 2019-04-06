# Лайки для элементов информационных блоков
https://marketplace.1c-bitrix.ru/solutions/vasoft.likeit/

**Описание решения**  
Модуль обеспечивает обработку "Лайков" проставляемых посетителями сайта для элементов информационных блоков.

При первом клике по кнопке отмеченной для модуля происходит установка лайка, при повторном - отмена.

Для работы модуля необходимо:
Ядро D7
Модуль "Информационные блоки"

**Установка**


После установки модуля из Маркетплейс необходимо выполнить следующее:

Указать элемент или элементы, которые будут содержать информацию о лайках. Для этого необходимо указать css-класс 'vs-likeit' и добавить атрибут 'dataid' со значением ИД элемента информационного блока
Для элементов, которые так же являются кнопками установки/отмены "лайка", указать css-класс vs-likeit-action
для отображения количества установленных "лайков" разместить внутри элемента с классом vs-likeit  элемент с классом vs-likeit-cnt
Подключить скрипт (c учетом кеширования)
```php
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs('/bitrix/js/vasoft.likeit/likeit.js');
```

Пример элементов:
```html
<span class="vs-likeit" dataid="10"><span class="vs-likeit-cnt></span></span>
<span class="vs-likeit vs-likeit-action"  dataid="10"><span class="vs-likeit-cnt></span></span>
<span class="vs-likeit vs-likeit-action" dataid="10"></span>
```

Если соответствующий элемент информационного блока уже был "лайкнут" текущим пользователем - элементу HTML добавляется класс 'vs-likeit-active'.

Класс 'vs-likeit-action' указывается если необходимо обрабатывать клик.

Классы 'vs-likeit-active' и 'vs-likeit-cnt' можно переопределить зада значения JavaScript переменным
```javascript
window.vas_likeit_classactive = 'my-acive';
window.vas_likeit_classcnt = 'my-cnt';
```


Так же получить статистику по лайкам в шаблонах  при помощи команды (где $arIDs - массив ИД элементов инфо-блока)
```php
\Bitrix\Main\Loader::includeModule('vasoft.likeit');
// Без учета текущего пользователя
$arLikes = \Vasoft\Likeit\LikeTable::checkLike($arIDs, false);
// C информацией о выборе текущего пользователя
$arLikes2 = \Vasoft\Likeit\LikeTable::getStatList($arIDs);
```


