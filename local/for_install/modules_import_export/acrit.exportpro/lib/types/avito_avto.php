<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['avito_avto'] = array(
	"CODE" => 'avito_avto',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "Id",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
        array(
            "CODE" => "DateBegin",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_DATEBEGIN"),
        ),
        array(
            "CODE" => "DateEnd",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_DATEEND"),
        ),
		array(
            "CODE" => "Description",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_DESCRIPTION"),
        ),
        array(
            "CODE" => "AdStatus",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ADSTATUS"),
        ),
        array(
            "CODE" => "EMail",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_EMAIL"),
        ),
        array(
            "CODE" => "AllowEmail",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ALLOWEMAIL"),
        ),
        array(
            "CODE" => "CompanyName",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_COMPANYNAME"),
        ),
        array(
            "CODE" => "ManagerName",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_MANAGERNAME"),
        ),
        array(
            "CODE" => "ContactPhone",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_CONTACTPHONE"),
        ),
        array(
            "CODE" => "Region",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_REGION"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "City",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_CITY"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "District",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_DISTRICT"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "Subway",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_SUBWAY"),
        ),
        array(
            "CODE" => "Category",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_CATEGORY"),
            "REQUIRED" => 'Y',
        ),
        array(
			"CODE" => "CarType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_CARTYPE"),
		),
		array(
			"CODE" => "Price",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
            "CODE" => "Kilometrage",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_KILOMETRAGE"),
        ),
        array(
            "CODE" => "Accident",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ACCIDENT"),
        ),
        array(
            "CODE" => "Make",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_MAKE"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "Model",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_MODEL"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "Year",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_YEAR"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "VIN",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_VIN"),
        ),
        array(
            "CODE" => "CertificationNumber",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_CERTIFICATIONNUMBER"),
        ),
        array(
            "CODE" => "AllowAvtokodReportLink",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ALLOWAVTOKODREPORTLINK"),
        ),
        array(
            "CODE" => "BodyType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_BODYTYPE"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "Doors",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_DOORS"),
        ),
        array(
            "CODE" => "Color",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_COLOR"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "FuelType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_FUELTYPE"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "EngineSize",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ENGINESIZE"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "Power",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_POWER"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "Transmission",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_TRANSMISSION"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "DriveType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_DRIVETYPE"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "WheelType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_WHEELTYPE"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "PowerSteering",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_POWERSTEERING"),
        ),
        array(
            "CODE" => "ClimateControl",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_CLIMATECONTROL"),
        ),
        array(
            "CODE" => "ClimateControlOptionsOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_CLIMATECONTROLOPTIONSOPTION"),
        ),
        array(
            "CODE" => "Interior",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_INTERIOR"),
        ),
        array(
            "CODE" => "InteriorOptionsOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_INTERIOROPTIONSOPTION"),
        ),
        array(
            "CODE" => "HeatingOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_HEATINGOPTION"),
        ),
        array(
            "CODE" => "PowerWindows",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_POWERWINDOWS"),
        ),
        array(
            "CODE" => "ElectricDriveOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ELECTRICDRIVEOPTION"),
        ),
        array(
            "CODE" => "MemorySettingsOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_MEMORYSETTINGSOPTION"),
        ),
        array(
            "CODE" => "DrivingAssistanceOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_DRIVINGASSISTANCEOPTION"),
        ),
        array(
            "CODE" => "AntitheftSystemOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ANTITHEFTSYSTEMOPTION"),
        ),
        array(
            "CODE" => "AirbagsOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_AIRBAGSOPTION"),
        ),
        array(
            "CODE" => "ActiveSafetyOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_ACTIVESAFETYOPTION"),
        ),
        array(
            "CODE" => "MultimediaOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_MULTIMEDIAOPTION"),
        ),
        array(
            "CODE" => "AudioSystem",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_AUDIOSYSTEM"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "AudioSystemOptionsOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_AUDIOSYSTEMOPTIONSOPTION"),
        ),
        array(
            "CODE" => "Lights",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_LIGHTS"),
        ),
        array(
            "CODE" => "LightsOptionsOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_LIGHTSOPTIONSOPTION"),
        ),
        array(
            "CODE" => "Wheels",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_WHEELS"),
        ),
        array(
            "CODE" => "WheelsOptionsOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_WHEELSOPTIONSOPTION"),
        ),
        array(
            "CODE" => "Owners",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_OWNERS"),
        ),
        array(
            "CODE" => "MaintenanceOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_MAINTENANCEOPTION"),
        ),
        array(
            "CODE" => "Image",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_IMAGE"),
        ),
        array(
            "CODE" => "VideoURL",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_VIDEOURL"),
        ),
	),
	"FORMAT" => '<?xml version="1.0"?>
