<?php

class DescriptionStep extends CWizardStep
{
    function InitStep()
    {
        //ID шага
        $this->SetStepID("description");

        //Заголовок
        $this->SetTitle(GetMessage("MVC_WIZARD_DESCRIPTION_TITLE"));
        $this->SetSubTitle("Введите имя и описание компонента");

        //Навигация
        $this->SetNextStep("routes");
        $this->SetCancelStep("cancel");

        $wizard =& $this->GetWizard(); // Получаем ссылку на объект мастера
        $wizard->SetDefaultVar("active", "Y"); //Устанавливаем значение по умолчанию
    }

    function ShowStep()
    {
        $this->content .= '<table class="data-table">';

//        $this->content .= '<tr><th align="right">Активен:</th><td>' . $this->ShowCheckBoxField("active", "Y") . '</td></tr>';

        $this->content .= '<tr><th align="right"><span class="wizard-required">*</span>Пространство имён компонента (латиницей):</th><td>';
        $this->content .= $this->ShowInputField("text", "namespace", Array("size" => 25));
        $this->content .= '</td></tr>';

        $this->content .= '<tr><th align="right"><span class="wizard-required">*</span>Название компонента (латиницей):</th><td>';
        $this->content .= $this->ShowInputField("text", "name", Array("size" => 25));
        $this->content .= '</td></tr>';

        $this->content .= '<tr><th align="right"><span class="wizard-required">*</span>Название компонента для пользователя:</th><td>';
        $this->content .= $this->ShowInputField("text", "title", Array("size" => 25));
        $this->content .= '</td></tr>';

        $this->content .= '<tr><th align="right"><span class="wizard-required"></span>Описание компонента для пользователя:</th><td>';
        $this->content .= $this->ShowInputField("text", "description", Array("size" => 25));
        $this->content .= '</td></tr>';
//
//        $this->content .= '<tr><th align="right">Имя:</th><td>' . $this->ShowInputField("text", "user_name", Array("size" => 25)) . '</td></tr>';
//        $this->content .= '<tr><th align="right">Фамилия:</th><td>' . $this->ShowInputField("text", "user_surname", Array("size" => 25)) . '</td></tr>';
//
        $this->content .= '</table>';
        $this->content .= '<br /><div class="wizard-note-box"><span class="wizard-required">*</span> Поля, обязательные для заполнения.</div>';
    }

    function OnPostForm()
    {
        $wizard =& $this->GetWizard();

        if ($wizard->IsCancelButtonClick()) {
            return;
        }

        $namespace = $wizard->GetVar("namespace");
        if (empty($namespace)) {
            $this->SetError("Пространство имён компонента не может быть пустым.", "namespace");
        }

        $name = $wizard->GetVar("name");
        if (empty($name)) {
            $this->SetError("Название компонента не может быть пустым.", "name");
        }

        if (!count($this->GetErrors())) {
            $componentName = $namespace . ':' . $name;

            $component = new CBitrixComponent;
            ob_start();
            if ($component->initComponent($componentName)) {
                $this->SetError("Компонент с таким названием уже существует.", "name");
            }
            ob_end_clean();
        }
    }
}