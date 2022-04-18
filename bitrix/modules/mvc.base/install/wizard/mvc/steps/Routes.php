<?php


class RoutesStep extends CWizardStep
{
    public static $maxPages = 10;

    function InitStep()
    {
        //ID шага
        $this->SetStepID("routes");

        //Заголовок
        $this->SetTitle("Настройка ЧПУ компонента");
        $this->SetSubTitle("Укажите адреса страниц, обрабатываемые компонентом");

        //Навигация
        $this->SetPrevStep("description");
        $this->SetNextStep("review");
        $this->SetCancelStep("cancel");

        $wizard =& $this->GetWizard(); // Получаем ссылку на объект мастера
        $wizard->SetDefaultVar("active", "Y"); //Устанавливаем значение по умолчанию
    }

    function ShowStep()
    {
        $this->content .= '<table class="data-table">';

//        $this->content .= '<tr><th align="right">Активен:</th><td>' . $this->ShowCheckBoxField("active", "Y") . '</td></tr>';
        for ($itemId = 0; $itemId < static::$maxPages; $itemId++) {
            $this->content .= '<tr><th align="right">ID страницы (латиницей):</th><td>';
            $this->content .= $this->ShowInputField("text", "page_id_$itemId", Array("size" => 25));
            $this->content .= '</td></tr>';


            $this->content .= '<tr><th align="right">Название страницы для пользователя:</th><td>';
            $this->content .= $this->ShowInputField("text", "page_name_$itemId", Array("size" => 25));
            $this->content .= '</td></tr>';

            $this->content .= '<tr><th align="right">Шаблон ЧПУ страницы:</th><td>';
            $this->content .= $this->ShowInputField("text", "page_sef_$itemId", Array("size" => 45));
            $this->content .= '</td></tr>';

            $this->content .= '<tr><th align="right">Переменные стрницы (VARIABLES, через запятую):</th><td>';
            $this->content .= $this->ShowInputField("text", "page_variables_$itemId", Array("size" => 45));
            $this->content .= '</td></tr>';

            $this->content .= '<tr><th><hr></th><td><hr></td></tr>';
        }

        $this->content .= '</table>';
    }

    function OnPostForm()
    {
        $wizard =& $this->GetWizard();

        if ($wizard->IsCancelButtonClick()) {
            return;
        }

        $variables = $wizard->GetVars();

        list($fieldsToCheck, $mandatoryFields) = static::getFieldsToCheck();
        for ($i = 0; $i < static::$maxPages; $i++) {
            $delete = true;
            foreach ($fieldsToCheck as $name => $caption) {
                if (!empty($variables['page_' . $name . '_' . $i])) {
                    foreach ($mandatoryFields as $mandatory => $error) {
                        if (empty($variables['page_' . $mandatory . '_' . $i])) {
                            $this->SetError($error, 'page_' . $mandatory . '_' . $i);
                        }
                    }
                    $delete = false;
                }
            }

            if ($delete) {
                foreach ($fieldsToCheck as $name => $caption) {
                    $wizard->UnSetVar('page_' . $name . '_' . $i);
                }
            }
        }
    }

    public static function getFieldsToCheck()
    {
        return array(
            array(
                'id' => 'ID страницы',
                'name' => 'Название страницы для пользователя',
                'sef' => 'Шаблон ЧПУ страницы',
                'variables' => 'Переменные стрницы'
            ),
            array(
                'id' => 'Укажите ID страницы',
                'name' => 'Укажите название страницы'
            )
        );
    }
}