<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<? CModule::IncludeModule("iblock"); ?>
<? require_once($_SERVER['DOCUMENT_ROOT'] . "/simplehtmldom/phpQuery-onefile.php"); ?>

<?


$html = file_get_contents('http://www.kolesa.ru/news');
$document = phpQuery::newDocument($html);
$hentry = $document->find('.post-excerpt');
$i = 0;
foreach ($hentry as $el) {
    $i++;
    if ($i > 10) {
        break;
    }
    $pq = pq($el);
    $title = trim($pq->find(".pe-title")->html());
    $short_text = trim($pq->find("p")->html());
    $img = $pq->find("img")->attr("src");
    $link = $pq->attr('href');

    $html2 = file_get_contents($link);
    $document2 = phpQuery::newDocument($html2);
    $hentry2 = $document2->find('.entry-content');
    $full_text = trim(pq($hentry2)->html());
    $post_socials_bottom = trim(pq($hentry2)->find(".post-socials-bottom"));
    $tags = trim(pq($hentry2)->find(".tags-line"));
    $slider_full_width_block = trim(pq($hentry2)->find(".slider-full-width-block "));

    $full_text = str_replace($tags, "", $full_text);
    $full_text = str_replace($post_socials_bottom, "", $full_text);
    $full_text = str_replace($slider_full_width_block, "", $full_text);
    $full_text = trim($full_text);
    $full_text = $full_text . "<br/><p><a href='{$link}' target='_blank'>Ссылка на источник</a></p>";
    $full_text = trim($full_text);


    $el = new CIBlockElement;
    $PROP = array();
    $arParams = array("replace_space" => "-", "replace_other" => "-");
    $code = Cutil::translit($title, "ru", $arParams);
    $arLoadProductArray = Array(
        "MODIFIED_BY" => 1,
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => 13,
        "PROPERTY_VALUES" => $PROP,
        "NAME" => $title,
        "CODE" => $code,
        "ACTIVE" => "Y",
        "PREVIEW_TEXT" => $short_text,
        "DETAIL_TEXT" => $full_text,
        "DETAIL_TEXT_TYPE" => "html",
        "PREVIEW_PICTURE" => CFile::MakeFileArray($img),
        "DETAIL_PICTURE" => CFile::MakeFileArray($img)
    );
    $PRODUCT_ID = $el->Add($arLoadProductArray);

}
?>