классы для ускорения разработки и отладки.

Включает:
Логгер(сейчас не наследуется от Diag)
Класс для работы с ошибками(старое ядро)
Класс для отслеживания производительности скрипта по шагам(учитывается время выполнения и используемая ОЗУ)
Класс для создание структурированного многоуровневого меню из списка
Класс для работы с HL блоками
Класс для работы с многоязычностью и пользовательскими группами


Замер ресурсов
```php
use \Wizard\Additional\CheckResources;
$obCheckResources = CheckResources::getInstance();
$obCheckResources->setUse(true);
$obCheckResources->init();
выполняем код
$obCheckResources->setStep();
выполняем код
$obCheckResources->setStep();
смотрим какой блок выполняется дольше и расходует слишком много ОЗУ
$obCheckResources->show();
```

Помещаем в result_modifier код для создания структурированного массива меню
```php
use \Wizard\Additional\CreateMultiLvlArray;
$obMulti = new CreateMultiLvlArray();
$arResult = $obMulti->get($arResult);
```

Логгер
```php
use \Wizard\Additional\Logger;
$logger = new Logger();
$logger->activate(); //Включаем\Выключаем логирвоание
$logger->setType('log_els'); //Указываем навзание файла в котоырй запишется лог
$logger->write('текст или массив'); //Записываем текст или массив
$logger->writeEndLine(); //Записываем разделитель конца
$logger->writeSeparator(); //Записываем промежуточный разделитель
```


Сбор не фатальных ошибок
```php
use \Wizard\Additional\Errors;
$obErrors = Errors::getInstance();
$obErrors->clearErrors();//Очищаем предыдущие ошибки
$obError->setError('Текст ошибки'); //Добавляем текст ошибки
$obErrors->setErrors()//Регистрация ошибок с возможностью показа
```

Класс базовых функций
```php
use \Wizard\Additional\Main;
$obMain = Main::getInstance();
$obMaon->setLanguageVars(); //Устанавливаем начальные параметры для языковой версии
$obMain->setPageLangValues(); //Заполняем параметры страниц из языковых настроек
$obMain->setCurrentPage(); //Устанавливаем параметры для текущей страницы
$fullName = $obMain->getFullName($arUser); //Получаем ФИО или логин
$obMain->trimArrayStrings($arr); //Удаление пробелов из всех элементов массива рекурсивно
$obMain->setCurrentUser(); //Устанавливает данные для текущего пользователя
$text = $obMain->pluralForm($int, $arForms); //Возвращает словоформу в зависимости от количества
```

Класс для работы с HL блоками
```php
use \Wizard\Additional\HLAdditional;
$obHl = HLAdditional::getInstance();
$obHl->getList($params) //Такой же ORM как и с обычными сущностями, есть дополнительный параметр HL_ID - его можно не передавать если установили через другой метод, с установленной опцией bReturnObject возвращает массив в котором помимо элементов еще и объект
$obHL->setHLID($ID); //Установка ID инфоблока для дальнейшей работы методов без доп параметра HL_ID
$obHL->haveValue($val); //Проверка переменной на пустату
$obHL->getListCount($params) //Получаем количество элементов
$obHL->add($params);//Данные в arData передаются
$obHL->update($params);//Данные в arData передаются, ID в ID
$obHL->delete($params);//ID = ID
$obHL->getHLList($select); //Получаем список HL блоков
$obHL->getHLFields($params); //Получаем список поле HL блока
```