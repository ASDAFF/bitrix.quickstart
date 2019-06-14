<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["y_realty"] = array(
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
    "REG" => "http://realty.yandex.ru/",
    "HELP" => "http://yandex.ru/support/realty/partners/requirements.xml",
    "FIELDS" => array(
        array(
            "CODE" => "ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_ID" ),
            "VALUE" => "ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_TYPE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "PROPERTY_TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PROPERTY_TYPE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "CATEGORY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_CATEGORY" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "COMMERCIAL_TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_COMMERCIAL_TYPE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "COMMERCIAL_BUILDING_TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_COMMERCIAL_BUILDING_TYPE" ),
        ),
        array(
            "CODE" => "PURPOSE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PURPOSE" ),
        ),
        array(
            "CODE" => "PURPOSE_WAREHOUSE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PURPOSE_WAREHOUSE" ),
        ),
        array(
            "CODE" => "LOT_NUMBER",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOT_NUMBER" ),
        ),
        array(
            "CODE" => "URL",
            "NAME" => "URL ".GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_URL" ),
            "VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "CREATION_DATE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_CREATION_DATE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "LAST_UPDATE_DATE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LAST_UPDATE_DATE" ),
        ),
        array(
            "CODE" => "EXPIRE_DATE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_EXPIRE_DATE" ),
        ),
        array(
            "CODE" => "PAYED_ADV",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PAYED_ADV" ),
        ),
        array(
            "CODE" => "MANUALLY_ADDED",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_MANUALLY_ADDED" ),
        ),
        array(
            "CODE" => "LOCATION_COUNTRY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_COUNTRY" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "LOCATION_REGION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_REGION" ),
        ),
        array(
            "CODE" => "LOCATION_DISTRICT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_DISTRICT" ),
        ),
        array(
            "CODE" => "LOCATION_LOCALITY_NAME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_LOCALITY_NAME" ),
        ),
        array(
            "CODE" => "LOCATION_SUB_LOCALITY_NAME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_SUB_LOCALITY_NAME" ),
        ),
        array(
            "CODE" => "LOCATION_ADDRESS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_MANUALLY_ADDRESS" ),
        ),
        array(
            "CODE" => "LOCATION_DIRECTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_MANUALLY_DIRECTION" ),
        ),
        array(
            "CODE" => "LOCATION_DISTANCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_DISTANCE" ),
        ),
        array(
            "CODE" => "LATITUDE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LATITUDE" ),
        ),
        array(
            "CODE" => "LONGITUDE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LONGITUDE" ),
        ),
        array(
            "CODE" => "METRO_NAME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_METRO_NAME" ),
        ),
        array(
            "CODE" => "METRO_TIME_ON_TRANSPORT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_METRO_TIME_ON_TRANSPORT" ),
        ),
        array(
            "CODE" => "METRO_TIME_ON_FOOT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_METRO_TIME_ON_FOOT" ),
        ),
        array(
            "CODE" => "LOCATION_RAILWAY_STATION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOCATION_RAILWAY_STATION" ),
        ),
        array(
            "CODE" => "SALES_AGENT_NAME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_NAME" ),
        ),
        array(
            "CODE" => "SALES_AGENT_PHONE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_PHONE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "SALES_AGENT_CATEGORY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_CATEGORY" ),
        ),
        array(
            "CODE" => "SALES_AGENT_ORGANIZATION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_ORGANIZATION" ),
        ),
        array(
            "CODE" => "SALES_AGENT_AGENCY_ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_AGENCY_ID" ),
        ),
        array(
            "CODE" => "SALES_AGENT_URL",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_URL" ),
        ),
        array(
            "CODE" => "SALES_AGENT_EMAIL",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_EMAIL" ),
        ),
        array(
            "CODE" => "SALES_AGENT_PHOTO",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_PHOTO" ),
        ),
        array(
            "CODE" => "SALES_AGENT_PARTNER",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SALES_AGENT_PARTNER" ),
        ),
        array(
            "CODE" => "PRICE_VALUE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PRICE_VALUE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "PRICE_CURRENCY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PRICE_CURRENCY" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
        ),
        array(
            "CODE" => "PRICE_PERIOD",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PRICE_PERIOD" ),
        ),
        array(
            "CODE" => "PRICE_UNIT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PRICE_UNIT" ),
        ),
        array(
            "CODE" => "PRICE_TAXATION_FORM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PRICE_TAXATION_FORM" ),
        ),
        array(
            "CODE" => "NOT_FOR_AGENTS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_NOT_FOR_AGENTS" ),
        ),
        array(
            "CODE" => "HAGGLE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_HAGGLE" ),
        ),
        array(
            "CODE" => "MORTGAGE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_MORTGAGE" ),
        ),
        array(
            "CODE" => "PREPAYMENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PREPAYMENT" ),
        ),
        array(
            "CODE" => "RENT_PLEDGE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_RENT_PLEDGE" ),
        ),
        array(
            "CODE" => "UTILITIES_INCLUDED",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_UTILITIES_INCLUDED" ),
        ),
        array(
            "CODE" => "COMMISSION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_COMMISSION" ),
        ),
        array(
            "CODE" => "SECURITY_PAYMENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_SECURITY_PAYMENT" ),
        ),
        array(
            "CODE" => "CLEANING_INCLUDED",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_CLEANING_INCLUDED" ),
        ),
        array(
            "CODE" => "ELECTRICITY_INCLUDED",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_ELECTRICITY_INCLUDED" ),
        ),
        array(
            "CODE" => "AGENT_FEE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_AGENT_FEE" ),
        ),
        array(
            "CODE" => "DEAL_STATUS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_DEAL_STATUS" ),
        ),
        array(
            "CODE" => "WITH_PETS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_WITH_PETS" ),
        ),
        array(
            "CODE" => "WITH_CHILDREN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_WITH_CHILDREN" ),
        ),
        array(
            "CODE" => "OBJECT_AREA",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_AREA" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "OBJECT_IMAGE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_IMAGE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "OBJECT_RENOVATION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_RENOVATION" ),
        ),
        array(
            "CODE" => "OBJECT_QUALITY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_QUALITY" ),
        ),
        array(
            "CODE" => "OBJECT_DESCRIPTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_DESCRIPTION" ),
        ),
        array(
            "CODE" => "OBJECT_LIVING_SPACE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_LIVING_SPACE" ),
        ),
        array(
            "CODE" => "OBJECT_ROOM_SPACE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_ROOM_SPACE" ),
        ),
        array(
            "CODE" => "OBJECT_KITCHEN_SPACE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_KITCHEN_SPACE" ),
        ),
        array(
            "CODE" => "OBJECT_UNIT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OBJECT_UNIT" ),
        ),
        array(
            "CODE" => "LOT_AREA",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOT_AREA" ),
        ),
        array(
            "CODE" => "LOT_TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LOT_TYPE" ),
        ),
        array(
            "CODE" => "LEAVING_NEW_FLAT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_NEW_FLAT" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "LEAVING_ROOMS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_ROOMS" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "LEAVING_ROOMS_OFFERED",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_ROOMS_OFFERED" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "LEAVING_STUDIO",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_STUDIO" ),
        ),
        array(
            "CODE" => "LEAVING_APARTMENTS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_APARTMENTS" ),
        ),
        array(
            "CODE" => "LEAVING_OPEN_PLAN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_OPEN_PLAN" ),
        ),
        array(
            "CODE" => "LEAVING_ROOMS_TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_ROOMS_TYPE" ),
        ),
        array(
            "CODE" => "LEAVING_PHONE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_PHONE" ),
        ),
        array(
            "CODE" => "LEAVING_INTERNET",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_INTERNET" ),
        ),
        array(
            "CODE" => "LEAVING_ROOM_FURNITURE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_ROOM_FURNITURE" ),
        ),
        array(
            "CODE" => "LEAVING_KITCHEN_FURNITURE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_KITCHEN_FURNITURE" ),
        ),
        array(
            "CODE" => "LEAVING_TELEVISION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_TELEVISION" ),
        ),
        array(
            "CODE" => "LEAVING_WASHING_MASHINE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_WASHING_MASHINE" ),
        ),
        array(
            "CODE" => "LEAVING_DISHWASHER",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_DISHWASHER" ),
        ),
        array(
            "CODE" => "LEAVING_REFRIGERATOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_REFRIGERATOR" ),
        ),
        array(
            "CODE" => "LEAVING_BUILT_IN_TECH",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_BUILT_IN_TECH" ),
        ),
        array(
            "CODE" => "LEAVING_BALCONY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_BALCONY" ),
        ),
        array(
            "CODE" => "LEAVING_BATHROOM_UNIT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_BATHROOM_UNIT" ),
        ),
        array(
            "CODE" => "LEAVING_FLOOR_COVERING",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_FLOOR_COVERING" ),
        ),
        array(
            "CODE" => "LEAVING_WINDOW_VIEW",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_WINDOW_VIEW" ),
        ),
        array(
            "CODE" => "LEAVING_FLOOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_FLOOR" ),
        ),
        array(
            "CODE" => "LEAVING_ENTRANCE_TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_ENTRANCE_TYPE" ),
        ),
        array(
            "CODE" => "LEAVING_PHONE_LINES",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_PHONE_LINES" ),
        ),
        array(
            "CODE" => "LEAVING_ADDING_PHONE_ON_REQUEST",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_ADDING_PHONE_ON_REQUEST" ),
        ),
        array(
            "CODE" => "LEAVING_SELF_SELECTION_TELECOM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_SELF_SELECTION_TELECOM" ),
        ),
        array(
            "CODE" => "LEAVING_AIR_CONDITIONER",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_AIR_CONDITIONER" ),
        ),
        array(
            "CODE" => "LEAVING_VENTILATION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_VENTILATION" ),
        ),
        array(
            "CODE" => "LEAVING_FIRE_ALARM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_FIRE_ALARM" ),
        ),
        array(
            "CODE" => "LEAVING_ELECTRIC_CAPACITY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_LEAVING_ELECTRIC_CAPACITY" ),
        ),
        array(
            "CODE" => "BUILDING_FLOORS_TOTAL",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_FLOORS_TOTAL" ),
        ),
        array(
            "CODE" => "BUILDING_NAME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_NAME" ),
        ),
        array(
            "CODE" => "BUILDING_YANDEX_BUILDING_ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_YANDEX_BUILDING_ID" ),
        ),
        array(
            "CODE" => "BUILDING_OFFICE_CLASS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_OFFICE_CLASS" ),
        ),
        array(
            "CODE" => "BUILDING_GUARDED_BUILDING",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_GUARDED_BUILDING" ),
        ),
        array(
            "CODE" => "BUILDING_ACCESS_CONTROL_SYSTEM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_ACCESS_CONTROL_SYSTEM" ),
        ),
        array(
            "CODE" => "BUILDING_TWENTY_FOUR_SEVEN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_TWENTY_FOUR_SEVEN" ),
        ),
        array(
            "CODE" => "BUILDING_PARKING_PLACES",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_PARKING_PLACES" ),
        ),
        array(
            "CODE" => "BUILDING_PARKING_PLACE_PRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_PARKING_PLACE_PRICE" ),
        ),
        array(
            "CODE" => "BUILDING_PARKING_GUEST",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_PARKING_GUEST" ),
        ),
        array(
            "CODE" => "BUILDING_PARKING_GUEST_PLACES",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_PARKING_GUEST_PLACES" ),
        ),
        array(
            "CODE" => "BUILDING_FLAT_ALARM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_FLAT_ALARM" ),
        ),
        array(
            "CODE" => "BUILDING_SECURITY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_SECURITY" ),
        ),
        array(
            "CODE" => "BUILDING_EATING_FACILITIES",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_EATING_FACILITIES" ),
        ),
        array(
            "CODE" => "STOCK_RESPONSIBLE_STORAGE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_RESPONSIBLE_STORAGE" ),
        ),
        array(
            "CODE" => "STOCK_PALLET_PRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_PALLET_PRICE" ),
        ),
        array(
            "CODE" => "STOCK_FREIGHT_ELEVATOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_FREIGHT_ELEVATOR" ),
        ),
        array(
            "CODE" => "STOCK_TRUCK_ENTRANCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_TRUCK_ENTRANCE" ),
        ),
        array(
            "CODE" => "STOCK_RAMP",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_RAMP" ),
        ),
        array(
            "CODE" => "STOCK_RAILWAY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_RAILWAY" ),
        ),
        array(
            "CODE" => "STOCK_OFFICE_WAREHOUSE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_OFFICE_WAREHOUSE" ),
        ),
        array(
            "CODE" => "STOCK_OPEN_AREA",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_OPEN_AREA" ),
        ),
        array(
            "CODE" => "STOCK_SERVICE_THREE_PL",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_SERVICE_THREE_PL" ),
        ),
        array(
            "CODE" => "STOCK_TEMPERATURE_COMMENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_STOCK_TEMPERATURE_COMMENT" ),
        ),
        array(
            "CODE" => "BUILDING_TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_TYPE" ),
        ),
        array(
            "CODE" => "BUILDING_SERIES",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_SERIES" ),
        ),
        array(
            "CODE" => "BUILDING_PHASE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_PHASE" ),
        ),
        array(
            "CODE" => "BUILDING_SECTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_SECTION" ),
        ),
        array(
            "CODE" => "BUILDING_STATE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_STATE" ),
        ),
        array(
            "CODE" => "BUILDING_BUILT_YEAR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_BUILT_YEAR" ),
        ),
        array(
            "CODE" => "BUILDING_READY_QUARTER",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_READY_QUARTER" ),
        ),
        array(
            "CODE" => "BUILDING_LIFT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_LIFT" ),
        ),
        array(
            "CODE" => "BUILDING_RUBBISH_CHUTE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_RUBBISH_CHUTE" ),
        ),
        array(
            "CODE" => "BUILDING_IS_ELITE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_IS_ELITE" ),
        ),
        array(
            "CODE" => "BUILDING_PARKING",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_PARKING" ),
        ),
        array(
            "CODE" => "BUILDING_ALARM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_ALARM" ),
        ),
        array(
            "CODE" => "BUILDING_CEILING_HEIGHT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_CEILING_HEIGHT" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_PMG",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_BUILDING_OUT_OF_TOWN_PMG" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_TOILET",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_TOILET" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_SHOWER",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_SHOWER" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_KITCHEN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_KITCHEN" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_POOL",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_POOL" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_BILLIARD",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_BILLIARD" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_SAUNA",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_SAUNA" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_HEATING_SUPPLY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_HEATING_SUPPLY" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_WATER_SUPPLY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_WATER_SUPPLY" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_SEWERAGE_SUPPLY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_SEWERAGE_SUPPLY" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_ELECTRICITY_SUPPLY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_ELECTRICITY_SUPPLY" ),
        ),
        array(
            "CODE" => "OUT_OF_TOWN_GAS_SUPPLY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_OUT_OF_TOWN_GAS_SUPPLY" ),
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
    ),
    "FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<realty-feed xmlns="http://webmaster.yandex.ru/schemas/feed/realty/2010-06">
