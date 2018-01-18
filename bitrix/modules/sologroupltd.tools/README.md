# В помощь разработчику - удобные функции, ускоряющие разработку от Solo-it Studio
  
  
  Описание
  
  Содержит набор фукций, ускоряющих разработку сайта. 
  Среди них: getibc, getibc2, geibc3, generate_password, html2line, AddNewProperty 
  
  
    //Возвращает ID подсвойства у свойства в инфоблоке (если задано XML_ID)
    //  или ID свойства у инфоблока
    //$IBLOCK_ID - число либо мнемонический код инфоблока
  function getibc($IBLOCK_ID, $CODE = false, $XML_ID = false)
  С помощью этой функции можно получить ID инфоблока по символьному коду, номер свойства в инфоблоке или ID значения свойства 
  
  
  function getibc2($CODE, $XML_ID = false)
   
  то же самое, но только не нужно указывать ID инфоблока (перед вызовом обязательно должен быть вызван getibc - чтобы ID инфоблока закешировалось) 
  
  
  function getibc3($XML_ID){
   
  аналогично, только опускаем первые 2 значения 
  
  Пример использования: 
  
  Нужно сделать фильтр по скойству CITY_PLACE в инфоблоке новостей news по значению spb: 
  $arFilter = array('PROPERTY_'.getibc('news','CITY_PLACE')=>getibc('news','CITY_PLACE','spb'));
  //или
  $arFilter = array('PROPERTY_'.getibc('news','CITY_PLACE')=>getibc3('spb'));
  
  /*Преобразует html код в строку, удаляя все теги и переносы строки*/
        function html2line($text_desc)
  
  //Генерирует пароль   
  // Параметр $number - сообщает число
  // символов в пароле
      function generate_password($number)
  
  
      //Функция позволяет добавлять доп свойства в инфоблок, предварительно проверив такое свойство на существование
      //Пример вызова:
          // if(CModule::IncludeModuleEx('sologroupltd.tools')){
          //   $arFields = Array(
          //     "NAME" => "Размещение",
          //     "PROPERTY_TYPE" => "L",//L - список
          //     "LIST_TYPE" => "C",//C - флажки, L - список
          //     "MULTIPLE" => "Y",
          //   );
          //   $arFields["VALUES"][] = Array(
          //     "VALUE" => "Самара",
          //     "XML_ID" => "smr",
          //   );
          //   $arFields["VALUES"][] = Array(
          //     "VALUE" => "Питер",
          //     "XML_ID" => "spb",
          //   );
          //   CSoloTools::AddNewProperty('news','CITY_PLACE',$arFields);
          //   CSoloTools::AddNewProperty('STATES','CITY_PLACE',$arFields);
          // }
      //коды свойств брать отсюда: http://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty
      //Возвращает ID данного свойства(не важно, добавили мы его или нет)
      //Debug = true - будет означать, что будет выводиться на экран текстовое описание
      //Если = get_text - то функция будет возвращать текст
      function AddNewProperty($IBLOCK_CODE,$CODE,$arFields,$Debug=false)