<?
namespace Ns\Parser;
set_time_limit (300);
/**
*
*/
class AlfaOffices extends ParserCore
{
	const ALFAOFFICES_IBLOCK_ID = 11;

	private $dom;
	private $arFields;
    private $objElement;
    private $checker;

	function __construct()
	{
		$html = file_get_contents("http://www.alfabank.by/bank/contacts/offices/3.html");
		$this->dom = \phpQuery::newDocumentHTML($html);
		/**
		 * Устанавливаем по дефолту инфоблок новостей для альфы
		 */
		$this->arFields["IBLOCK_ID"] = self::ALFAOFFICES_IBLOCK_ID;
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
        $table = \phpQuery::pq($this->dom->find("div#catalog")->find("table#tbl"));
        foreach ($table->find("tr") as $tr)
        {
            $this->arFields = array();
            $this->arFields["IBLOCK_ID"] = self::ALFAOFFICES_IBLOCK_ID;
            /**
             * Получение Даты создания и Имени новости
             */
            $tr = \phpQuery::pq($tr);
            $this->arFields["NAME"] = $tr->find('td:eq(0)')->find("a")->text();
            $infoLink = $tr->find('td:eq(0)')->find("a")->attr("href");
            $this->arFields["PROPERTY_VALUES"]["STATE"] = $tr->find('td:eq(1)')->text();
            $this->arFields["PROPERTY_VALUES"]["CITY"] = $tr->find('td:eq(2)')->text();
            $this->arFields["PROPERTY_VALUES"]["BIND_CITY"] = $this->findCity($this->arFields["PROPERTY_VALUES"]["CITY"]);
            $this->arFields["PROPERTY_VALUES"]["ADDRESS"] = $tr->find('td:eq(3)')->text();

            $html = file_get_contents("http://www.alfabank.by" . $infoLink);
            $fullInfo = \phpQuery::newDocumentHTML($html);

            $info = \phpQuery::pq($fullInfo->find("div.content"));

            /**
             * Получение инфо о работе отделения для физ лиц
             */
            foreach ($info->find("div.section.s1")->find('table')->find('tr') as $tr)
            {
                $tr = \phpQuery::pq($tr);
                if ($tr->find('td:eq(0)')->text())
                {
                    $this->arFields["PROPERTY_VALUES"]["OPERATION_TIME_FIZ"][] = $tr->find('td:eq(0)')->text() . " " . $tr->find('td:eq(1)')->text();
                }
            }
            /**
             * Получение инфо о работе отделения для юр лиц
             */
            foreach ($info->find("div.section.s2")->find('table')->find('tr') as $tr)
            {
                $tr = \phpQuery::pq($tr);
                if ($tr->find('td:eq(0)')->text())
                {
                    $this->arFields["PROPERTY_VALUES"]["OPERATION_TIME_UR"][] = $tr->find('td:eq(0)')->text() . " " . $tr->find('td:eq(1)')->text();
                }
            }
            /**
             * Получение инфо о работе отделения для физ лиц
             */
            foreach ($info->find("div.section.s3")->find('div.text')->find("div") as $div)
            {
                $div = \phpQuery::pq($div);
                $tmp = $div->html();
                $tmp = explode("<br><br>", $tmp);
                $this->arFields["PROPERTY_VALUES"]["PHONES_FIZ"][] = iconv("windows-1251", "UTF-8", str_replace("<br>", "", trim($tmp[0])));
                $this->arFields["PROPERTY_VALUES"]["PHONES_FIZ"][] = iconv("windows-1251", "UTF-8", str_replace("<br>", "", trim($tmp[1])));
            }
            /**
             * Получение инфо о работе отделения для физ лиц
             */
            foreach ($info->find("div.section.s4")->find('div.text')->find("div") as $div)
            {
                $div = \phpQuery::pq($div);
                $tmp = $div->html();
                $tmp = explode("<br><br>", $tmp);
                $this->arFields["PROPERTY_VALUES"]["PHONES_UR"][] = iconv("windows-1251", "UTF-8", str_replace("<br>", "", trim($tmp[0])));
                $this->arFields["PROPERTY_VALUES"]["PHONES_UR"][] = iconv("windows-1251", "UTF-8", str_replace("<br>", "", trim($tmp[1])));
            }

            /**
             * Compose name for element of infoblock
             */
            prent($this->arFields);
            $this->Add();
        }
        /**
         * Check and add element to infoblock Terminals
         */

        return true;
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