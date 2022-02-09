<?
namespace Ns\Parser;

require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/phpQuery.php";
/**
*
*/
abstract class ParserCore
{
	const CITIES_IBLOCK_ID = 20;

	abstract public function go();

	protected function findCity($city)
    {
        if (!$city)
        {
            return false;
        }
        else
        {
            $rsCities = \CIBlockElement::GetList(
                false,
                array(
                    "NAME" => $city,
                    "IBLOCK_ID" => self::CITIES_IBLOCK_ID
                    ),
                false,
                array("nTopCount" => 1),
                array("ID")
                );
            if ($searchCity = $rsCities->Fetch())
            {
                return $searchCity["ID"];
            }
            else
            {
                $newCityID = $this->objElement->Add(array(
                    "IBLOCK_ID" => self::CITIES_IBLOCK_ID,
                    "ACTIVE" => "Y",
                    "NAME" => $city,
                    ));
                if ($this->objElement->LAST_ERROR)
                {
                    throw new Exception("An error was occured..." . __CLASS__ . ". In line: " . __LINE__ . "Problem: " . $this->objElement->LAST_ERROR, 1);
                }
                else
                {
                    return $newCityID;
                }
            }
        }
        throw new \Exception("An error was occured..." . __CLASS__ . ". In line: " . __LINE__, 1);
    }
}


?>