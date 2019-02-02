Bitrix Cleaner
==============

![Панель управления](https://raw.github.com/creadome/bitrixcleaner/master/cleaner.png)

Гаджет для панели управления 1С-Битрикс, позволяющий быстро очистить неуправляемый кеш, управляемый и миниатюры изображений ([CFile::ResizeImageGet](http://dev.1c-bitrix.ru/api_help/main/reference/cfile/resizeimageget.php)).

При очистке производится полное удаление директорий `/bitrix/cache/`, `/bitrix/managed_cache/` и `/upload/resize_cache/`.

Отображение статистики по количеству файлов кеша и их общему размеру.

Установка
---------

1. Создайте свое пространство имен для гаджетов, например `/bitrix/gadgets/tools/`;
2. Скопируйте папку `cleaner` в `/bitrix/gadgets/tools/`;
3. В панели управления добавьте на рабочий стол гаджет "Bitrix Cleaner" (Добавить гаджет / Контент / Bitrix Cleaner);