<generation-date>#DATE#</generation-date>
#ITEMS#
</realty-feed>',
    "DATEFORMAT" => "Y-m-d_h:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["y_realty"]["FIELDS"][38] = array(
        "CODE" => "PRICE_VALUE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_REALTY_FIELD_PRICE_VALUE" ),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["y_realty"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_REALTY_PORTAL_REQUIREMENTS" );
$profileTypes["y_realty"]["PORTAL_VALIDATOR"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_REALTY_PORTAL_VALIDATOR" );
$profileTypes["y_realty"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_REALTY_EXAMPLE" );

$profileTypes["y_realty"]["ITEMS_FORMAT"] = "
<offer internal-id=\"#ID#\">
    <type>#TYPE#</type>
    <property-type>#PROPERTY_TYPE#</property-type>
    <category>#CATEGORY#</category>
    <commercial-type>#COMMERCIAL_TYPE#</commercial-type>
    <commercial-building-type>#COMMERCIAL_BUILDING_TYPE#</commercial-building-type>
    <purpose>#PURPOSE#</purpose>
    <purpose-warehouse>#PURPOSE_WAREHOUSE#</purpose-warehouse>
    <lot-number>#LOT_NUMBER#</lot-number>
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <payed-adv>#PAYED_ADV#</payed-adv>
    <manually-added>#MANUALLY_ADDED#</manually-added>
    <creation-date>#CREATION_DATE#</creation-date>
    <expire-date>#EXPIRE_DATE#</expire-date>
    <last-update-date>#LAST_UPDATE_DATE#</last-update-date>
    <location>
        <country>#LOCATION_COUNTRY#</country>
        <region>#LOCATION_REGION#</region>
        <district>#LOCATION_DISTRICT#</district>
        <locality-name>#LOCATION_LOCALITY_NAME#</locality-name>
        <sub-locality-name>#LOCATION_SUB_LOCALITY_NAME#</sub-locality-name>
        <railway-station>#LOCATION_RAILWAY_STATION#</railway-station>
        <address>#LOCATION_ADDRESS#</address>
        <direction>#LOCATION_DIRECTION#</direction>
        <distance>#LOCATION_DISTANCE#</distance>
        <latitude>#LATITUDE#</latitude>
        <longitude>#LONGITUDE#</longitude>
        <metro>
            <name>#METRO_NAME#</name>
            <time-on-foot>#METRO_TIME_ON_FOOT#</time-on-foot>
            <time-on-transport>#METRO_TIME_ON_TRANSPORT#</time-on-transport>
        </metro>
    </location>
    <sales-agent>
        <name>#SALES_AGENT_NAME#</name>
        <phone>#SALES_AGENT_PHONE#</phone>
        <photo>#SALES_AGENT_PHOTO#</photo>
        <category>#SALES_AGENT_CATEGORY#</category>
        <organization>#SALES_AGENT_ORGANIZATION#</organization>
        <url>#SALES_AGENT_URL#</url>
        <email>#SALES_AGENT_EMAIL#</email>
        <agency-id>#SALES_AGENT_AGENCY_ID#</agency-id>
        <partner>#SALES_AGENT_PARTNER#</partner>
    </sales-agent>
    <new-flat>#LEAVING_NEW_FLAT#</new-flat>
    <price>
        <value>#PRICE_VALUE#</value>
        <currency>#PRICE_CURRENCY#</currency>
        <period>#PRICE_PERIOD#</period>
        <unit>#PRICE_UNIT#</unit>
        <taxation-form>#PRICE_TAXATION_FORM#</taxation-form>
    </price>
    <haggle>#HAGGLE#</haggle>
    <mortgage>#MORTGAGE#</mortgage>
    <prepayment>#PREPAYMENT#</prepayment>
    <rent-pledge>#RENT_PLEDGE#</rent-pledge>
    <agent-fee>#AGENT_FEE#</agent-fee>
    <deal-status>#DEAL_STATUS#</deal-status>
    <not-for-agents>#NOT_FOR_AGENTS#</not-for-agents>
    <with-pets>#WITH_PETS#</with-pets>
    <with-children>#WITH_CHILDREN#</with-children>
    <image>#OBJECT_IMAGE#</image>
    <description>#OBJECT_DESCRIPTION#</description>
    <quality>#OBJECT_QUALITY#</quality>
    <renovation>#OBJECT_RENOVATION#</renovation>
    <commission>#COMMISSION#</commission>
    <security-payment>#SECURITY_PAYMENT#</security-payment>
    <cleaning-included>#CLEANING_INCLUDED#</cleaning-included>
    <utilities-included>#UTILITIES_INCLUDED#</utilities-included>
    <electricity-included>#ELECTRICITY_INCLUDED#</electricity-included>
    <area>
        <value>#OBJECT_AREA#</value>
        <unit>#OBJECT_UNIT#</unit>
    </area>
    <lot-area>
        <value>#LOT_AREA#</value>
        <unit>#OBJECT_UNIT#</unit>
    </lot-area>
    <living-space>
        <value>#OBJECT_LIVING_SPACE#</value>
        <unit>#OBJECT_UNIT#</unit>
    </living-space>
    <room-space>
        <value>#OBJECT_ROOM_SPACE#</value>
        <unit>#OBJECT_UNIT#</unit>
    </room-space>
    <kitchen-space>
        <value>#OBJECT_KITCHEN_SPACE#</value>
        <unit>#OBJECT_UNIT#</unit>
    </kitchen-space>
    <lot-type>#LOT_TYPE#</lot-type>
    <rooms>#LEAVING_ROOMS#</rooms>
    <rooms-offered>#LEAVING_ROOMS_OFFERED#</rooms-offered>
    <studio>#LEAVING_STUDIO#</studio>
    <apartments>#LEAVING_APARTMENTS#</apartments>
    <rooms-type>#LEAVING_ROOMS_TYPE#</rooms-type>
    <phone>#LEAVING_PHONE#</phone>
    <internet>#LEAVING_INTERNET#</internet>
    <room-furniture>#LEAVING_ROOM_FURNITURE#</room-furniture>
    <kitchen-furniture>#LEAVING_KITCHEN_FURNITURE#</kitchen-furniture>
    <television>#LEAVING_TELEVISION#</television>
    <washing-machine>#LEAVING_WASHING_MASHINE#</washing-machine>
    <dishwasher>#LEAVING_DISHWASHER#</dishwasher>
    <refrigerator>#LEAVING_REFRIGERATOR#</refrigerator>
    <built-in-tech>#LEAVING_BUILT_IN_TECH#</built-in-tech>
    <balcony>#LEAVING_BALCONY#</balcony>
    <open-plan>#LEAVING_OPEN_PLAN#</open-plan>
    <bathroom-unit>#LEAVING_BATHROOM_UNIT#</bathroom-unit>
    <window-view>#LEAVING_WINDOW_VIEW#</window-view>
    <floor-covering>#LEAVING_FLOOR_COVERING#</floor-covering>
    <floor>#LEAVING_FLOOR#</floor>
    <entrance-type>#LEAVING_ENTRANCE_TYPE#</entrance-type>
    <phone-lines>#LEAVING_PHONE_LINES#</phone-lines>
    <adding-phone-on-request>#LEAVING_ADDING_PHONE_ON_REQUEST#</adding-phone-on-request>
    <self-selection-telecom>#LEAVING_SELF_SELECTION_TELECOM#</self-selection-telecom>
    <air-conditioner>#LEAVING_AIR_CONDITIONER#</air-conditioner>
    <ventilation>#LEAVING_VENTILATION#</ventilation>
    <fire-alarm>#LEAVING_FIRE_ALARM#</fire-alarm>
    <electric-capacity>#LEAVING_ELECTRIC_CAPACITY#</electric-capacity>
    <floors-total>#BUILDING_FLOORS_TOTAL#</floors-total>
    <building-name>#BUILDING_NAME#</building-name>
    <building-type>#BUILDING_TYPE#</building-type>
    <yandex-building-id>#BUILDING_YANDEX_BUILDING_ID#</yandex-building-id>
    <office-class>#BUILDING_OFFICE_CLASS#</office-class>
    <guarded-building>#BUILDING_GUARDED_BUILDING#</guarded-building>
    <access-control-system>#BUILDING_ACCESS_CONTROL_SYSTEM#</access-control-system>
    <twenty-four-seven>#BUILDING_TWENTY_FOUR_SEVEN#</twenty-four-seven>
    <parking-places>#BUILDING_PARKING_PLACES#</parking-places>
    <parking-place-price>#BUILDING_PARKING_PLACE_PRICE#</parking-place-price>
    <parking-guest>#BUILDING_PARKING_GUEST#</parking-guest>
    <parking-guest-places>#BUILDING_PARKING_GUEST_PLACES#</parking-guest-places>
    <flat-alarm>#BUILDING_FLAT_ALARM#</flat-alarm>
    <security>#BUILDING_SECURITY#</security>
    <eating-facilities>#BUILDING_EATING_FACILITIES#</eating-facilities>
    <responsible-storage>#STOCK_RESPONSIBLE_STORAGE#</responsible-storage>
    <pallet-price>#STOCK_PALLET_PRICE#</pallet-price>
    <freight-elevator>#STOCK_FREIGHT_ELEVATOR#</freight-elevator>
    <truck-entrance>#STOCK_TRUCK_ENTRANCE#</truck-entrance>
    <ramp>#STOCK_RAMP#</ramp>
    <railway>#STOCK_RAILWAY#</railway>
    <office-warehouse>#STOCK_OFFICE_WAREHOUSE#</office-warehouse>
    <open-area>#STOCK_OPEN_AREA#</open-area>
    <service-three-pl>#STOCK_SERVICE_THREE_PL#</service-three-pl>
    <temperature-comment>#STOCK_TEMPERATURE_COMMENT#</temperature-comment>
    <building-series>#BUILDING_SERIES#</building-series>
    <building-phase>#BUILDING_PHASE#</building-phase>
    <building-section>#BUILDING_SECTION#</building-section>
    <building-state>#BUILDING_STATE#</building-state>
    <built-year>#BUILDING_BUILT_YEAR#</built-year>
    <ready-quarter>#BUILDING_READY_QUARTER#</ready-quarter>
    <lift>#BUILDING_LIFT#</lift>
    <rubbish-chute>#BUILDING_RUBBISH_CHUTE#</rubbish-chute>
    <ceiling-height>#BUILDING_CEILING_HEIGHT#</ceiling-height>
    <pmg>#OUT_OF_TOWN_PMG#</pmg>
    <is-elite>#BUILDING_IS_ELITE#</is-elite>
    <toilet>#OUT_OF_TOWN_TOILET#</toilet>
    <shower>#OUT_OF_TOWN_SHOWER#</shower>
    <kitchen>#OUT_OF_TOWN_KITCHEN#</kitchen>
    <heating-supply>#OUT_OF_TOWN_HEATING_SUPPLY#</heating-supply>
    <water-supply>#OUT_OF_TOWN_WATER_SUPPLY#</water-supply>
    <electricity-supply>#OUT_OF_TOWN_ELECTRICITY_SUPPLY#</electricity-supply>
    <sewerage-supply>#OUT_OF_TOWN_SEWERAGE_SUPPLY#</sewerage-supply>
    <gas-supply>#OUT_OF_TOWN_GAS_SUPPLY#</gas-supply>
    <parking>#BUILDING_PARKING#</parking>
    <alarm>#BUILDING_ALARM#</alarm>
    <pool>#OUT_OF_TOWN_POOL#</pool>
    <billiard>#OUT_OF_TOWN_BILLIARD#</billiard>
    <sauna>#OUT_OF_TOWN_SAUNA#</sauna>
</offer>
";
    
$profileTypes["y_realty"]["LOCATION"] = array(
    "yandex" => array(
        "name" => GetMessage( "ACRIT_EXPORTPRO_ANDEKS" ),
        "sub" => array(
            "market" => array(
                "name" => GetMessage( "ACRIT_EXPORTPRO_VEBMASTER" ),
                "sub" => "",
            )
        )
    ),
);