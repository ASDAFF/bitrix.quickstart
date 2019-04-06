<?
interface DSocialPosterParams
{
	public function HasParam($propertyID);
}
class DSocialPosterEntityParams implements DSocialPosterParams
{
	private $arFields = array();
	public function __construct(array $arFields)
	{
		$this->arFields = $arFields;
	}	
	public function HasParam($propertyID)
	{
		return is_set($this->arFields, $propertyID);
	}
	public function __get($propertyID)
	{
/*
		if(!$this->HasParam($propertyID))
			throw new InvalidArgumentException("property not found");
*/
		return $this->arFields[$propertyID];
	}
}

interface DSocialPosterIBlockSettings extends Iterator
{
	public function __construct($IBLOCK_ID=false, $ELEMENT_ID=false, $obEntity=false);
}
class DSocialPosterIBlockPropertySettings extends DSocialIterator implements DSocialPosterIBlockSettings
{
    /**
     *
     * @param bool|int $IBLOCK_ID
     * @param bool|int $ELEMENT_ID
     * @param bool|DSocialPosterEntity $obEntity
     * @throws InvalidArgumentException
     *
     */
	public function __construct($IBLOCK_ID=false, $ELEMENT_ID=false, $obEntity=false)
	{
		if(($IBLOCK_ID === false) && intval($ELEMENT_ID))
			throw new InvalidArgumentException("if you whant filter by ELEMNT_ID, you must set IBLOCK_ID");			
		elseif($obEntity !== false && !($obEntity instanceof DSocialPosterEntity))
			throw new InvalidArgumentException("invalid entity type");		
			
		$arProperty = DSocialMediaPosterCIBlockProperty::GetUserTypeDescription();

		$arFilter = array(
				"ACTIVE" => "Y",
				"PROPERTY_TYPE" => $arProperty["PROPERTY_TYPE"],
				"USER_TYPE" => $arProperty["USER_TYPE"],
			);
		if($IBLOCK_ID !== false)
			$arFilter["IBLOCK_ID"] = $IBLOCK_ID;
	
		$rsProperty = CIBlockProperty::GetList(array(), $arFilter);
			
		$obListEntity = DSocialPosterEntityFactory::GetEntityList();		
		while($arProperty = $rsProperty->Fetch())
		{

			if($ELEMENT_ID !== false)
			{
				$rs = CIBlockElement::GetProperty(
							$IBLOCK_ID, 
							$ELEMENT_ID, 
							array(), 
							array(
								"ID" => $arProperty["ID"]
							)
						);
				if( !($ar = $rs->Fetch()) )
					throw new InvalidArgumentException("value not found");

				if (!is_set($arProperty["USER_TYPE_SETTINGS"], "ENTITIES"))
					$arProperty["USER_TYPE_SETTINGS"] = array("ENTITIES" => $arProperty["USER_TYPE_SETTINGS"]);

				foreach ($arProperty["USER_TYPE_SETTINGS"]["ENTITIES"] as $entity => $value) {
					if (!in_array($entity, $ar["VALUE"]))
						unset($arProperty["USER_TYPE_SETTINGS"]["ENTITIES"][$entity]);
				}

			}			

			if(is_array($arProperty["USER_TYPE_SETTINGS"]["ENTITIES"]))
			{
				foreach($arProperty["USER_TYPE_SETTINGS"]["ENTITIES"] as $entity => $arSettings)
					if($ob = $obListEntity->GetByID($entity))
					{
						foreach($arProperty["USER_TYPE_SETTINGS"] as $k => $v) {
							if ($k != "ENTITIES")
								$arSettings[$k] = $v;
						}
					
						if($obEntity === false || $obEntity === $ob)
							$this->add(new DSocialPosterEntityParams($arSettings));						
					}
					
			}
		}		
	}
}
?>
