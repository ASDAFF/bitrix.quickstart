<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 29.01.2021
 * Time: 16:58
 */

namespace Helper;

use Bitrix\Main\Loader;

class CUserRegistration
{
    /**
     * @param $arFields
     * @return bool
     * @throws Exception
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    function OnBeforeUserRegisterCheckINN(&$arFields)
    {
        $inn = (int)$arFields['UF_PERSON_INN'];
        $checkINN = new CCheckINN;
        $checkCSV = $checkINN->checkingCSV($inn, 1, $_SERVER['DOCUMENT_ROOT'] . '/contractor.csv');
        $checkHL = $checkINN->checkingHL($inn, 2);

        if ($checkHL == false && !$checkCSV && isset($arFields['UF_PERSON_INN'])) {
            $GLOBALS['APPLICATION']->ThrowException('Вашего ИНН нет в базе - зарегистрируйте нового контрагента, кликнув ссылку внизу формы.');
            return false;
        }

    }

    /**
     * @param $arFields
     * @return mixed
     * @throws Exception
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    function OnAfterUserRegisterHandler(&$arFields)
    {
        if ($arFields["USER_ID"] > 0) {
            global $USER_FIELD_MANAGER;

            $checkINN = new CCheckINN;
            $checkHL = $checkINN->checkinnHL($arFields['UF_PERSON_INN'], 2);
            if ($checkHL > 0) {
                // прикрепляем организацию к пользователю
                $fields = Array(
                    "UF_COUNTERPARTNERS" => array('ID' => $checkHL[0]['ID'])
                );
                $USER_FIELD_MANAGER->Update("USER", $arFields["USER_ID"], $fields);
            } else {
                self::addContractor($arFields['UF_PERSON_INN'], $arFields["USER_ID"]);
            }
        }
        return $arFields;
    }

    /**
     * @param $inn
     * @param $user
     * @throws Exception
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    function addContractor($inn, $user)
    {
        global $USER_FIELD_MANAGER;
        // добавляем нового контрагента
        // и добавляем его к пользователю
        Loader::includeModule("highloadblock");
        $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById(2)->fetch();
        $ent = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $ent->getDataClass();


        // определяем данные контрагента
        $url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party';
        $fields = array(
            'query' => urlencode($inn)
        );


        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => false,

            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Token 3a23729ee5bf5d31d9781136bbd04429d3a72499'
            ),
            CURLOPT_POSTFIELDS => json_encode($fields)
        ));

        $response = curl_exec($ch);
        $responseData = json_decode($response, TRUE);

        //AddMessage2Log($responseData);

        $result = $entity_data_class::add(
            array(
                'UF_NAME' => $responseData['suggestions'][0]['data']['name']['short_with_opf'],
                'UF_INN' => $inn,
                'UF_OGRN' => $responseData['suggestions'][0]['data']['ogrn'],
                'UF_KPP' => $responseData['suggestions'][0]['data']['kpp'],
                'UF_ADDRESS' => $responseData['suggestions'][0]['data']['address']['unrestricted_value'],
                'UF_ACTIVE' => 1
            )
        );
        if ($result->isSuccess()) {
//                		  echo 'ДОБАВЛЕН ' . $result->getId();
        } else {
//		                  echo 'ОШИБКА ' . implode(', ', $result->getErrors());
        }

        $fields = Array(
            "UF_COUNTERPARTNERS" => array('ID' => $result->getId())
        );

        $USER_FIELD_MANAGER->Update("USER", $user, $fields);
    }
}