# Настройки проекта

https://marketplace.1c-bitrix.ru/solutions/ws.projectsettings/

**Описание**

Модуль позволяет централизовано хранить "гибкие" настройки проекта, не копируя их по всему проекту.
Версия 1С-Битрикс должна быть не ниже 11.5
Подробнее http://marketplace.1c-bitrix.ru/blog/free-module-project-settings/

После установки модуля, в административной части интерфейса появится страница "Настройки" -> "Настройки проекта". Где определяется список полей и их значения.

Для получения значений настроек используется метод: 
- WS_PSettings::getFieldValue($name, $default = null): получение значения поля $name, при отсутствии поля будет возвращено значение по умолчанию $default.

Пример использования:
```php
CModule::includeModule('ws.projectsettings');
/* Получение значения указанного в настройках */
$productIblock = WS_PSettings::getFieldValue('productIblock', false);
```
