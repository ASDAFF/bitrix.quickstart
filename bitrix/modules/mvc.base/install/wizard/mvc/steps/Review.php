<?php


use Bitrix\Main\Loader;
use DigitalWand\MVC\ComponentBuilder;

class ReviewStep extends CWizardStep
{
    private $maxPages = 10;

    function InitStep()
    {
        //ID шага
        $this->SetStepID("review");

        //Заголовок
        $this->SetTitle("Подтверждение создания компонента");
        $this->SetSubTitle("Проверьте настройки компонента перед его созданием");

        //Навигация
        $this->SetPrevStep("routes");
        $this->SetNextStep("success");
        $this->SetCancelStep("cancel");

        $wizard =& $this->GetWizard(); // Получаем ссылку на объект мастера
        $wizard->SetDefaultVar("active", "Y"); //Устанавливаем значение по умолчанию
    }

    function ShowStep()
    {
        $wizard =& $this->GetWizard();
        $this->content .= '<table class="data-table">';

        $this->content .= '<tr><th align="right">Пространство имён компонента:</th><td>';
        $this->content .= $wizard->GetVar('namespace');
        $this->content .= '</td></tr>';

        $this->content .= '<tr><th align="right">Название компонента:</th><td>';
        $this->content .= $wizard->GetVar('name');
        $this->content .= '</td></tr>';

        $this->content .= '<tr><th align="right">Название компонента для пользователя:</th><td>';
        $this->content .= $wizard->GetVar('title');
        $this->content .= '</td></tr>';

        $this->content .= '<tr><th align="right">Описание компонента для пользователя:</th><td>';
        $this->content .= $wizard->GetVar('description');
        $this->content .= '</td></tr>';

        $this->content .= '</table>';

        $this->content .= '<br /><div class="wizard-note-box">Настройки ЧПУ:</div>';

        $this->content .= '<table class="data-table">';

        $variables = $wizard->GetVars();
        list($fieldsToCheck, $mandatoryFields) = RoutesStep::getFieldsToCheck();

        Loader::includeModule('digitalwand.mvc');
        for ($i = 0; $i < RoutesStep::$maxPages; $i++) {
            if (!isset($variables['page_id_' . $i])) {
                continue;
            }
            foreach ($fieldsToCheck as $name => $caption) {
                if ($name == 'variables') {
                    $variables['page_' . $name . '_' . $i] = ComponentBuilder::extractVariablesFromSEF($variables['page_sef_' . $i], $variables['page_' . $name . '_' . $i]);
                    $wizard->SetVar('page_' . $name . '_' . $i, $variables['page_' . $name . '_' . $i]);
                }
                $this->content .= '<tr><th align="right">' . $caption . ':</th><td>';
                $this->content .= $variables['page_' . $name . '_' . $i];
                $this->content .= '</td></tr>';
            }
            $this->content .= '<tr><th><hr></th><td><hr></td></tr>';
        }

        $this->content .= '</table>';
    }

    function OnPostForm()
    {
        Loader::includeModule('digitalwand.mvc');
        $wizard =& $this->GetWizard();

        if ($wizard->IsCancelButtonClick()) {
            return;
        }

        $variables = $wizard->GetVars();
        list($fieldsToCheck, $mandatoryFields) = RoutesStep::getFieldsToCheck();
        $converted = array(
            'name' => $variables['name'],
            'title' => $variables['title'],
            'namespace' => $variables['namespace'],
            'description' => $variables['description'],
            'routes' => array()
        );
        for ($i = 0; $i < RoutesStep::$maxPages; $i++) {
            if (!isset($variables['page_id_' . $i])) {
                continue;
            }

            $route = array();
            foreach ($fieldsToCheck as $name => $caption) {
                $route[$name] = $variables['page_' . $name . '_' . $i];
            }
            $converted['routes'][] = $route;
        }

        $builder = new ComponentBuilder($converted);

        if ($builder->build()) {
            $wizard->SetVar('componentPath', $builder->getComponentRelativePath());
        } else {
            $this->SetError('Во время генерации компонента произошла ошибка!');
        }

    }
}