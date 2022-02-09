<?
namespace Ns\Parser;
set_time_limit (300);
/**
*
*/
class AlfaTerminals extends ParserCore
{
    const ALFATERMINALS_IBLOCK_ID = 10;
    const ALFATERMINALS_SECTION_ID = 327;
    const SBSTERMINALS_SECTION_ID = 328;


	private $dom;
	private $arFields;
    private $objElement;
    private $checker;
    private $type;

	function __construct($type = false)
	{
        $this->type = $type;
        if ($type == "all")
        {
    		$html = file_get_contents("http://www.alfabank.by/bank/contacts/offices/6.html?page=2");
        }
        elseif ($type == "alfa")
        {
            $html = file_get_contents("http://www.alfabank.by/bank/contacts/offices/12.html");
        }
		$this->dom = \phpQuery::newDocumentHTML($html);
		/**
		 * Устанавливаем по дефолту инфоблок новостей для альфы
		 */
		$this->arFields["IBLOCK_ID"] = self::ALFATERMINALS_IBLOCK_ID;
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
        $index = 0;
        foreach ($table->find("tr") as $tr)
        {
            var_dump($index);
            if (++$index == 1) {
                continue;
            }
            $this->arFields = array();
            $this->arFields["IBLOCK_ID"] = self::ALFATERMINALS_IBLOCK_ID;
            $this->arFields["IBLOCK_SECTION_ID"] = ($this->type == "alfa") ? self::ALFATERMINALS_SECTION_ID : self::SBSTERMINALS_SECTION_ID;
            /**
             * Получение информации о банкомете
             */
            $tr = \phpQuery::pq($tr);
            /**
             * 1. Name
             */
            $this->arFields["NAME"] = $tr->find('td:eq(0)')->find("a")->text();
            $infoLink = $tr->find('td:eq(0)')->find("a")->attr("href");
            /**
             * 2. State
             */
            $this->arFields["PROPERTY_VALUES"]["STATE"] = $tr->find('td:eq(1)')->text();
            /**
             * 3. City as link
             */
            $this->arFields["PROPERTY_VALUES"]["BIND_CITY"] = $this->findCity($tr->find('td:eq(2)')->text());
            /**
             * 4. City text
             */
            $this->arFields["PROPERTY_VALUES"]["CITY"] = $tr->find('td:eq(2)')->text();
            /**
             * 5. Address
             */
            $this->arFields["PROPERTY_VALUES"]["ADDRESS"] = $tr->find('td:eq(3)')->text();
             /**
             * 5. Operating mode
             */
            $this->arFields["PROPERTY_VALUES"]["OPERATION_TIME"] = $tr->find('td:eq(4)')->text();
            /**
             * Find nessesary element of link
             */
            try
            {
                $this->arFields["PROPERTY_VALUES"]["WORK_TYPES"] = $this->findWorkType($this->arFields["PROPERTY_VALUES"]["OPERATION_TIME"]);
            }
            catch (\Exception $e)
            {
                prentExpection($e->getMessage());
            }

            $html = file_get_contents("http://www.alfabank.by" . $infoLink);
            $fullInfo = \phpQuery::newDocumentHTML($html);

            $info = \phpQuery::pq($fullInfo->find("div.content"));

            /**
             * Compose currency string
             */
            $info->find("div.section.s1")->find('table')->find('tr');
            foreach ($info->find("div.section.s4")->find('ul')->find('li') as $li) {
                $li = \phpQuery::pq($li);
                $strCurrency .= $li->text() . " ";
            }
            $this->arFields["PROPERTY_VALUES"]["CURRENCY"] = trim($strCurrency); unset($strCurrency);
            /**
             * Lat & len of map
             */
            // $coordinates = explode(",", $info->find("div.section:eq(5)")->find("div.ya_map_data")->text());
            $coordinates = $info->find("div.section:eq(5)")->find("div.ya_map_data")->text();
            $this->arFields["PROPERTY_VALUES"]["POINT"] = $coordinates;

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
                    prentExpection($this->objElement->LAST_ERROR);
                }
            }
    }

    private function findWorkType($workType)
    {
        if (!$workType)
        {
            return false;
        }
        else
        {
            $workType = trim($workType);
            if ($workType == "В режиме работы организации")
            {
                return 2903;
            }
            elseif ($workType == "24 часа")
            {
                return 2904;
            }
        }
        throw new \Exception("An error was occured..." . __CLASS__ . ". In line: " . __LINE__, 1);
    }

}

?>