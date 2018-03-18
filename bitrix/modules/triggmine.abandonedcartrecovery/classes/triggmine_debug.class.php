<?php


class CTriggMineDebug {

    private static $debugToFile = false;
    private static $debugFilename = '/var/www/bitrix.log';

    private static function debugToFileEnabled()
    {
        return self::$debugToFile;
    }

    public static function getLogFilename()
    {
        return self::$debugFilename;
    }

    private static function debugToSessionEnabled()
    {
        if (!empty($_SESSION['triggmine']['triggmine_debug_to_session']) && $_SESSION['triggmine']['triggmine_debug_to_session'] == 1) {
            return true;
        } else {
            self::cleanDebugSession();
            return false;
        }
    }

    private static function cleanDebugSession()
    {
        unset($_SESSION['triggmine']['triggmine_debug_session']);
    }

    private static function setDebugToSession($value)
    {
        $_SESSION['triggmine']['triggmine_debug_to_session'] = (int)$value;
        if (!self::debugToSessionEnabled()) {
            self::cleanDebugSession();
        }
    }

    private static function showInfo($html){
        echo "<div style='position: absolute; top:10px; left: 10%; background-color: white; border: 1px solid black; padding: 10px; z-index: 1000;'>"
            . $html
            . "</div>";
    }

    public static function onPageLoad()
    {
        // to change options
        if (isset($_GET['triggmine_set_option'])) {
            $option = $_GET['triggmine_set_option'];
            $value = $_GET['option_value'];
            if (!empty($value) && !empty($option) && in_array($option, array('triggmine_is_on', 'triggmine_rest_api', 'triggmine_token', 'triggmine_cart_url'), true)) {
                COption::SetOptionString(CTriggMine::$MODULE_ID, $option, $value);
            }
        }

        // to enable/disable debug to session
        if (isset($_GET['triggmine_debug_to_session'])) {
            self::setDebugToSession($_GET['triggmine_debug_to_session']);
        }

        // to enable/disable debug to session
        if (isset($_GET['session_triggmine_is_on'])) {
            $_SESSION['triggmine']['session_triggmine_is_on'] = $_GET['session_triggmine_is_on'];
        }

        // to show settings
        if (isset($_GET['triggmine_show_settings']) || isset($_GET['triggmine_debug']) || isset($_GET['triggmine_settings'])) {
            $html = '';
            $html .= "<br/><b>Settings:</b><br/>";
            $html .= "module version:" . CTriggMine::getModuleVersion() . "<br/>";
            $html .= "bitrix charset:" . LANG_CHARSET . "<br/>";
            $html .= "on:" . CTriggMine::isOn(true) . "<br/>";
            $html .= "on for session:" . ($_SESSION['triggmine']['session_triggmine_is_on']) . "<br/>";
            $html .= "api:" . CTriggMine::triggmine_rest_api() . "<br/>";
            $html .= "token:" . CTriggMine::triggmine_token() . "<br/>";
            $html .= "cart:" . CTriggMine::triggmine_cart_url_full() . "<br/>";
            $html .= "triggmine_debug_to_session:" . (int)$_SESSION['triggmine']['triggmine_debug_to_session'] . "<br/>";
            $html .= "<br/><b>To change settings use:</b><br/>";
            $html .= "&triggmine_set_option=(triggmine_is_on|triggmine_rest_api|triggmine_token|triggmine_cart_url)&option_value=23434<br/>";
            $html .= "<br/><b>Other options</b><br/>";
            $html .= "<a href='?triggmine_debug=1&session_triggmine_is_on=Y'>Enable in session</a><br>";
            $html .= "<a href='?triggmine_debug=1&session_triggmine_is_on=N'>Disable in session</a><br>";
            $html .= "<a href='?triggmine_debug=1&triggmine_debug_to_session=1'>Enable debug to session</a><br>";
            $html .= "<a href='?triggmine_debug=1&triggmine_debug_to_session=0'>Disable debug to session</a><br>";
            $html .= "<a href='?triggmine_debug=1&triggmine_show_session=1'>Show debug session</a><br>";

            self::showInfo($html);
        }

        // to show erros
        if (isset($_GET['triggmine_show_errors'])) {
            $errors = CTriggMine::getErrors();
            $html = '';
            if (!empty($errors)) {
                $html .= "<br/>Errors:<br/>";
                $html .= var_dump($errors, true);
            } else {
                $html .= "<br/>No errors:<br/>";
            }
            self::showInfo($html);
        }

        // to see debug session
        if (isset($_GET['triggmine_show_debug_session']) || isset($_GET['triggmine_show_session'])) {
            $html = '<pre>';
            foreach ($_SESSION['triggmine']['triggmine_debug_session'] as $record){
                $html .= $record;
            }
            $html .= '</pre>';
            self::showInfo($html);
        }

        // to see session
        if (isset($_GET['triggmine_session'])) {
            $html = '<pre>';
            $html .= print_r($_SESSION, true);
            $html .= '</pre>';
            self::showInfo($html);
        }

        // to see cart
        if (isset($_GET['triggmine_cart'])) {
            $html = '<pre>';
            $dbBasketItems = CSaleBasket::getList();
            while ($arBasketItems = $dbBasketItems->Fetch()) {
                $html .= print_r($arBasketItems, true);
            }
            $html .= '</pre>';
            self::showInfo($html);
        }

        // to see product data
        if (isset($_GET['triggmine_product'])) {
            $data = CTriggMine::getProductData((int)$_GET['triggmine_product']);
            $html = '<pre>';
            $html .= print_r($data, true);
            $html .= '</pre>';
            self::showInfo($html);
        }
    }

