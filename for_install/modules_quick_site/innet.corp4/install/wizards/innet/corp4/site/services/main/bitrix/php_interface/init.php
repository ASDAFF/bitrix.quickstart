<?
function INNETGetPrice($price = '', $saleValue = '', $saleType = '', $currency = ''){
    $arPrice = array();
    if ($saleValue > 0) {
        if ($saleType == 'percentage') {
            $arPrice['PRICE'] = number_format($price - ($price * $saleValue / 100), 0, '', ' ') . ' ' . $currency;
            $arPrice['OLD_PRICE'] = number_format($price, 0, '', ' ') . ' ' . $currency;
            $arPrice['PRICE_DIFF'] = number_format(($price * $saleValue / 100), 0, '', ' ') . ' ' . $currency;
        } else if ($saleType == 'amount') {
            $arPrice['PRICE'] = number_format($price - $saleValue, 0, '', ' ') . ' ' . $currency;
            $arPrice['OLD_PRICE'] = number_format($price, 0, '', ' ') . ' ' . $currency;
            $arPrice['PRICE_DIFF'] = number_format($saleValue, 0, '', ' ') . ' ' . $currency;
        } else if ($saleType == 'final_price') {
            $arPrice['PRICE'] = number_format($saleValue, 0, '', ' ') . ' ' . $currency;
            $arPrice['OLD_PRICE'] = number_format($price, 0, '', ' ') . ' ' . $currency;
            $arPrice['PRICE_DIFF'] = number_format(($price - $saleValue), 0, '', ' ') . ' ' . $currency;
        }
    } else {
        $arPrice['PRICE'] = number_format($price, 0, '', ' ') . ' ' . $currency;
    }

    return $arPrice;
}

AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("INNETQuestions", "ResponseUser"));

class INNETQuestions
{
    function ResponseUser(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == "#INNET_IBLOCK_ID_QUESTIONS_ANSWER#") {
            $arFilter = Array("IBLOCK_ID" => "#INNET_IBLOCK_ID_QUESTIONS_ANSWER#", "ID" => $arFields['ID'], "PROPERTY_SEND_MAIL_VALUE" => "Y");
            $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), Array("IBLOCK_ID", "ID", "PROPERTY_QUESTION_REPLY", "PROPERTY_QUESTION_MAIL", "PROPERTY_QUESTION", "DATE_ACTIVE_FROM"));
            if ($ob = $res->GetNextElement()) {
                $data = $ob->GetFields();

                if ($data['PROPERTY_QUESTION_REPLY_VALUE']['TEXT'] != '') {
                    $arEventFields = array(
                        "NEW_DATE_ACTIVE_FROM" => $data["DATE_ACTIVE_FROM"],
                        "NEW_QUESTION_REPLY" => $data['PROPERTY_QUESTION_REPLY_VALUE']['TEXT'],
                        "NEW_QUESTION_EMAIL_USER" => $data['PROPERTY_QUESTION_MAIL_VALUE'],
                        "NEW_QUESTION_USER" => $data['PROPERTY_QUESTION_VALUE']['TEXT']
                    );

                    CEvent::Send("INNET_NEW_QUESTION_REPLY", "#SITE_ID#", $arEventFields);
                }
            }
        }
    }
}
?>