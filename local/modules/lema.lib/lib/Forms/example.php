<?php

return ;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Lema\Common\Helper;

$form = new \Lema\Forms\Form();
$form
    ->setRules(array(
        array('name, name3, name4, email, phone', 'required'),
        array('email', 'email'),
        array('phone2', 'phone'),
        array('phone', 'phone', array('message' => 'Телефон должен быть в формате +7 (999) 666-33-11')),
        array('name2', 'length', array('min' => 3, 'max' => 10)),
        array('name', 'length', array('min' => 3, 'max' => 10, 'message' => 'Имя должно быть больше {min} и меньше {max} символов')),
    ))
    ->setFields(array(
        'name' => 'а',
        'name2' => 'а',
        'name3' => '',
        'email' => 'email',
        'phone' => '89151555',
        'phone2' => '89151555',
    ));

if($form->validate())
    echo 'Ok!';
else
    var_dump($form->getErrors());

/*
  result:
array(7) {
    ["name3"]=> "Введите name3"
    ["name4"]=> "Введите name4"
    ["email"]=> "Неверный формат E-mail"
    ["phone2"]=> "Неверный формат телефона"
    ["phone"]=> "Телефон должен быть в формате +7 (999) 666-33-11"
    ["name2"]=> "Поле name2 должно содержать от 3 до 10 символов"
    ["name"]=> "Имя должно быть больше 3 и меньше 10 символов"
}
 */

/* ajax form with send email & add record */

$form = new \Lema\Forms\AjaxForm();
$form
    ->setRules(array(
        array('name, name3, name4, email, phone', 'required'),
        array('email', 'email'),
        array('phone2', 'phone'),
        array('phone', 'phone', array('message' => 'Телефон должен быть в формате +7 (999) 666-33-11')),
        array('name2', 'length', array('min' => 3, 'max' => 10)),
        array('name', 'length', array('min' => 3, 'max' => 10, 'message' => 'Имя должно быть больше {min} и меньше {max} символов')),
    ))
    ->setFields(array(
        'name' => 'а',
        'name2' => 'а',
        'name3' => '',
        'email' => 'email',
        'phone' => '89151555',
        'phone2' => '89151555',
    ));
echo '<pre>';
if($form->validate())
{
    $status = true;

    $status = $status && $form->sendMessage('FEEDBACK_FORM', array(
            'AUTHOR' => $form->getField('name'),
            'TEXT' => $form->getField('text'),
        ));

    $status = $status && $form->addRecord(2, array(
            'NAME' => Helper::enc($form->getField('name')),
            'PREVIEW_TEXT' => Helper::enc($form->getField('text')),
            'ACTIVE' => 'Y',
        ));

    if($status)
    {
        //ok
    }
    else
    {
        //error
        var_dump($form->getErrors());
    }
}
else
    var_dump($form->getErrors());