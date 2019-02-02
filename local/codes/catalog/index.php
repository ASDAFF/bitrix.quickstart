<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Каталог");
// определим где мы
preg_match('/^[^\?]+/', $_SERVER['REQUEST_URI'], $uri);
$uri = $full_url = explode('/', $uri[0]);
// первое всегда пусто, второе - catalog, последнее пусто
array_shift($uri);
array_shift($uri);
array_pop($uri);

// параметры не участвующие в кэше
$query = DeleteParam(array('clear_cache', /*'SECTION_CODE','PAGEN_2','PAGEN_1'*/));
$obCache = new CPHPCache();
if ($query) {
  $full_url .= '?'.$query;
}
$cacheID = 'catalog_'.md5($full_url); // используем некое уникальное имя для кэша
$cacheLifetime = 3600*24; // сутки
if ( $obCache->InitCache($cacheLifetime, $cacheID) ) {
  $vars = $obCache->GetVars();
  $type = $vars['type'];
  $arSection = $vars['arSection'];
  $arProduct = $vars['arProduct'];
} else {
  $type = 'section';
  $arSection = $arProduct = false;
  CModule::IncludeModule('iblock');
  if ( count($uri) ) {
    // последнее детально товар или раздел
    $last_code = array_pop($uri);
    $uri[] = $last_code;
    if ( $arSection = getSection($last_code) ) {
      $rsPath = CIBlockSection::GetNavChain( $arSection['IBLOCK_ID'], $arSection['ID'] );
      while($arPath = $rsPath->GetNext()) {
        if ( array_shift($uri) != $arPath['CODE'] ) { $arSection = false; break; }
        $APPLICATION->AddChainItem($arPath['NAME'], $arPath['~SECTION_PAGE_URL']);
      }
    } else {
      $res = CIBlockElement::GetList( array(), array( 'IBLOCK_ID'=>CFG::IBLOCK_CATALOG, 'ACTIVE'=>'Y', 'INCLUDE_SUBSECTIONS'=>'Y', 'CODE'=>$last_code ), false, false, array() );
      if ( $arProduct = $res->GetNext() ) {
        $rsPath = CIBlockSection::GetNavChain( $arProduct['IBLOCK_ID'], $arProduct['IBLOCK_SECTION_ID'] );
        while($arPath = $rsPath->GetNext()) {
          if ( array_shift($uri) != $arPath['CODE'] ) { $arProduct = false; break; }
          $APPLICATION->AddChainItem($arPath['NAME'], $arPath['~SECTION_PAGE_URL']);
        }
        if ( $arProduct ) { $type = 'detail'; $APPLICATION->AddChainItem($arProduct['NAME']); }
      }
    }
  } else {
    // корень каталога
    $type = 'root';
  }
  $metaTags = array();
  $title = '';
  if ($type=='root') {

  } elseif ($arSection) {
    $title = $arSection['NAME'];
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arSection['IBLOCK_ID'], $arSection['ID']);
    $meta = $ipropValues->getValues();
    if ( $meta['SECTION_META_TITLE'] ) { $metaTags['title'] = $meta['SECTION_META_TITLE']; }
    if ( $meta['SECTION_META_DESCRIPTION'] ) { $metaTags['desc'] = $meta['SECTION_META_DESCRIPTION']; }
    if ( $meta['SECTION_META_KEYWORDS'] ) { $metaTags['keywords'] = $meta['SECTION_META_KEYWORDS']; }
    if ( $meta['SECTION_PAGE_TITLE'] ) { $title = $meta['SECTION_PAGE_TITLE']; }
  } elseif ($arProduct) {
    $title = $arProduct['NAME'];
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arProduct['IBLOCK_ID'], $arProduct['ID']);
    $meta = $ipropValues->getValues();
    if ( $meta['ELEMENT_PAGE_TITLE'] ) { $metaTags['title'] = $meta['ELEMENT_META_TITLE']; }
    if ( $meta['ELEMENT_META_DESCRIPTION'] ) { $metaTags['desc'] =  $meta['ELEMENT_META_DESCRIPTION']; }
    if ( $meta['ELEMENT_META_KEYWORDS'] ) { $metaTags['keywords'] = $meta['ELEMENT_META_KEYWORDS']; }
    if ( $meta['ELEMENT_PAGE_TITLE'] ) { $title = $meta['ELEMENT_PAGE_TITLE']; }
  }
  $obCache->EndDataCache( array('type' => $type, 'arProduct' => $arProduct, 'arSection' => $arSection, 'title'=>$title, 'metaTags'=>$metaTags ) );
}

if ( !$arSection && !$arProduct && $type != 'root' ) { include $_SERVER['DOCUMENT_ROOT'].'/404.php'; }

// h1 & meta
if ($title) {
  $APPLICATION->SetTitle($title, true);
}
if ( $metaTags['title'] ) { $APPLICATION->SetPageProperty('title', $metaTags['title'] ); }
if ( $metaTags['desc'] ) { $APPLICATION->SetPageProperty('description', $metaTags['desc'] ); }
if ( $metaTags['keywords'] ) { $APPLICATION->SetPageProperty('keywords', $metaTags['keywords'] ); }

switch ($type) {
  case'detail':
    include 'detail.php';
  break;
  case'root':
    include 'root.php';
  break;
  default:
    include 'section.php';
  break;
}

function getSection($code) {
  if ( !$code ) { return false; }
  $res = CIBlockSection::GetList( array(), array( 'IBLOCK_ID'=>CFG::IBLOCK_CATALOG, 'ACTIVE'=>'Y', 'CODE'=>$code ), false );
  if ( $arSection = $res->GetNext() ) {
    // $type = 'section';
    return $arSection;
  }
  return false;
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>