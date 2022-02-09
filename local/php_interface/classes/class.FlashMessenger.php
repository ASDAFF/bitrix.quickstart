<?php

class FlashMessenger
{
    public static function addFlash($message, $category = 'default') 
    {
        if (!is_array($_SESSION['flashMessages'])) {
            $_SESSION['flashMessages'] = array();
        }
        if (!is_array($_SESSION['flashMessages'][$category])) {
            $_SESSION['flashMessages'][$category] = array();
            $_SESSION['flashMessages'][$category]['messages'] = array();
            $_SESSION['flashMessages'][$category]['counter'] = 0;
        }
        $_SESSION['flashMessages'][$category]['counter']++;
        $_SESSION['flashMessages'][$category]['messages'][] = $message;
    }

    public static function getFlash($delimeter = '<br />', $category = 'default', $returnArray = false) 
    {
        if (is_array($_SESSION['flashMessages']) && !empty($_SESSION['flashMessages'][$category]) 
            && !empty($_SESSION['flashMessages'][$category]['messages']) && $_SESSION['flashMessages'][$category]['counter'] > 0
        ) {
            if (!$returnArray) {
                $messages = implode($delimeter, $_SESSION['flashMessages'][$category]['messages']);
                return $messages;
            
            } else {
                return $_SESSION['flashMessages'][$category]['messages'];
            }
            
        } else {
            if ($returnArray) {
                return array();
            } else {
                return '';
            }
        }
    }

    public static function reduceFlashMessages()
    {
        session_start();
        if (is_array($_SESSION['flashMessages'])) {
            foreach ($_SESSION['flashMessages'] as $key => &$category) {
                $category['counter'] -= 1;
                if ($category['counter'] < 0) {
                    unset($_SESSION['flashMessages'][$key]);
                }
            }
        }    
    }
}