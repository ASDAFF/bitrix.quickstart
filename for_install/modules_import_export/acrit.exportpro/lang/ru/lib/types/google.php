<?
$MESS["ACRIT_EXPORTPRO_PODDERJIVAETSA"] = "Поддерживается Google Merchants";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ID"] = "Идентификатор товара<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_TITLE"] = "Название<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_DESCRIPTION"] = "Описание";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_LINK"] = "URL товара (обязательно начинается с http://)";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_IMAGELINK"] = "URL изображения товара (Должен начинаться с http:// или https://)";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_CONDITION"] = "Состояние товара. Возможные значения:<br>
new - новый, used - б/у, refurbished - восстановленный
<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_AVAILABILITY"] = "Наличие. Возможные значения:
<br>in stock - в наличии, out of stock - нет в наличии, preorder - предзаказ
<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PRICE"] = "Цена<br><b class='required'>Обязательный элемент</b>";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_COUNTRY"] = "Страна доставки";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SERVICE"] = "Служба доставки";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SHIPPINGPRICE"] = "Цена доставки";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_GTIN"] = "Код международной маркировки";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_BRAND"] = "Брэнд товара";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_MPN"] = "Код производителя";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PRODUCTCATEGORY"] = "Категория по классификации Google";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_TYPE"] = "Тип товара";
$MESS['ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SCHEME_DESCRIPTION'] = "<b style='color:red;'>Не забудьте заменить #GOOGLEFEED# необходимым вам значением.<br> Значение #GOOGLEFEED# прописывается вручную<br>Подробнее можно узнать в кабинете пользователя <a href='https://www.google.com/retail/merchant-center/'>Google Merchant Center</a></b>";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_SOURCE"] = "UTM метка: рекламная площадка";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_SOURCE_VALUE"] = "cpc_yandex_market";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_MEDIUM"] = "UTM метка: тип рекламы";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_MEDIUM_VALUE"] = "cpc";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_TERM"] = "UTM метка: ключевая фраза";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_CONTENT"] = "UTM метка: контейнер для дополнительной информации";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_CAMPAIGN"] = "UTM метка: название рекламной кампании";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PORTAL_REQUIREMENTS"] = "https://support.google.com/merchants/?hl=ru#topic=3404818";
$MESS["ACRIT_EXPORTPRO_GOOGLE_MERCHANT_EXAMPLE"] = "
<item>
    <g:id>TV_123456</g:id>
    <g:item_group_id>124</g:item_group_id>
    <g:title>LG 22LB4510 - 22\" LED TV - 1080p (FullHD)</g:title>
    <g:description>Attractively styled and boasting stunning picture quality,
        the LG 22LB4510 - 22&quot; LED TV - 1080p (FullHD) is an excellent television/monitor.
        The LG 22LB4510 - 22&quot; LED TV - 1080p (FullHD) sports a widescreen 1080p panel,
        perfect for watching movies in their original format, whilst also providing plenty of
        working space for your other applications.</g:description>
    <g:link>http://www.example.com/electronics/tv/22LB4510.html</g:link>
    <g:image_link>http://images.example.com/TV_123456.png</g:image_link>
    <g:condition>used</g:condition>
    <g:availability>in stock</g:availability>
    <g:price>159.00 USD</g:price>
    <g:shipping>
        <g:country>US</g:country>
        <g:service>Standard</g:service>
        <g:price>14.95 USD</g:price>
    </g:shipping>
    
    <g:gtin>71919219405200</g:gtin>
    <g:brand>LG</g:brand>
    <g:mpn>22LB4510/US</g:mpn>
    
    <g:google_product_category>Electronics > Video > Televisions > Flat Panel Televisions</g:google_product_category>
    <g:product_type>Consumer Electronics &gt; TVs &gt; Flat Panel TVs</g:product_type>
</item>
";
?>