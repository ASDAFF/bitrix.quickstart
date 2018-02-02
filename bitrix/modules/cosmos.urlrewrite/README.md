# Сортировка urlrewrite
![alt-текст](img_md/urlrewrite.png "1") 

https://marketplace.1c-bitrix.ru/solutions/cosmos.urlrewrite/

**Описание**

Всем известно, что правила обработки адресов в БУСе сортируются по длине условия. Чем длиннее условие, тем оно выше располагается в файле urlrewrite.php. На практике часто необходимо изменить порядок сортировки условий не опираясь на длину.

Модуль организует сортировку правил обработки адресов согласно полю SORT, которое добавляется в массив описывающий правила. Рассмотрим файл urlrewrite.php, было:
```php
$arUrlRewrite = array(
   array(
      "CONDITION" => "#^/news/(.*?)/#",
      "RULE" => "SECTION_CODE=$1",
      "ID" => "",
      "PATH" => "/news/index.php",
   ),
   array(
      "CONDITION" => "#^/news/x/#",
      "RULE" => "SECTION_CODE=main&CODE=x",
      "ID" => "",
      "PATH" => "/news_main/index.php",

   )
);
```
Станет:
```php
$arUrlRewrite = array(
   array(
      "CONDITION" => "#^/news/x/#",
      "RULE" => "SECTION_CODE=main&CODE=x",
      "ID" => "",
      "PATH" => "/news_main/index.php",
      "SORT" => "90",
   ),
   array(
      "CONDITION" => "#^/news/(.*?)/#",
      "RULE" => "SECTION_CODE=$1",
      "ID" => "",
      "PATH" => "/news/index.php",
      "SORT" => "100",
   )
);
```
Как это работает? 
Модуль отслеживает состояние файла urlrewrite.php и, при обнаружении изменений, запускает свой механизм сортировки по увеличению значению поля SORT.
В случае, если у правила не задано поле SORT, модуль создаст его и присвоит значение = 100.