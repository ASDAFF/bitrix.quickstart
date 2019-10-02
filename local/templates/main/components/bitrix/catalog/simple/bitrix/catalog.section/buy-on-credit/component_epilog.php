<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<?$APPLICATION->IncludeComponent(
    "custom:form.prototype",
    "analogue-good",
    array(
        "FORM_ID" => 5,
        "FORM_ACTION" => "form-analogue",
        "SUCCESS_MESSAGE" => "Заявка на подбор аналогичного товара отправлена. В ближайшее время с Вами свяжется наш менеджер и поможет подобрать аналогичный товар.",
        "FIELDS" => array(
            "form_text_14",
            "form_text_15",
            "form_text_16",
            "form_text_17",
            "form_checkbox_24"
        ),
    ),
    false,
    array(
        "HIDE_ICONS" => "Y",
    )
);?>

<?$APPLICATION->IncludeComponent(
    "custom:form.prototype",
    "announce-good",
    array(
        "FORM_ID" => 6,
        "FORM_ACTION" => "form-announce",
        "SUCCESS_MESSAGE" => "Заявка на поступление товара отправлена. Как только товар появится в продаже, на Ваш телефон или e-mail будет выслано уведомление.",
        "FIELDS" => array(
            "form_text_18",
            "form_text_19",
            "form_text_20",
            "form_text_21",
            "form_checkbox_25"
        ),
    ),
    false,
    array(
        "HIDE_ICONS" => "Y",
    )
);?>