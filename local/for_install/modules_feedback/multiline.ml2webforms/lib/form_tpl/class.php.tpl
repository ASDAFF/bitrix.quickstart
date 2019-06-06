<?php

namespace Ml2WebForms;

/**
 * Class ##webform_id_uc_first##WebForm
 * @package Ml2WebForms
 */
class ##webform_id_uc_first##WebForm extends WebForm {
    public function getId() {
        return '##webform_id##';
    }

    protected function useMl2WebFormsAntispam() {
        return true;
    }

    protected function getPostEventId() {
        return '##webform_post_event_id##';
    }

    protected function getPostEventTemplates() {
        return ##webform_post_template_id##;
    }

    public function getFields() {
        return ##webform_fields##;
    }
}
