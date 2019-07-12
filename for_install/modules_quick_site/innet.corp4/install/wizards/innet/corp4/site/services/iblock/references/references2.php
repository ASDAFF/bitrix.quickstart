<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("highloadblock"))
    return;

//adding rows
WizardServices::IncludeServiceLang("references.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;

global $USER_FIELD_MANAGER;


$COLOR_ID = $_SESSION["INNET_HBLOCK_COLOR_ID"];
unset($_SESSION["INNET_HBLOCK_COLOR_ID"]);

if ($COLOR_ID) {
    $hldata = HL\HighloadBlockTable::getById($COLOR_ID)->fetch();
    $hlentity = HL\HighloadBlockTable::compileEntity($hldata);

    $entity_data_class = $hlentity->getDataClass();
    $arColors = array(
        "PURPLE" => "references_files/iblock/0d3/0d3ef035d0cf3b821449b0174980a712.jpg",
        "BROWN" => "references_files/iblock/f5a/f5a37106cb59ba069cc511647988eb89.jpg",
        "SEE" => "references_files/iblock/f01/f01f801e9da96ae5a7f26aae01255f38.jpg",
        "BLUE" => "references_files/iblock/c1b/c1ba082577379bdc75246974a9f08c8b.jpg",
        "ORANGERED" => "references_files/iblock/0ba/0ba3b7ecdef03a44b145e43aed0cca57.jpg",
        "REDBLUE" => "references_files/iblock/1ac/1ac0a26c5f47bd865a73da765484a2fa.jpg",
        "RED" => "references_files/iblock/0a7/0a7513671518b0f2ce5f7cf44a239a83.jpg",
        "GREEN" => "references_files/iblock/b1c/b1ced825c9803084eb4ea0a742b2342c.jpg",
        "WHITE" => "references_files/iblock/b0e/b0eeeaa3e7519e272b7b382e700cbbc3.jpg",
        "BLACK" => "references_files/iblock/d7b/d7bdba8aca8422e808fb3ad571a74c09.jpg",
        "PINK" => "references_files/iblock/1b6/1b61761da0adce93518a3d613292043a.jpg",
        "AZURE" => "references_files/iblock/c2b/c2b274ad2820451d780ee7cf08d74bb3.jpg",
        "JEANS" => "references_files/iblock/24b/24b082dc5e647a3a945bc9a5c0a200f0.jpg",
        "FLOWERS" => "references_files/iblock/64f/64f32941a654a1cbe2105febe7e77f33.jpg",
        "YELLOW" => "references_files/iblock/15/0d63bddb4688d363b1de7d49f4ac9338.jpg",
        "LIGHT_GREEN" => "references_files/iblock/16/7fdce1693f9b658adee7580f465626f8.jpg",
        "GREY" => "references_files/iblock/17/9f38b04a89596e7988acf4b8f4cfb385.jpg",
        "TURQUOISE" => "references_files/iblock/18/034b17a888efac9cfd1c9377eef7e780.jpg",
        "BITTER_SWEET" => "references_files/iblock/19/e193408f2211b98d5e856c163743d8a7.jpg",
    );
	
    $sort = 0;
	
    foreach ($arColors as $colorName => $colorFile) {
        $sort += 10;
		
        $arData = array(
            'UF_NAME' => GetMessage("WZD_REF_COLOR_" . $colorName),
            'UF_FILE' =>
                array(
                    'name' => ToLower($colorName) . ".jpg",
                    'type' => 'image/jpeg',
                    'tmp_name' => WIZARD_ABSOLUTE_PATH . "/site/services/iblock/references/" . $colorFile
                ),
            'UF_SORT' => $sort,
            'UF_DEF' => ($sort > 10) ? "0" : "1",
            'UF_XML_ID' => ToLower($colorName)
        );
		
        $USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_' . $COLOR_ID, $arData);
        $USER_FIELD_MANAGER->checkFields('HLBLOCK_' . $COLOR_ID, null, $arData);

        $result = $entity_data_class::add($arData);
    }
}


$SIZE_ID = $_SESSION["INNET_HBLOCK_SIZE_ID"];
unset($_SESSION["INNET_HBLOCK_SIZE_ID"]);

if ($SIZE_ID) {
    $hldata = HL\HighloadBlockTable::getById($SIZE_ID)->fetch();
    $hlentity = HL\HighloadBlockTable::compileEntity($hldata);

    $entity_data_class = $hlentity->getDataClass();
    $arSize = array(
        "16" => "16",
        "17" => "17",
        "18" => "18",
        "19" => "19",
        "20" => "20",
        "21" => "21",
        "22" => "22",
        "23" => "23",
        "24" => "24",
        "25" => "25",
        "26" => "26",
        "27" => "27",
        "28" => "28",
        "29" => "29",
        "30" => "30",
        "31" => "31",
        "32" => "32",
        "33" => "33",
        "34" => "34",
        "35" => "35",
        "36" => "36",
        "37" => "37",
        "38" => "38",
        "39" => "39",
        "40" => "40",
        "41" => "41",
        "42" => "42",
        "43" => "43",
    );
	
    $sort = 0;
	
    foreach ($arSize as $sizeName => $sizeValue) {
        $sort += 10;
		
        $arData = array(
            'UF_NAME' => $sizeName,
            'UF_VALUE' => $sizeValue,
            'UF_SORT' => $sort,
            //'UF_DESCRIPTION' => GetMessage("WZD_REF_BRAND_DESCR_" . $brandName),
            //'UF_FULL_DESCRIPTION' => GetMessage("WZD_REF_BRAND_FULL_DESCR_" . $brandName),
            'UF_XML_ID' => $sizeValue,
        );
		
        $USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_' . $SIZE_ID, $arData);
        $USER_FIELD_MANAGER->checkFields('HLBLOCK_' . $SIZE_ID, null, $arData);

        $result = $entity_data_class::add($arData);
    }
}
?>