    public static function debugVisitor($visitorId)
    {
        $string = "\n\n";
        $string .= 'Url: ' . $_SERVER['REQUEST_URI'] . "\n";
        $string .= 'Addr: ' . $_SERVER['REMOTE_ADDR'] . "\n";
        $string .= date('d-m-Y H:i:s') . " - $visitorId \n";
        self::_writeToLog($string);
    }

    public static function debugRequest($aData, $aResponse)
    {
        $string = "\n\n";
        $string .= date('d-m-Y H:i:s') . "\n";
        $string .= 'Url: ' . $_SERVER['REQUEST_URI'] . "\n";
        $string .= 'Addr: ' . $_SERVER['REMOTE_ADDR'] . "\n";
        $string .= 'Request: ' . json_encode(self::shortenData($aData)) . "\n";
        $string .= 'Response: ' . json_encode($aResponse) . "\n";
        $string .= 'Cookies: ' . json_encode($_COOKIE) . "\n";
        $string .= 'Post data: ' . json_encode($_POST) . "\n";
        self::_writeToLog($string);
    }

    public static function debugInfo($debugInfo)
    {
        if (!is_string($debugInfo)) {
            $debugInfo = json_encode($debugInfo);
        }

        $string = "\n\n";
        $string .= date('d-m-Y H:i:s') . "\n";
        $string .= $debugInfo . "\n";
        self::_writeToLog($string);
    }

    public static function debugLine($debugString)
    {
        $string = date('d-m-Y H:i:s') . ' ' . $debugString . "\n";
        self::_writeToLog($string);
    }

    private static function _writeToLog($content)
    {
        if (self::debugToFileEnabled()) {
            $filename = self::getLogFilename();
            $handle = fopen($filename, 'a+');
            if ($handle) {
                fwrite($handle, $content);
                fclose($handle);
            } else {
                echo "Unwritable log!";
            }
        }

        if (self::debugToSessionEnabled()) {
            if (empty($_SESSION['triggmine']['triggmine_debug_session'])){
                $_SESSION['triggmine']['triggmine_debug_session'] = array();
            }
            $_SESSION['triggmine']['triggmine_debug_session'][] = $content;
            if (sizeof($_SESSION['triggmine']['triggmine_debug_session']) > 50) {
                $_SESSION['triggmine']['triggmine_debug_session'] = array_slice($_SESSION['triggmine']['triggmine_debug_session'], -50);
            }
        }

    }

    const SHORTEN_TO = 155;

    public function shortenData($aArray)
    {
        foreach ($aArray as $id => $value)
        {
            if (is_array($value)) {
                $aArray[$id] = self::shortenData($value);
            } else {
                if (strlen($value) > CTriggMineDebug::SHORTEN_TO) {
                    $aArray[$id] = substr($value, 0, CTriggMineDebug::SHORTEN_TO);
                }


            }
        }
        return $aArray;
    }

} 