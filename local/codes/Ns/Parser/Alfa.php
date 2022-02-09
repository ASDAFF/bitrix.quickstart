<?
namespace Ns\Parser;
set_time_limit (300);
/**
*
*/
class Alfa extends ParserCore
{
    const ALFA_NEWS_IBLOCK_ID = 9;
	const DETAIL_TEXT_TYPE = "html";

	private $dom;
	private $arFields;
    private $objElement;
    private $checker;

	function __construct()
	{
		$html = file_get_contents("http://www.alfabank.by/bank/info/press/news/all.html");
		$this->dom = \phpQuery::newDocumentHTML($html);
		/**
		 * Устанавливаем по дефолту инфоблок новостей для альфы
		 */
        $this->arFields["IBLOCK_ID"] = self::ALFA_NEWS_IBLOCK_ID;
		$this->arFields["DETAIL_TEXT_TYPE"] = self::DETAIL_TEXT_TYPE;
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
        try
        {
            $this->checker = \Ns\Bitrix\Helper::Create('iblock')->useVariant('checker');
        }
        catch (\Exception $e)
        {
            prentExpection($e->getMessage());
        }
        foreach ($this->dom->find("div.newsItem") as $article)
        {
            /**
             * Получение Даты создания и Имени новости
             */
            $pq = \phpQuery::pq($article);
            $this->arFields["ACTIVE_FROM"] = $this->clearTrash($pq->find("div.date")->html()); // парсим текст сообщения в html формате
            /**
             * Получение полной новости
             */
            var_dump($pq->find("a")->attr('href'));
            $html = file_get_contents("http://www.alfabank.by" . $pq->find("a")->attr('href'));
            $fullArticle = \phpQuery::newDocumentHTML($html);
                /**
                 * Получение текста полной новости
                 */
                $this->arFields["NAME"] = $this->clearTrash($fullArticle->find("div.newsItem")->find("h2")->text()); // парсим текст сообщения в html формате
                $tmpDetail = '';
                foreach ($fullArticle->find("div.newsItem > div:not(.date)") as $val)
                {
                    $p = \phpQuery::pq($val);
                    $tmpDetail .= $p->removeAttr('class')->removeAttr('style')->html();
                }
                $this->arFields["DETAIL_TEXT"] = $this->clearTrash(iconv("windows-1251", "UTF-8", $tmpDetail));

            $this->Add();

        }
        return true;
	}

    private function clearTrash($string)
    {
        return str_replace("</body> </html>", "", trim($string));
    }

    private function Add()
    {
         if ($this->checker->withParams(array("NAME" => $this->arFields["NAME"], "IBLOCK_ID" => $this->arFields["IBLOCK_ID"]))->isUnique())
            {
                $this->objElement->Add($this->arFields);
                if ($this->objElement->LAST_ERROR)
                {
                    prent($this->objElement->LAST_ERROR);
                }
            }
    }
}

?>