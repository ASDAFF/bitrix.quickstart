<?php

namespace Lema\Subscribe;

use \Lema\Common\User;



\Bitrix\Main\Loader::IncludeModule('subscribe');

/**
 * Class Subscribe
 * @package Lema\Subscribe
 */
class Subscribe
{
    const SUBSCRIBE_ID = 1;
    
    public static function addSubscribe($email)
    {
        $userId = User::get()->GetId();
        
        $fields = Array(
            'USER_ID' => $userId,
            'FORMAT' => 'html',
            'EMAIL' => $email,
            'ACTIVE' => 'Y',
            'RUB_ID' => array(static::SUBSCRIBE_ID),
        );

        $subscr = new \CSubscription;
        $ID = $subscr->Add($fields);
        if (!empty($ID))
            $subscr->Authorize($ID);

        return !empty($ID);

    }
    
    public static function hasSubscribe($email)
    {
        $res = \CSubscription::GetList(array(), array('EMAIL' => $email, 'RUBRIC' => static::SUBSCRIBE_ID));
        return (bool) $res->SelectedRowsCount();
    }
    
    public static function getByEmail($email)
    {
        
    }
}