<? require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
/**
 * Этот файл выполняется по отправке формы
 * В массиве $_POST содержаться все заполненные данные формы
 * Валидация формы проходит тоже в этом файле
 */

if(!empty($_POST))
{

    $arParams = $_SESSION['COMPONENT_FORM']['PARAMS'];
    //$result['log'][] = print_r($_POST,1);
    $result['log'][] = print_r($arParams,1);


    // создаем элемент инфоблока
    if(!empty($arParams['IBLOCK_ID']))
    {

        // валидация - перебираем все поля формы
        foreach($arParams['FIELDS'] as $field)
        {
            // если поле обязательно и не заполнено
            if($field['REQUEST'] && !$_POST[$field['NAME']])
            {
                // добавяем ошибку
                if(!empty($field['ERROR_MESSAGE']))
                {
                    $errors[] = $field['ERROR_MESSAGE'];
                }
                else
                {
                    $errors[] = "Необходимо заполнить поле " . $field['PLACEHOLDER'];
                }
            }
        }


        $newElementFields = array(
            "IBLOCK_ID" => $arParams['IBLOCK_ID'],
            "ACTIVE" => $arParams['ACTIVE'],
        );

        foreach ($_POST as $fieldCode => $fieldValue) 
        {
            // отделяем свойства
            if (strpos($fieldCode, "PROPERTY_") !== false) 
            {
                $propertyCode = str_replace("PROPERTY_", "", $fieldCode);
                $newElementFields['PROPERTY_VALUES'][$propertyCode] = $fieldValue;
            } // от стандартных полей
            else 
            {
                $newElementFields[$fieldCode] = $fieldValue;
            }
        }


        // если нет ошибок
        if (empty($errors) && \Bitrix\Main\Loader::IncludeModule('iblock')) 
        {
            // добавляем элемент
            $newElement = new CIBlockElement;
            if ($newElementID = $newElement->Add($newElementFields)) {
                $result['success'] = $arParams['SUCCESS_MESSAGE']?:"Отзыв отправлен на модерацию";
                
                //создаем почтовое событие
                if(!empty($arParams['EMAIL_EVENT']))
                {
                     global $USER;
                     if(is_object($USER))
                     {
                        $mailFields = array(
                            'USER_NAME' => $USER->GetFullName(),
                            'USER_ID' => $USER->GetID(),
                        );
                     }
                     $mailFields = array_merge($_POST,$mailFields);
                     CEvent::SendImmediate($arParams['EMAIL_EVENT'], 's1', $mailFields, "Y", $arParams['EMAIL_EVENT_TEMPLATE']);
                     $result['success'] = "Сообщение отправлено";
                }

            }
        } 
        else 
        {
            $result['errors'] = implode('<br>', $errors);
        }
    }
    else
    {
        $result['errors'] = "Извините, форма временно не работает.";
    }

    echo json_encode($result);

}
?>