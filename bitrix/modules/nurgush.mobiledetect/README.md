# Mobile Detect
https://marketplace.1c-bitrix.ru/solutions/nurgush.mobiledetect/

**Описание** 

Легкий модуль для определения устройства и браузера посетителя. 

Позволяет на серверной стороне  узнать через что посетитель смотрит ваш сайт — через планшет, телефон или компьютер, определить операционную систему — android, iOS и т.д., версию браузера и другое.

**Установка модуля**

Установить модуль. Для использования вызвать:
```php
CModule::IncludeModule('nurgush.mobiledetect');

$detect = new Nurgush\MobileDetect\Main();

if($detect->isMobile()){ 
   //делаем что-то
} 
 
 // Любой планшет
if( $detect->isTablet() ){
}
 
// Мобильные исключая планшеты
if( $detect->isMobile() && !$detect->isTablet() ){
}
 
// Определение ОС
if( $detect->isiOS() ){
}
 
if( $detect->isAndroidOS() ){
}
```

Модуль является портом под битрикс класса http://mobiledetect.net
Краткое описание API можно посмотреть по ссылке, подробнее в самом классе директория_модуля/lib/Main.php