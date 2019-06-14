<?php
/**
 * @var \Ml2WebForms\WebFormTemplate $arResult['TPL']
 * @var array $arParams
 *
 * $arResilt = array(
 *     'TPL' => \Ml2WebForms\WebFormTemplate
 *     'FIELDS'     => array of webform fields from WebFrom config
 * )
 */
use \Ml2WebForms\WebForm;
?>
<script type="text/javascript">
    var Ml2WebForms_<?php echo $arParams['ID']?> = Ml2WebForms_default;
    Ml2WebForms_<?php echo $arParams['ID']?>.webform_id = "<?php echo $arParams['ID']?>";

    // init webform on document load
    if (typeof window.addEventListener != 'undefined') {
        window.addEventListener("load", function() { Ml2WebForms_<?php echo $arParams['ID']?>.init(); }, false);
    } else {
        window.attachEvent("onload", function() { Ml2WebForms_<?php echo $arParams['ID']?>.init(); });
    }

</script>
<?php echo $arResult['TPL']->getFormBegin();?>
    <?php
    foreach ($arResult['FIELDS'] as $field => $params) {
        switch ($params['type']) {
            case WebForm::FIELD_TYPE_SELECT:
                echo '<div>'.($params['required']?'<span class="req">*</span>':'').'<select name="' . $field . '">';
                if (!isset($params['required']) || !$params['required']) {
                    echo '<option value="">' . (isset($params['title']) ? $params['title'][LANGUAGE_ID] : '-') . '</option>';
                }
                foreach ($params['list'] as $nn => $row) {
                    echo '<option value="' . $nn . '">' . $row['title'][LANGUAGE_ID] . '</option>';
                }
                echo '</select></div>';
                break;
            case WebForm::FIELD_TYPE_SELECT_MULTIPLE:
                echo '<div>'.($params['required']?'<span class="req">*</span>':'').'<select name="' . $field . '[]" multiple>';
                if (!isset($params['required']) || !$params['required']) {
                    echo '<option value="">' . (isset($params['title']) ? $params['title'][LANGUAGE_ID] : '-') . '</option>';
                }
                foreach ($params['list'] as $nn => $row) {
                    echo '<option value="' . $nn . '">' . $row['title'][LANGUAGE_ID] . '</option>';
                }
                echo '</select></div>';
                break;
            case WebForm::FIELD_TYPE_RADIO:
                echo '<div>' . (isset($params['title']) ? $params['title'][LANGUAGE_ID] : '') . ''.($params['required']?'<span class="req">*</span>':'').'<br>';
                if (!isset($params['required']) || !$params['required']) {
                    echo '<input type="radio" id="' . $arResult['TPL']->getFormName() . '_radio_' . $field . '" name="' . $field . '"> <label for="' . $arResult['TPL']->getFormName() . '_radio_' . $field . '">-</label><br>';
                }
                foreach ($params['list'] as $nn => $row) {
                    echo '<input type="radio" id="' . $arResult['TPL']->getFormName() . '_radio_' . $field . '_' . $nn . '" name="' . $field . '" value="' . $nn . '"> <label for="' . $arResult['TPL']->getFormName() . '_radio_' . $field . '_' . $nn . '">' . $row['title'][LANGUAGE_ID] . '</label><br>';
                }
                echo '</div>';
                break;
            case WebForm::FIELD_TYPE_TEXTAREA:
                echo '<div>'.($params['required']?'*':'').'<textarea name="' . $field . '" placeholder="' . (isset($params['title']) ? $params['title'][LANGUAGE_ID] : '') . '">' . (isset($params['value']) ? $params['value'] : '') . '</textarea></div>';
                break;
            case WebForm::FIELD_TYPE_HIDDEN:
                echo '<div style="display:none"><input type="hidden" name="' . $field . '" value="' . (isset($params['value']) ? $params['value'] : '') . '"></div>';
                break;
            case WebForm::FIELD_TYPE_CHECKBOX:
                echo '<div>'.($params['required']?'<span class="req">*</span>':'').'<input type="checkbox" name="' . $field . '" id="' . $arResult['TPL']->getFormName() . '_checkbox_' . $field . '" value="' . (isset($params['value']) ? $params['value'] : '1') . '"> <label for="' . $arResult['TPL']->getFormName() . '_checkbox_' . $field . '">' . (isset($params['title']) ? $params['title'][LANGUAGE_ID] : '') . '</label></div>';
                break;
            case WebForm::FIELD_TYPE_FILE:
                echo '<div>'.($params['required']?'<span class="req">*</span>':'').'<label for="' . $arResult['TPL']->getFormName() . '_file_' . $field . '">' . (isset($params['title']) ? $params['title'][LANGUAGE_ID] : '') . '</label> <input type="file" name="' . $field . '" id="' . $arResult['TPL']->getFormName() . '_file_' . $field . '" value=""></div>';
                break;
            case WebForm::FIELD_TYPE_TEXT:
            default:
                echo '<div>'.($params['required']?'<span class="req">*</span>':'').'<input type="text" name="' . $field . '" value="' . (isset($params['value']) ? $params['value'] : '') . '" placeholder="' . (isset($params['title']) ? $params['title'][LANGUAGE_ID] : '') . '"></div>';
                break;
        }
    }
    ?>
    <div><input type="submit" value="Отправить"></div>
<?php echo $arResult['TPL']->getFormEnd();?>