<Ads target="Avito.ru" formatVersion="1">
    #ITEMS#
</Ads>',
    
	"DATEFORMAT" => "Y-m-d",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes['avito_avto']["FIELDS"][16] = array(
        "CODE" => "Price",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_AVTO_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['avito_avto']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_AVTO_PORTAL_REQUIREMENTS' );
$profileTypes['avito_avto']['PORTAL_VALIDATOR'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_AVTO_PORTAL_VALIDATOR' );
$profileTypes['avito_avto']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_AVITO_AVTO_EXAMPLE');

$profileTypes['avito_avto']['CURRENCIES'] = "";

$profileTypes['avito_avto']['SECTIONS'] = "";

$profileTypes['avito_avto']['ITEMS_FORMAT'] = "
<Ad>
    <Id>#Id#</Id>
    <DateBegin>#DateBegin#</DateBegin>
    <DateEnd>#DateEnd#</DateEnd>
    <Description>#Description#</Description>
    <AdStatus>#AdStatus#</AdStatus>
    <EMail>#EMail#</EMail>
    <AllowEmail>#AllowEmail#</AllowEmail>
    <CompanyName>#CompanyName#</CompanyName>
    <ManagerName>#ManagerName#</ManagerName>
    <ContactPhone>#ContactPhone#</ContactPhone>
    <Region>#Region#</Region>
    <City>#City#</City>
    <District>#District#</District>
    <Subway>#Subway#</Subway>
    <Category>#Category#</Category>
    <CarType>#CarType#</CarType>
    <Price>#Price#</Price>
    <Kilometrage>#Kilometrage#</Kilometrage>
    <Accident>#Accident#</Accident>
    <Make>#Make#</Make>
    <Model>#Model#</Model>
    <Year>#Year#</Year>
    <VIN>#VIN#</VIN>
    <CertificationNumber>#CertificationNumber#</CertificationNumber>
    <AllowAvtokodReportLink>#AllowAvtokodReportLink#</AllowAvtokodReportLink>
    <BodyType>#BodyType#</BodyType>
    <Doors>#Doors#</Doors>
    <Color>#Color#</Color>
    <FuelType>#FuelType#</FuelType>
    <EngineSize>#EngineSize#</EngineSize>
    <Power>#Power#</Power>
    <Transmission>#Transmission#</Transmission>
    <DriveType>#DriveType#</DriveType>
    <WheelType>#WheelType#</WheelType>
    <PowerSteering>#PowerSteering#</PowerSteering>
    <ClimateControl>#ClimateControl#</ClimateControl>
    <ClimateControlOptions>
        <Option>#ClimateControlOptionsOption#</Option>
    </ClimateControlOptions>
    <Interior>#Interior#</Interior>
    <InteriorOptions>
        <Option>#InteriorOptionsOption#</Option>
    </InteriorOptions>
    <Heating>
        <Option>#HeatingOption#</Option>
    </Heating>       
    <PowerWindows>#PowerWindows#</PowerWindows>
    <ElectricDrive>
        <Option>#ElectricDriveOption#</Option>
    </ElectricDrive>     
    <MemorySettings>
        <Option>#MemorySettingsOption#</Option>
    </MemorySettings>
    <DrivingAssistance>
        <Option>#DrivingAssistanceOption#</Option>
    </DrivingAssistance>
    <AntitheftSystem>
        <Option>#AntitheftSystemOption#</Option>
    </AntitheftSystem>
    <Airbags>
        <Option>#AirbagsOption#</Option>
    </Airbags>
    <ActiveSafety>
        <Option>#ActiveSafetyOption#</Option>
    </ActiveSafety>
    <Multimedia>
        <Option>#MultimediaOption#</Option>
    </Multimedia>
    <AudioSystem>#AudioSystem#</AudioSystem>
    <AudioSystemOptions>
        <Option>#AudioSystemOptionsOption#</Option>
    </AudioSystemOptions>
    <Lights>#Lights#</Lights>
    <LightsOptions>
        <Option>#LightsOptionsOption#</Option>
    </LightsOptions>
    <Wheels>#Wheels#</Wheels>
    <WheelsOptions>
        <Option>#WheelsOptionsOption#</Option>
    </WheelsOptions>
    <Owners>#Owners#</Owners>
    <Maintenance>
        <Option>#MaintenanceOption#</Option>
    </Maintenance>       
    <Images>
        <Image url=\"#SITE_URL##Image#\"></Image>
    </Images>
    <VideoURL>#VideoURL#</VideoURL>
</Ad>
";
    
$profileTypes['avito_avto']['LOCATION'] = array(
	'yandex' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_AVITO"),
		'sub' => array(
		)
	),
);