<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "������ ������ �� �������������: ������, �������, ������� ������, ������ � �����-����������");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("������ ������ �� �������������: ������, �������, ������� ������, ������ � �����-����������");
?> 
<div style="text-align: center; width: 50%; margin-bottom: 20px; float: left; margin-right: 20px; "> 
  <div><strong>����� ������: </strong></div>
 <?$APPLICATION->IncludeComponent(
	"bitrix:photo.random",
	"main",
	Array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCKS" => "#CATALOG_IBLOCK_ID#",
		"PARENT_SECTION" => "#SECTION_NEWPROD_ID#",
		"DETAIL_URL" => "#SITE_DIR#/catalog/#SECTION_NEWPROD_ID#/#ELEMENT_ID#/",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "180",
		"CACHE_NOTES" => "",
		"CACHE_GROUPS" => "Y"
	)
);?> </div>
 
<div style="text-align: center; margin-bottom: 20px; float: left; "> 
  <div><strong>��� ������: </strong></div>
 <?$APPLICATION->IncludeComponent(
	"bitrix:photo.random",
	"main",
	Array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCKS" => "#CATALOG_IBLOCK_ID#",
		"PARENT_SECTION" => "#SECTION_HIT_ID#",
		"DETAIL_URL" => "#SITE_DIR#/catalog/#SECTION_HIT_ID#/#ELEMENT_ID#/",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "180",
		"CACHE_NOTES" => "",
		"CACHE_GROUPS" => "Y"
	)
);?> </div>
 <hr style="MARGIN: 20px 0px; CLEAR: both" /> 
<br />
 
<div class="h2">������� / <a href="/news/" class="subhead" >����� ��������</a></div>
 
<br />
 
<p><?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"top",
	Array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(0=>"",1=>"",),
		"PROPERTY_CODE" => array(0=>"",1=>"",),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "#SITE_DIR#/news/#ID#/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "3600",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "�������",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?><span style="font-size: 7.5pt; ">� </span></p>
<p><font size="2">������� &quot;<strong>������ ������</strong>&quot; ���������� ������������� � �������� ������ ������ � 1993 ����. 
    <br />
   </font></p>
 
<p><font size="2">� ��������� ����� ��������� ����������� ����������� ����� 50-�� ����� �������, ����� ������� ������� ������, ������-�������, ������, ��������� ������ ������. 
    <br />
   </font></p>
 
<p><font size="2">����� ������������ � �������� ���������� �������� �����-����������: &quot;</font><a href="/contacts/3.php" >�������</a><font size="2">&quot;, &quot;������-����&quot;, &quot;��������� ���������&quot;, &quot;</font><a href="/contacts/4.php" >������-����</a><font size="2">&quot;, ������������ �������� � ������� ������ � �� �����. </font><a id="bxid_184814" href="/company/" ><font size="2" color="#0000ff"> 
      <br />
     </font></a></p>
 
<p><a id="bxid_184814" href="/company/" ><font size="2" color="#0000ff">������� ������ ������ </font></a><font size="2">���������� �������� ��� ����������� ���������� ���� � ���������� �������� ���������, ��������� ������������ ���� �����, ��������� � ��������� � ������ � �� �������.</font></p>
 
<p><font size="2">�� ������ ������ ���� </font><a id="bxid_65879" href="/catalog/stock.php" >������ �� ������<font size="2">,</font></a><font size="2">� ����������� ����� �� </font><a href="/discount_coupon/" >5% ������</a><font size="2">. 
    <br />
   </font></p>
 
<p><font size="2">�������� ������� ����� </font><a id="bxid_37419" href="/wheretobuy/" ><font size="2" color="#0000ff">������ �� �����</font></a><font size="2">. ����� � ��� ��������� ������ ������ �����.</font></p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>