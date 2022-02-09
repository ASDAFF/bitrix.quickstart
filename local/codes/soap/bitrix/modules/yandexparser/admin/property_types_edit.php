<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/properties/include.php"); // инициализация модуля

IncludeModuleLangFile(__FILE__);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight("properties");
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D")
  $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

// сформируем список закладок
$aTabs = array(
  array("DIV" => "edit1", "TAB" => 'Группа характеристик', "ICON"=>"main_user_edit", "TITLE"=>GetMessage("rub_tab_rubric_title")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);		// идентификатор редактируемой записи
$message = null;		// сообщение об ошибке
$bVarsFromForm = false; // флаг "Данные получены с формы", обозначающий, что выводимые данные получены с формы, а не из БД.

// ******************************************************************** //
//                ОБРАБОТКА ИЗМЕНЕНИЙ ФОРМЫ                             //
// ******************************************************************** //

if(
	$REQUEST_METHOD == "POST" // проверка метода вызова страницы
	&&
	($save!="" || $apply!="") // проверка нажатия кнопок "Сохранить" и "Применить"
	&&
	$POST_RIGHT=="W"          // проверка наличия прав на запись для модуля
	&&
	check_bitrix_sessid()     // проверка идентификатора сессии
)
{
    
            

  // обработка данных формы
  $arFields = Array(
    "NAME"    => $NAME,
    "SORT"    => $SORT,
    "SECTION_ID"  => $SECTION_ID,
    "ACTIVE"   =>( $ACTIVE <> "Y"? "N":"Y"),
  );

  // сохранение данных
  if($ID > 0)
  {
      CPropertyTypes::Update($ID, $arFields);  
      $res = CPropertyTypes::GetMaxID();
  }
  else
  {
      CPropertyTypes::Add($arFields);     
      $res = CPropertyTypes::GetMaxID();
  }

    if ($apply != ""){
      // если была нажата кнопка "Применить" - отправляем обратно на форму.
      LocalRedirect("/bitrix/admin/property_types_edit.php?ID=".$ID."&mess=ok?=".LANG."&".$tabControl->ActiveTabParam());
    }else{
      // если была нажата кнопка "Сохранить" - отправляем к списку элементов.
     
        CModule::IncludeModule('iblock');
        $iblock_id = 2;
        
        LocalRedirect("/bitrix/admin/options.php?id=".$iblock_id."&section=".$SECTION_ID."&lang=".LANG);

    }
 
}

// ******************************************************************** //
//                ВЫБОРКА И ПОДГОТОВКА ДАННЫХ ФОРМЫ                     //
// ******************************************************************** //

// значения по умолчанию
$str_SORT          = 500;
$str_ACTIVE        = "Y";
$str_NAME          = "";
$str_SECTION_ID    = intval($_REQUEST['section_id']);

// выборка данных
if($ID>0)
{
  $rubric = CPropertyTypes::GetByID($ID);
  if(!$rubric->ExtractFields("str_"))
    $ID=0;
}

// если данные переданы из формы, инициализируем их
if($bVarsFromForm)
  $DB->InitTableVarsForEdit("b_list_rubric", "", "str_");
 
//                ВЫВОД ФОРМЫ             

$APPLICATION->SetTitle(($ID>0? 'Редактирование группы характеристик '.$ID : 'Добавление группы характеристик'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?
// если есть сообщения об ошибках или об успешном сохранении - выведем их.
if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>'сохранено', "TYPE"=>"OK"));

if($message)
  echo $message->Show();
elseif($rubric->LAST_ERROR!="")
  CAdminMessage::ShowMessage($rubric->LAST_ERROR);
?>


<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?// проверка идентификатора сессии ?>
<?echo bitrix_sessid_post();?>
<?
// отобразим заголовки закладок
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
  <tr>
    <td width="40%">Активность</td>
    <td width="60%"><input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>></td>
  </tr>
  <tr>
    <td>Сортировка</td>
    <td><input type="text" name="SORT" value="<?echo $str_SORT;?>" size="10"></td>
  </tr>
  <tr>
    <td><span class="required">*</span>Имя группы</td>
    <td><input type="text" name="NAME" value="<?echo $str_NAME;?>" size="50" maxlength="255"></td>
  </tr>
    <input type="hidden" name="lang" value="<?=LANG?>">
    <input type="hidden" name="ID" value="<?=$ID?>">
    <input type="hidden" name="SECTION_ID" value="<?=$str_SECTION_ID;?>">
<?
// завершение формы - вывод кнопок сохранения изменений
$tabControl->Buttons(
  array(
    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>"rubric_admin.php?lang=".LANG,
    
  )
);

$tabControl->End();
// дополнительное уведомление об ошибках - вывод иконки около поля, в котором возникла ошибка
$tabControl->ShowWarnings("post_form", $message);
// информационная подсказка
echo BeginNote();?>
<span class="required">*</span>Имя группы характеристик обязательно для заполнения
<?echo EndNote();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
