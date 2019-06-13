<?
namespace Slam\Easyform;
class event
{
    public function eventHandler(\Bitrix\Main\Entity\Event $event)
    {
        $result = new \Bitrix\Main\Entity\EventResult;
        $result->modifyFields(array('result' => true));
        return $result;
    }
}