<?php

class SuccessStep extends CWizardStep
{
    function InitStep()
    {
        //ID шага
        $this->SetStepID("success");

        //Заголовок
        $this->SetTitle("Работа мастера успешно завершена");

        //Навигация
        $this->SetCancelStep("success");
        $this->SetCancelCaption("Готово");
    }

    function ShowStep()
    {
        $this->content .= "Новый компонент создан!";

        $wizard =& $this->GetWizard();
        $path = urlencode($wizard->GetVar('componentPath'));
        $this->content .= '<a target="_blank" href="/bitrix/admin/fileman_admin.php?&path=' . $path . '&lang=' . LANGUAGE_ID . '">Посмотреть файлы компонента</a>';
    }
}
