<?
namespace Ns\Bitrix\Helper\IBlock;

/**
 *
 */
class Validator extends \Ns\Bitrix\Helper\HelperCore
{

    public function email($email='', $xss=True) {
        if ($xss) {
            $email = $this->xss($email);
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        } else {
            return False;
        }
    }

    public function xss($value = '') {
        return htmlspecialchars(trim($value));
    }

}


?>