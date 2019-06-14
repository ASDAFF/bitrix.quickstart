<?//подключается при вызове модуля
CModule::AddAutoloadClasses(
    'aamedia.dutil',
    array(
        'AAM\\DUtil\\CMain' => 'lib/CMain.php',
    )
);

class CHandlers
{
    /**
     * Work with robots.txt
     *
     * When option "setup for develop" is on,
     * rename original robots.txt to robots_old.txt
     * then create new robots.txt
     *
     * When option is off,
     * delete dev version robots.txt
     * then rename original file back
     *
     * @param \Bitrix\Main\Event $value
     */
    function Handler_update_devsrv(\Bitrix\Main\Event $value)
    {
        if (COption::GetOptionString('aamedia.dutil', 'site_index') === "Y")
            if ($value->getParameters()["value"] === "Y")
            {
                self::CreateRobotsTXT();
            }
            else
            {
                self::DeleteRobotsTXT();
            }
    }

    /**
     * show CAdminMessage when admin authorize
     */
    function HandlerUserAuthorize()
    {
        global $USER;
        if ($USER->IsAdmin())
        {
            AAM\DUtil\CMain::AdminNotify();
        }
    }

    static function CreateRobotsTXT()
    {
        $fileRobots = "robots.txt";
        $fileRobotsOld = "robots_old.txt";
        $robotsDevText = "User-Agent: *\nDisallow: /";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobotsOld))
        {
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobots))
            {
                rename($_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobots, $_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobotsOld);
            }

            $file = fopen($_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobots, "w");
            fwrite($file, $robotsDevText);
            fclose($file);
        }
    }

    static function DeleteRobotsTXT()
    {
        $fileRobots = "robots.txt";
        $fileRobotsOld = "robots_old.txt";

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobotsOld))
        {
            unlink($_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobots);
            rename($_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobotsOld, $_SERVER["DOCUMENT_ROOT"] . "/" . $fileRobots);
        }
    }
}