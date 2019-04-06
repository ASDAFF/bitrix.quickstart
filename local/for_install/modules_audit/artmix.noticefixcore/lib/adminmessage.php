<?php
/**
 * Created by Artmix.
 * User: Oleg Maksimenko <oleg.39style@gmail.com>
 * Date: 27.10.2014. Time: 12:50
 */

namespace Artmix\NoticeFixCore;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;

class AdminMessage
{

    /**
     * @param $content
     */
    public static function OnEndBufferContent(&$content)
    {
        global $APPLICATION;

        Loc::loadMessages(__FILE__);

        if ($APPLICATION->GetCurPage() == '/bitrix/admin/update_system.php') {

            $fileConfigPath = sprintf('%s/.settings_%s.php', dirname(__DIR__), LANGUAGE_ID);

            $configData = null;

            if (is_file($fileConfigPath)) {
                $configData = require($fileConfigPath);

                if (!is_array($configData)) {
                    $configData = array(
                        'notice_text' => '',
                    );
                }

            }

            if (is_array($configData)) {
                $noticeText = $configData['notice_text'];
            } else {
                $noticeText = '';
            }

            $noticeText = self::nl2brWithoutPre($noticeText);

            if (strlen(trim($noticeText))) {

                $m = new \CAdminMessage(array(
                    "MESSAGE" => Loc::getMessage('ARTMIX_NOTICEFIXCORE_MESSAGE_TITLE'),
                    "DETAILS" =>
                        $noticeText
                        . Loc::getMessage('ARTMIX_NOTICEFIXCORE_SETTINGS_NOTICE_TEXT_BOTTOM', array('#LANG#' => LANGUAGE_ID)),
                    "HTML" => true,
                ));

                ob_start();
                ?>
                <script>
                    function insertAfter(newElement, targetElement) {
                        //target is what you want it to go after. Look for this elements parent.
                        var parent = targetElement.parentNode;

                        //if the parents lastchild is the targetElement...
                        if (parent.lastchild == targetElement) {
                            //add the newElement after the target element.
                            parent.appendChild(newElement);
                        } else {
                            // else the target has siblings, insert the new element between the target and it's next sibling.
                            parent.insertBefore(newElement, targetElement.nextSibling);
                        }
                    }

                    BX.ready(function () {
                        var admTitle = document.getElementById('adm-title');
                        var newDiv = document.createElement('div');
                        newDiv.innerHTML = '<?= \CUtil::JSEscape($m->Show());?>';
                        insertAfter(newDiv, admTitle);

                        var fixCoreAgree = BX('artmix-noticefixcore-agree'),
                            installUpdatesButton = BX('install_updates_button');

                        if (fixCoreAgree && installUpdatesButton && !installUpdatesButton.disabled) {
                            installUpdatesButton.disabled = true;
                            BX.bind(fixCoreAgree, 'change', function (e) {
                                if (e.target.checked) {
                                    installUpdatesButton.disabled = false;
                                } else {
                                    installUpdatesButton.disabled = true;
                                }
                            });
                        } else {
                            BX('artmix-noticefixcore-agree-block').style.display = 'none';
                        }

                    });
                </script>
                <?
                $js = @ob_get_clean();
                $content = str_replace('</body>', $js . '</body>', $content);
            }

        }
    }

    private static function nl2brWithoutPre($string)
    {
        if (preg_match_all('/\<pre\>(.*?)\<\/pre\>/', $string, $match)) {
            foreach ($match as $a) {
                foreach ($a as $b) {
                    $string = str_replace('<pre>' . $b . '</pre>', "<pre>" . str_replace("<br />", PHP_EOL, $b) . "</pre>", $string);
                }
            }
        }

        return $string;
    }


}