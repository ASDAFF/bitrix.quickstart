# Обработчик изображений phpThumb (Миниатюры, водяные знаки и пр.)
**Описание**

Модуль предоставляет возможность использовать весь функционал библиотеки phpThumb(). В модуле реализована форма удобной настройки генерации миниатюр.
Библиотека phpThumb позволяет менять размер, вращать изображения, накладывать водяные знаки, применять фильтры и пр.

После установки можно создать шаблон обработки изображений(Сервисы->Обработчик изображений phpThumb) и вызвать в шаблоне метод генерации миниатюры:
```php
  if (CModule::IncludeModule("millcom.phpthumb"))
    $arItem["PREVIEW_PICTURE"]["SRC"] = CMillcomPhpThumb::generateImg($arItem["DETAIL_PICTURE"]["SRC"], 1);
```


либо передать в метод параметры для нового изображения:
```php
if (CModule::IncludeModule("millcom.phpthumb")) {
   $arParams = array(
      'f' => 'jpg',
      'q' => '96',
      'zc' => '1',
      'w' => '400',
      'h' => '400',
      'fltr' => array(
         '0' => 'blur|1'
      )
   );

   $img = CMillcomPhpThumb::generateImg('test.jpg', $arParams);
   echo '<img src="'.$img.'">';
}
```
