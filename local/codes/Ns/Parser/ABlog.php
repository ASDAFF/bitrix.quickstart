<?
namespace Ns\Parser;
set_time_limit (300);
/**
*
*/
class ABlog extends ParserCore
{
    const ABLOG_IBLOCK_ID = 19;

    private $objElement;

	function __construct($type = false)
	{
        /**
         * Пытаемся подключить модуль инфоблоков
         */
        try
        {
            \Ns\Bitrix\Modules::IncludeModule('iblock');
        }
        catch (\Exception $e)
        {
            prentExpection($e->getMessage());
        }
        $this->objElement = new \CIBlockElement();

    }

    public function go()
    {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/XML.php";
        $xml = file_get_contents("http://alfabank.by/a-blog.xml");
        $rss = new \XML($xml);
        foreach ($rss->rss->channel->item as $it) {
            $dom = \phpQuery::newDocumentHTML($it->childByName("content:encoded"));
            $date = date_create($it->pubDate->getData());
            $arResult = array(
                "IBLOCK_ID" => self::ABLOG_IBLOCK_ID,
                "NAME" => $it->title->getData(),
                "DATE_ACTIVE_FROM" => date_format($date, 'd.m.Y'),
                "PREVIEW_TEXT" => $it->description->getData(),
                "PREVIEW_PICTURE" => \CFile::MakeFileArray($dom->find('img')->attr('src')),
                "DETAIL_TEXT" => $it->childByName("content:encoded"),
                "CODE" => \Ns\Bitrix\Helper::Create('iblock')->useVariant('text')->translite($it->title->getData()),
                "PROPERTY_VALUES" => array("ORIGINAL_LINK" => $it->link->getData())
            );
            $this->objElement->Add($arResult);
            if ($this->objElement->LAST_ERROR) {
                prentExpection($this->objElement->LAST_ERROR);
            }
        }

        return true;
	}

}

?>