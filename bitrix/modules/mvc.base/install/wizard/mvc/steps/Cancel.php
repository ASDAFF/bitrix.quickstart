<?php

class CancelStep extends CWizardStep
{
    function InitStep()
    {
        $this->SetTitle("Мастер прерван");
        $this->SetStepID("cancel");
        $this->SetCancelStep("cancel");
        $this->SetCancelCaption("Закрыть");
    }

    function ShowStep()
    {
        $this->content .= "Мастер создания компонента прерван.";
    }
}