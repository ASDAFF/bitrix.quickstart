BX.ready(function()
{
    // добавление новых полей param для формы правил
    BX.bind(BX('filter_add'), 'click', function()
    {
        BX('div_filter').appendChild(BX.create('div', {
            html: BX('first_filter').innerHTML
        }));
    });
    // добавление новых полей param для формы правил
    BX.bind(BX('param_add'), 'click', function()
    {
        BX('div_params').appendChild(BX.create('div', {
            html: BX('first_param').innerHTML
        }));
    });
    // обработчик выбора сайта
    BX.bind(BX('f_site_id'), 'change', function()
    {
        BX('f_iblock_type').selectedIndex=0;
        BX('f_iblock_id').innerHTML = "<option></option>";
    });
    // обработчик выбора типа инфоблока
    BX.bind(BX('f_iblock_type'), 'change', function()
    {
        BX.showWait();

        var postData = {
            'sessid': BX.bitrix_sessid(),
            'site_id': BX('f_site_id').value,
            'iblock_type': this.options[this.selectedIndex].value,
            'action': 'get_iblocks_options'
        };

        BX.ajax({
            url: '/bitrix/admin/mibix.yamexport_ajax.php',
            method: 'POST',
            data: postData,
            dataType: 'json',
            onsuccess: function(result){
                BX.closeWait();
                BX('f_iblock_id').innerHTML = result["IBLOCK_OPTIONS"];
            }
        });
    });
    // обработчик выбора разделов
    BX.bind(BX('f_iblock_id'), 'change', function()
    {
        BX.showWait();

        var postData = {
            'sessid': BX.bitrix_sessid(),
            'iblock_id': this.options[this.selectedIndex].value,
            'action': 'get_iblock_sections'
        };

        BX.ajax({
            url: '/bitrix/admin/mibix.yamexport_ajax.php',
            method: 'POST',
            data: postData,
            dataType: 'json',
            onsuccess: function(result){
                BX.closeWait();
                BX('f_include_sections').innerHTML = result["IBLOCK_SECTIONS"];
                BX('f_exclude_sections').innerHTML = result["IBLOCK_SECTIONS"];
            }
        });
    });
    // выбор категорий яндекса
    BX.bind(BX('f_market_category_id_0'), 'change', function(){
        clearSelectFields(1); // чистка элементов
        ajaxGetYMarketCategory(this.options[this.selectedIndex].value, 1); // генерация нового SelectBox
    });
    BX.bind(BX('f_market_category_id_1'), 'change', function(){
        clearSelectFields(2); // чистка элементов
        ajaxGetYMarketCategory(this.options[this.selectedIndex].value, 2);
    });
    BX.bind(BX('f_market_category_id_2'), 'change', function(){
        clearSelectFields(3); // чистка элементов
        ajaxGetYMarketCategory(this.options[this.selectedIndex].value, 3);
    });
    BX.bind(BX('f_market_category_id_3'), 'change', function(){
        clearSelectFields(4); // чистка элементов
        ajaxGetYMarketCategory(this.options[this.selectedIndex].value, 4);
    });
    BX.bind(BX('f_market_category_id_4'), 'change', function(){
        clearSelectFields(5); // чистка элементов
        ajaxGetYMarketCategory(this.options[this.selectedIndex].value, 5);
    });
    BX.bind(BX('f_market_category_id_5'), 'change', function(){
        clearSelectFields(6); // чистка элементов
        ajaxGetYMarketCategory(this.options[this.selectedIndex].value, 6);
    });
    // обработчик при выборе источников данных
    BX.bind(BX('f_datasource_id'), 'change', function()
    {
        BX.showWait();
        var postData = {
            'sessid': BX.bitrix_sessid(),
            'datasource_id': this.options[this.selectedIndex].value,
            'action': 'get_parameters_select'
        };
        BX.ajax({
            url: '/bitrix/admin/mibix.yamexport_ajax.php',
            method: 'POST',
            data: postData,
            dataType: 'json',
            onsuccess: function(result){
                BX.closeWait();
                BX('f_available').innerHTML = result["PARAM_SELECT_AVAILABLE"];
                BX('f_bid').innerHTML = result["PARAM_SELECT_BID"];
                BX('f_cbid').innerHTML = result["PARAM_SELECT_CBID"];
                BX('f_price').innerHTML = result["PARAM_SELECT_PRICE"];
                BX('f_oldprice').innerHTML = result["PARAM_SELECT_OLDPRICE"];
                BX('f_picture').innerHTML = result["PARAM_SELECT_PICTURE"];
                BX('f_typeprefix').innerHTML = result["PARAM_SELECT_TYPEPREFIX"];
                BX('f_model').innerHTML = result["PARAM_SELECT_MODEL"];
                BX('f_store').innerHTML = result["PARAM_SELECT_STORE"];
                BX('f_pickup').innerHTML = result["PARAM_SELECT_PICKUP"];
                BX('f_delivery').innerHTML = result["PARAM_SELECT_DELIVERY"];
                BX('f_name').innerHTML = result["PARAM_SELECT_NAME"];
                BX('f_description').innerHTML = result["PARAM_SELECT_DESCRIPTION"];
                BX('f_vendor').innerHTML = result["PARAM_SELECT_VENDOR"];
                BX('f_vendorcode').innerHTML = result["PARAM_SELECT_VENDORCODE"];
                BX('f_local_delivery_cost').innerHTML = result["PARAM_SELECT_LOCALDELIVERYCOST"];
                BX('f_sales_notes').innerHTML = result["PARAM_SELECT_SALESNOTES"];
                BX('f_manufacturer_warranty').innerHTML = result["PARAM_SELECT_MANUFACTURERWARRANTY"];
                BX('f_seller_warranty').innerHTML = result["PARAM_SELECT_SELLERWARRANTY"];
                BX('f_country_of_origin').innerHTML = result["PARAM_SELECT_COUNTRYOFORIGIN"];
                BX('f_adult').innerHTML = result["PARAM_SELECT_ADULT"];
                BX('f_downloadable').innerHTML = result["PARAM_SELECT_DOWNLOADABLE"];
                BX('f_rec').innerHTML = result["PARAM_SELECT_REC"];
                BX('f_age').innerHTML = result["PARAM_SELECT_AGE"];
                BX('f_ageunit').innerHTML = result["PARAM_SELECT_AGEUNIT"];
                BX('f_barcode').innerHTML = result["PARAM_SELECT_BARCODE"];
                BX('f_expiry').innerHTML = result["PARAM_SELECT_EXPIRY"];
                BX('f_weight').innerHTML = result["PARAM_SELECT_WEIGHT"];
                BX('f_dimensions').innerHTML = result["PARAM_SELECT_DIMENSIONS"];
                BX('f_param').innerHTML = result["PARAM_SELECT_PARAM"]; ////////////////////////////
                BX('f_cpa').innerHTML = result["PARAM_SELECT_CPA"];
                BX('f_author').innerHTML = result["PARAM_SELECT_AUTHOR"];
                BX('f_publisher').innerHTML = result["PARAM_SELECT_PUBLISHER"];
                BX('f_series').innerHTML = result["PARAM_SELECT_SERIES"];
                BX('f_year').innerHTML = result["PARAM_SELECT_YEAR"];
                BX('f_isbn').innerHTML = result["PARAM_SELECT_ISBN"];
                BX('f_volume').innerHTML = result["PARAM_SELECT_VOLUME"];
                BX('f_part').innerHTML = result["PARAM_SELECT_PART"];
                BX('f_language').innerHTML = result["PARAM_SELECT_LANGUAGE"];
                BX('f_binding').innerHTML = result["PARAM_SELECT_BINDING"];
                BX('f_page_extent').innerHTML = result["PARAM_SELECT_PAGEEXTENT"];
                BX('f_table_of_contents').innerHTML = result["PARAM_SELECT_TABLEOFCONTENTS"];
                BX('f_performed_by').innerHTML = result["PARAM_SELECT_PERFORMEDBY"];
                BX('f_performance_type').innerHTML = result["PARAM_SELECT_PERFORMANCETYPE"];
                BX('f_format').innerHTML = result["PARAM_SELECT_FORMAT"];
                BX('f_storage').innerHTML = result["PARAM_SELECT_STORAGE"];
                BX('f_recording_length').innerHTML = result["PARAM_SELECT_RECORDINGLENGTH"];
                BX('f_artist').innerHTML = result["PARAM_SELECT_ARTIST"];
                BX('f_title').innerHTML = result["PARAM_SELECT_TITLE"];
                BX('f_media').innerHTML = result["PARAM_SELECT_MEDIA"];
                BX('f_starring').innerHTML = result["PARAM_SELECT_STARRING"];
                BX('f_director').innerHTML = result["PARAM_SELECT_DIRECTOR"];
                BX('f_originalname').innerHTML = result["PARAM_SELECT_ORIGINALNAME"];
                BX('f_country').innerHTML = result["PARAM_SELECT_COUNTRY"];
                BX('f_worldregion').innerHTML = result["PARAM_SELECT_WORLDREGION"];
                BX('f_region').innerHTML = result["PARAM_SELECT_REGION"];
                BX('f_days').innerHTML = result["PARAM_SELECT_DAYS"];
                BX('f_datatour').innerHTML = result["PARAM_SELECT_DATATOUR"];
                BX('f_hotel_stars').innerHTML = result["PARAM_SELECT_HOTELSTARS"];
                BX('f_room').innerHTML = result["PARAM_SELECT_ROOM"];
                BX('f_meal').innerHTML = result["PARAM_SELECT_MEAL"];
                BX('f_included').innerHTML = result["PARAM_SELECT_INCLUDED"];
                BX('f_transport').innerHTML = result["PARAM_SELECT_TRANSPORT"];
                BX('f_place').innerHTML = result["PARAM_SELECT_PLACE"];
                BX('f_hall_plan').innerHTML = result["PARAM_SELECT_HALLPLAN"];
                BX('f_date').innerHTML = result["PARAM_SELECT_DATE"];
                BX('f_is_premiere').innerHTML = result["PARAM_SELECT_ISPREMIERE"];
                BX('f_is_kids').innerHTML = result["PARAM_SELECT_ISKIDS"];
            }
        });
    });
    // выбор типа категории яндекса из 7 типов (+1 тип дублирующий с разными полями (муз. и видео продукция))
    if(BX('f_type'))
    {
        // определяем выбранный тип категории правила по умолчанию при загрузке формы
        var fType = BX('f_type').options[BX('f_type').selectedIndex].value;
        displayFieldsByType(fType);
    }
    BX.bind(BX('f_type'), 'change', function(){
        displayFieldsByType(this.options[this.selectedIndex].value); // генерация нового SelectBox
    });
    // установка событий для показ/скрытия доп.полей для ввода своего значения (для элементов, у которых есть значение self)
    addFieldBind('bid');
    addFieldBind('cbid');
    addFieldBind('typeprefix');
    addFieldBind('model');
    addFieldBind('local_delivery_cost');
    addFieldBind('sales_notes');
    addFieldBind('manufacturer_warranty');
    addFieldBind('seller_warranty');
    addFieldBind('country_of_origin');
    addFieldBind('age');
    addFieldBind('barcode');
    addFieldBind('expiry');
    addFieldBind('hall_plan');
});

// показ/скрытие поле для установки своего значения
function addFieldBind(name)
{
    BX.bind(BX('f_'+name), 'change', function(){
        if (this.options[this.selectedIndex].value == 'self') {
            BX('selfField_'+name).innerHTML = ''; // предварительная чистка
            BX('selfField_'+name).appendChild(BX.create('input', {
                props: {type:'text',name:'self_'+name,size:'30',maxlength:'255'}
            }));
        } else {
            BX('selfField_'+name).innerHTML = '';
        }
    });
}

// чистим выделенные поля
function clearSelectFields(startNum)
{
    for(var i=startNum;i<=6;i++) {
        if(BX('ymselect_'+i)!=null) {
            BX.hide(BX('ymselect_'+i));
        }
        if(BX('f_market_category_id_'+i)!=null) {
            BX('f_market_category_id_'+i).innerHTML="";
        }
    }
}

// ajax - получение информации о категории яндекса
function ajaxGetYMarketCategory(selected, field)
{
    BX.showWait();
    var postData = {
        'sessid': BX.bitrix_sessid(),
        'parent_id': selected,
        'field': field,
        'action': 'get_market_categories'
    };
    BX.ajax({
        url: '/bitrix/admin/mibix.yamexport_ajax.php',
        method: 'POST',
        data: postData,
        dataType: 'json',
        onsuccess: function(result){
            BX.closeWait();
            var market_category = BX('f_market_category_id_'+field);
            if(market_category!=null)
            {
                if(result["MARKET_CATEGORY"])
                {
                    if(BX('ymselect_'+field)!=null)
                    {
                        BX.show(BX('ymselect_'+field)); // показываем блок
                        market_category.innerHTML = result["MARKET_CATEGORY"]; // выводим элементы
                    }
                }
            }
        }
    });
}

// установка видимости полей в зависимости от выбранного типа описания
function displayFieldsByType(fType)
{
    switch (fType)
    {
        case "vendor.model":
            BX.style(BX('t_typeprefix'), 'display','table-row'); // display
            BX.style(BX('t_model'), 'display','table-row'); // display
            BX.style(BX('t_store'), 'display','table-row'); // display
            BX.style(BX('t_pickup'), 'display','table-row'); // display
            BX.style(BX('t_delivery'), 'display','table-row'); // display
            BX.style(BX('t_name'), 'display','none'); // hide
            BX.style(BX('t_description'), 'display','table-row'); // display
            BX.style(BX('t_description_frm'), 'display','table-row'); // display
            BX.style(BX('t_vendor'), 'display','table-row'); // display
            BX.style(BX('t_vendorcode'), 'display','table-row'); // display
            BX.style(BX('t_local_delivery_cost'), 'display','table-row'); // display
            BX.style(BX('t_sales_notes'), 'display','table-row'); // display
            BX.style(BX('t_manufacturer_warranty'), 'display','table-row'); // display
            BX.style(BX('t_seller_warranty'), 'display','table-row'); // display
            BX.style(BX('t_country_of_origin'), 'display','table-row'); // display
            BX.style(BX('t_adult'), 'display','table-row'); // display
            BX.style(BX('t_downloadable'), 'display','table-row'); // display
            BX.style(BX('t_rec'), 'display','table-row'); // display
            BX.style(BX('t_barcode'), 'display','table-row'); // display
            BX.style(BX('t_expiry'), 'display','table-row'); // display
            BX.style(BX('t_weight'), 'display','table-row'); // display
            BX.style(BX('t_dimensions'), 'display','table-row'); // display
            BX.style(BX('t_param'), 'display','table-row'); // display
            BX.style(BX('t_cpa'), 'display','table-row'); // display
            BX.style(BX('t_author'), 'display','none'); // hide
            BX.style(BX('t_publisher'), 'display','none'); // hide
            BX.style(BX('t_series'), 'display','none'); // hide
            BX.style(BX('t_year'), 'display','none'); // hide
            BX.style(BX('t_isbn'), 'display','none'); // hide
            BX.style(BX('t_volume'), 'display','none'); // hide
            BX.style(BX('t_part'), 'display','none'); // hide
            BX.style(BX('t_language'), 'display','none'); // hide
            BX.style(BX('t_binding'), 'display','none'); // hide
            BX.style(BX('t_page_extent'), 'display','none'); // hide
            BX.style(BX('t_table_of_contents'), 'display','none'); // hide
            BX.style(BX('t_performed_by'), 'display','none'); // hide
            BX.style(BX('t_performance_type'), 'display','none'); // hide
            BX.style(BX('t_format'), 'display','none'); // hide
            BX.style(BX('t_storage'), 'display','none'); // hide
            BX.style(BX('t_recording_length'), 'display','none'); // hide
            BX.style(BX('t_artist'), 'display','none'); // hide
            BX.style(BX('t_title'), 'display','none'); // hide
            BX.style(BX('t_media'), 'display','none'); // hide
            BX.style(BX('t_starring'), 'display','none'); // hide
            BX.style(BX('t_director'), 'display','none'); // hide
            BX.style(BX('t_originalname'), 'display','none'); // hide
            BX.style(BX('t_country'), 'display','none'); // hide
            BX.style(BX('t_worldregion'), 'display','none'); // hide
            BX.style(BX('t_region'), 'display','none'); // hide
            BX.style(BX('t_days'), 'display','none'); // hide
            BX.style(BX('t_datatour'), 'display','none'); // hide
            BX.style(BX('t_hotel_stars'), 'display','none'); // hide
            BX.style(BX('t_room'), 'display','none'); // hide
            BX.style(BX('t_meal'), 'display','none'); // hide
            BX.style(BX('t_included'), 'display','none'); // hide
            BX.style(BX('t_transport'), 'display','none'); // hide
            BX.style(BX('t_place'), 'display','none'); // hide
            BX.style(BX('t_hall_plan'), 'display','none'); // hide
            BX.style(BX('t_date'), 'display','none'); // hide
            BX.style(BX('t_is_premiere'), 'display','none'); // hide
            BX.style(BX('t_is_kids'), 'display','none'); // hide
            break;
        case "book":
            BX.style(BX('t_typeprefix'), 'display','none'); // hide
            BX.style(BX('t_model'), 'display','none'); // hide
            BX.style(BX('t_store'), 'display','table-row'); // display
            BX.style(BX('t_pickup'), 'display','table-row'); // display
            BX.style(BX('t_delivery'), 'display','table-row'); // display
            BX.style(BX('t_name'), 'display','table-row'); // display
            BX.style(BX('t_description'), 'display','table-row'); // display
            BX.style(BX('t_description_frm'), 'display','table-row'); // display
            BX.style(BX('t_vendor'), 'display','none'); // hide
            BX.style(BX('t_vendorсode'), 'display','none'); // hide
            BX.style(BX('t_local_delivery_cost'), 'display','table-row'); // display
            BX.style(BX('t_sales_notes'), 'display','none'); // hide
            BX.style(BX('t_manufacturer_warranty'), 'display','none'); // hide
            BX.style(BX('t_seller_warranty'), 'display','none'); // hide
            BX.style(BX('t_country_of_origin'), 'display','none'); // hide
            BX.style(BX('t_adult'), 'display','none'); // hide
            BX.style(BX('t_downloadable'), 'display','table-row'); // display
            BX.style(BX('t_rec'), 'display','none'); // hide
            BX.style(BX('t_barcode'), 'display','none'); // hide
            BX.style(BX('t_expiry'), 'display','none'); // hide
            BX.style(BX('t_weight'), 'display','none'); // hide
            BX.style(BX('t_dimensions'), 'display','none'); // hide
            BX.style(BX('t_param'), 'display','none'); // hide
            BX.style(BX('t_cpa'), 'display','none'); // hide
            BX.style(BX('t_author'), 'display','table-row'); // display
            BX.style(BX('t_publisher'), 'display','table-row'); // display
            BX.style(BX('t_series'), 'display','table-row'); // display
            BX.style(BX('t_year'), 'display','table-row'); // display
            BX.style(BX('t_isbn'), 'display','table-row'); // display
            BX.style(BX('t_volume'), 'display','table-row'); // display
            BX.style(BX('t_part'), 'display','table-row'); // display
            BX.style(BX('t_language'), 'display','table-row'); // display
            BX.style(BX('t_binding'), 'display','table-row'); // display
            BX.style(BX('t_page_extent'), 'display','table-row'); // display
            BX.style(BX('t_table_of_contents'), 'display','table-row'); // display
            BX.style(BX('t_performed_by'), 'display','none'); // hide
            BX.style(BX('t_performance_type'), 'display','none'); // hide
            BX.style(BX('t_format'), 'display','none'); // hide
            BX.style(BX('t_storage'), 'display','none'); // hide
            BX.style(BX('t_recording_length'), 'display','none'); // hide
            BX.style(BX('t_artist'), 'display','none'); // hide
            BX.style(BX('t_title'), 'display','none'); // hide
            BX.style(BX('t_media'), 'display','none'); // hide
            BX.style(BX('t_starring'), 'display','none'); // hide
            BX.style(BX('t_director'), 'display','none'); // hide
            BX.style(BX('t_originalname'), 'display','none'); // hide
            BX.style(BX('t_country'), 'display','none'); // hide
            BX.style(BX('t_worldregion'), 'display','none'); // hide
            BX.style(BX('t_region'), 'display','none'); // hide
            BX.style(BX('t_days'), 'display','none'); // hide
            BX.style(BX('t_datatour'), 'display','none'); // hide
            BX.style(BX('t_hotel_stars'), 'display','none'); // hide
            BX.style(BX('t_room'), 'display','none'); // hide
            BX.style(BX('t_meal'), 'display','none'); // hide
            BX.style(BX('t_included'), 'display','none'); // hide
            BX.style(BX('t_transport'), 'display','none'); // hide
            BX.style(BX('t_place'), 'display','none'); // hide
            BX.style(BX('t_hall_plan'), 'display','none'); // hide
            BX.style(BX('t_date'), 'display','none'); // hide
            BX.style(BX('t_is_premiere'), 'display','none'); // hide
            BX.style(BX('t_is_kids'), 'display','none'); // hide
            break;
        case "audiobook":
            BX.style(BX('t_typeprefix'), 'display','none'); // hide
            BX.style(BX('t_model'), 'display','none'); // hide
            BX.style(BX('t_store'), 'display','none'); // hide
            BX.style(BX('t_pickup'), 'display','none'); // hide
            BX.style(BX('t_delivery'), 'display','none'); // hide
            BX.style(BX('t_name'), 'display','table-row'); // display
            BX.style(BX('t_description'), 'display','table-row'); // display
            BX.style(BX('t_description_frm'), 'display','table-row'); // display
            BX.style(BX('t_vendor'), 'display','none'); // hide
            BX.style(BX('t_vendorсode'), 'display','none'); // hide
            BX.style(BX('t_local_delivery_cost'), 'display','none'); // hide
            BX.style(BX('t_sales_notes'), 'display','none'); // hide
            BX.style(BX('t_manufacturer_warranty'), 'display','none'); // hide
            BX.style(BX('t_seller_warranty'), 'display','none'); // hide
            BX.style(BX('t_country_of_origin'), 'display','none'); // hide
            BX.style(BX('t_adult'), 'display','none'); // hide
            BX.style(BX('t_downloadable'), 'display','table-row'); // display
            BX.style(BX('t_rec'), 'display','none'); // hide
            BX.style(BX('t_barcode'), 'display','none'); // hide
            BX.style(BX('t_expiry'), 'display','none'); // hide
            BX.style(BX('t_weight'), 'display','none'); // hide
            BX.style(BX('t_dimensions'), 'display','none'); // hide
            BX.style(BX('t_param'), 'display','none'); // hide
            BX.style(BX('t_cpa'), 'display','none'); // hide
            BX.style(BX('t_author'), 'display','table-row'); // display
            BX.style(BX('t_publisher'), 'display','table-row'); // display
            BX.style(BX('t_series'), 'display','table-row'); // display
            BX.style(BX('t_year'), 'display','table-row'); // display
            BX.style(BX('t_isbn'), 'display','table-row'); // display
            BX.style(BX('t_volume'), 'display','table-row'); // display
            BX.style(BX('t_part'), 'display','table-row'); // display
            BX.style(BX('t_language'), 'display','table-row'); // display
            BX.style(BX('t_binding'), 'display','none'); // hide
            BX.style(BX('t_page_extent'), 'display','none'); // hide
            BX.style(BX('t_table_of_contents'), 'display','table-row'); // display
            BX.style(BX('t_performed_by'), 'display','table-row'); // display
            BX.style(BX('t_performance_type'), 'display','table-row'); // display
            BX.style(BX('t_format'), 'display','table-row'); // display
            BX.style(BX('t_storage'), 'display','table-row'); // display
            BX.style(BX('t_recording_length'), 'display','table-row'); // display
            BX.style(BX('t_artist'), 'display','none'); // hide
            BX.style(BX('t_title'), 'display','none'); // hide
            BX.style(BX('t_media'), 'display','none'); // hide
            BX.style(BX('t_starring'), 'display','none'); // hide
            BX.style(BX('t_director'), 'display','none'); // hide
            BX.style(BX('t_originalname'), 'display','none'); // hide
            BX.style(BX('t_country'), 'display','none'); // hide
            BX.style(BX('t_worldregion'), 'display','none'); // hide
            BX.style(BX('t_region'), 'display','none'); // hide
            BX.style(BX('t_days'), 'display','none'); // hide
            BX.style(BX('t_datatour'), 'display','none'); // hide
            BX.style(BX('t_hotel_stars'), 'display','none'); // hide
            BX.style(BX('t_room'), 'display','none'); // hide
            BX.style(BX('t_meal'), 'display','none'); // hide
            BX.style(BX('t_included'), 'display','none'); // hide
            BX.style(BX('t_transport'), 'display','none'); // hide
            BX.style(BX('t_place'), 'display','none'); // hide
            BX.style(BX('t_hall_plan'), 'display','none'); // hide
            BX.style(BX('t_date'), 'display','none'); // hide
            BX.style(BX('t_is_premiere'), 'display','none'); // hide
            BX.style(BX('t_is_kids'), 'display','none'); // hide
            break;
        case "artist.title.m":
            BX.style(BX('t_typeprefix'), 'display','none'); // hide
            BX.style(BX('t_model'), 'display','none'); // hide
            BX.style(BX('t_store'), 'display','table-row'); // display
            BX.style(BX('t_pickup'), 'display','table-row'); // display
            BX.style(BX('t_delivery'), 'display','table-row'); // display
            BX.style(BX('t_name'), 'display','none'); // hide
            BX.style(BX('t_description'), 'display','table-row'); // display
            BX.style(BX('t_description_frm'), 'display','table-row'); // display
            BX.style(BX('t_vendor'), 'display','none'); // hide
            BX.style(BX('t_vendorсode'), 'display','none'); // hide
            BX.style(BX('t_local_delivery_cost'), 'display','none'); // hide
            BX.style(BX('t_sales_notes'), 'display','none'); // hide
            BX.style(BX('t_manufacturer_warranty'), 'display','none'); // hide
            BX.style(BX('t_seller_warranty'), 'display','none'); // hide
            BX.style(BX('t_country_of_origin'), 'display','none'); // hide
            BX.style(BX('t_adult'), 'display','none'); // hide
            BX.style(BX('t_downloadable'), 'display','none'); // hide
            BX.style(BX('t_rec'), 'display','none'); // hide
            BX.style(BX('t_barcode'), 'display','table-row'); // display
            BX.style(BX('t_expiry'), 'display','none'); // hide
            BX.style(BX('t_weight'), 'display','none'); // hide
            BX.style(BX('t_dimensions'), 'display','none'); // hide
            BX.style(BX('t_param'), 'display','none'); // hide
            BX.style(BX('t_cpa'), 'display','none'); // hide
            BX.style(BX('t_author'), 'display','none'); // hide
            BX.style(BX('t_publisher'), 'display','none'); // hide
            BX.style(BX('t_series'), 'display','none'); // hide
            BX.style(BX('t_year'), 'display','table-row'); // display
            BX.style(BX('t_isbn'), 'display','none'); // hide
            BX.style(BX('t_volume'), 'display','none'); // hide
            BX.style(BX('t_part'), 'display','none'); // hide
            BX.style(BX('t_language'), 'display','none'); // hide
            BX.style(BX('t_binding'), 'display','none'); // hide
            BX.style(BX('t_page_extent'), 'display','none'); // hide
            BX.style(BX('t_table_of_contents'), 'display','none'); // hide
            BX.style(BX('t_performed_by'), 'display','none'); // hide
            BX.style(BX('t_performance_type'), 'display','none'); // hide
            BX.style(BX('t_format'), 'display','none'); // hide
            BX.style(BX('t_storage'), 'display','none'); // hide
            BX.style(BX('t_recording_length'), 'display','none'); // hide
            BX.style(BX('t_artist'), 'display','table-row'); // display
            BX.style(BX('t_title'), 'display','table-row'); // display
            BX.style(BX('t_media'), 'display','table-row'); // display
            BX.style(BX('t_starring'), 'display','none'); // hide
            BX.style(BX('t_director'), 'display','none'); // hide
            BX.style(BX('t_originalname'), 'display','none'); // hide
            BX.style(BX('t_country'), 'display','none'); // hide
            BX.style(BX('t_worldregion'), 'display','none'); // hide
            BX.style(BX('t_region'), 'display','none'); // hide
            BX.style(BX('t_days'), 'display','none'); // hide
            BX.style(BX('t_datatour'), 'display','none'); // hide
            BX.style(BX('t_hotel_stars'), 'display','none'); // hide
            BX.style(BX('t_room'), 'display','none'); // hide
            BX.style(BX('t_meal'), 'display','none'); // hide
            BX.style(BX('t_included'), 'display','none'); // hide
            BX.style(BX('t_transport'), 'display','none'); // hide
            BX.style(BX('t_place'), 'display','none'); // hide
            BX.style(BX('t_hall_plan'), 'display','none'); // hide
            BX.style(BX('t_date'), 'display','none'); // hide
            BX.style(BX('t_is_premiere'), 'display','none'); // hide
            BX.style(BX('t_is_kids'), 'display','none'); // hide
            break;
        case "artist.title.v":
            BX.style(BX('t_typeprefix'), 'display','none'); // hide
            BX.style(BX('t_model'), 'display','none'); // hide
            BX.style(BX('t_store'), 'display','table-row'); // display
            BX.style(BX('t_pickup'), 'display','table-row'); // display
            BX.style(BX('t_delivery'), 'display','table-row'); // display
            BX.style(BX('t_name'), 'display','none'); // hide
            BX.style(BX('t_description'), 'display','table-row'); // display
            BX.style(BX('t_description_frm'), 'display','table-row'); // display
            BX.style(BX('t_vendor'), 'display','none'); // hide
            BX.style(BX('t_vendorсode'), 'display','none'); // hide
            BX.style(BX('t_local_delivery_cost'), 'display','none'); // hide
            BX.style(BX('t_sales_notes'), 'display','none'); // hide
            BX.style(BX('t_manufacturer_warranty'), 'display','none'); // hide
            BX.style(BX('t_seller_warranty'), 'display','none'); // hide
            BX.style(BX('t_country_of_origin'), 'display','none'); // hide
            BX.style(BX('t_adult'), 'display','table-row'); // display
            BX.style(BX('t_downloadable'), 'display','none'); // hide
            BX.style(BX('t_rec'), 'display','none'); // hide
            BX.style(BX('t_barcode'), 'display','table-row'); // display
            BX.style(BX('t_expiry'), 'display','none'); // hide
            BX.style(BX('t_weight'), 'display','none'); // hide
            BX.style(BX('t_dimensions'), 'display','none'); // hide
            BX.style(BX('t_param'), 'display','none'); // hide
            BX.style(BX('t_cpa'), 'display','none'); // hide
            BX.style(BX('t_author'), 'display','none'); // hide
            BX.style(BX('t_publisher'), 'display','none'); // hide
            BX.style(BX('t_series'), 'display','none'); // hide
            BX.style(BX('t_year'), 'display','table-row'); // display
            BX.style(BX('t_isbn'), 'display','none'); // hide
            BX.style(BX('t_volume'), 'display','none'); // hide
            BX.style(BX('t_part'), 'display','none'); // hide
            BX.style(BX('t_language'), 'display','none'); // hide
            BX.style(BX('t_binding'), 'display','none'); // hide
            BX.style(BX('t_page_extent'), 'display','none'); // hide
            BX.style(BX('t_table_of_contents'), 'display','none'); // hide
            BX.style(BX('t_performed_by'), 'display','none'); // hide
            BX.style(BX('t_performance_type'), 'display','none'); // hide
            BX.style(BX('t_format'), 'display','none'); // hide
            BX.style(BX('t_storage'), 'display','none'); // hide
            BX.style(BX('t_recording_length'), 'display','none'); // hide
            BX.style(BX('t_artist'), 'display','none'); // hide
            BX.style(BX('t_title'), 'display','table-row'); // display
            BX.style(BX('t_media'), 'display','table-row'); // display
            BX.style(BX('t_starring'), 'display','table-row'); // display
            BX.style(BX('t_director'), 'display','table-row'); // display
            BX.style(BX('t_originalname'), 'display','table-row'); // display
            BX.style(BX('t_country'), 'display','table-row'); // display
            BX.style(BX('t_worldregion'), 'display','none'); // hide
            BX.style(BX('t_region'), 'display','none'); // hide
            BX.style(BX('t_days'), 'display','none'); // hide
            BX.style(BX('t_datatour'), 'display','none'); // hide
            BX.style(BX('t_hotel_stars'), 'display','none'); // hide
            BX.style(BX('t_room'), 'display','none'); // hide
            BX.style(BX('t_meal'), 'display','none'); // hide
            BX.style(BX('t_included'), 'display','none'); // hide
            BX.style(BX('t_transport'), 'display','none'); // hide
            BX.style(BX('t_place'), 'display','none'); // hide
            BX.style(BX('t_hall_plan'), 'display','none'); // hide
            BX.style(BX('t_date'), 'display','none'); // hide
            BX.style(BX('t_is_premiere'), 'display','none'); // hide
            BX.style(BX('t_is_kids'), 'display','none'); // hide
            break;
        case "tour":
            BX.style(BX('t_typeprefix'), 'display','none'); // hide
            BX.style(BX('t_model'), 'display','none'); // hide
            BX.style(BX('t_store'), 'display','table-row'); // display
            BX.style(BX('t_pickup'), 'display','table-row'); // display
            BX.style(BX('t_delivery'), 'display','table-row'); // display
            BX.style(BX('t_name'), 'display','table-row'); // display
            BX.style(BX('t_description'), 'display','table-row'); // display
            BX.style(BX('t_description_frm'), 'display','table-row'); // display
            BX.style(BX('t_vendor'), 'display','none'); // hide
            BX.style(BX('t_vendorсode'), 'display','none'); // hide
            BX.style(BX('t_local_delivery_cost'), 'display','none'); // hide
            BX.style(BX('t_sales_notes'), 'display','none'); // hide
            BX.style(BX('t_manufacturer_warranty'), 'display','none'); // hide
            BX.style(BX('t_seller_warranty'), 'display','none'); // hide
            BX.style(BX('t_country_of_origin'), 'display','none'); // hide
            BX.style(BX('t_adult'), 'display','none'); // hide
            BX.style(BX('t_downloadable'), 'display','none'); // hide
            BX.style(BX('t_rec'), 'display','none'); // hide
            BX.style(BX('t_barcode'), 'display','table-row'); // display
            BX.style(BX('t_expiry'), 'display','none'); // hide
            BX.style(BX('t_weight'), 'display','none'); // hide
            BX.style(BX('t_dimensions'), 'display','none'); // hide
            BX.style(BX('t_param'), 'display','none'); // hide
            BX.style(BX('t_cpa'), 'display','none'); // hide
            BX.style(BX('t_author'), 'display','none'); // hide
            BX.style(BX('t_publisher'), 'display','none'); // hide
            BX.style(BX('t_series'), 'display','none'); // hide
            BX.style(BX('t_year'), 'display','none'); // hide
            BX.style(BX('t_isbn'), 'display','none'); // hide
            BX.style(BX('t_volume'), 'display','none'); // hide
            BX.style(BX('t_part'), 'display','none'); // hide
            BX.style(BX('t_language'), 'display','none'); // hide
            BX.style(BX('t_binding'), 'display','none'); // hide
            BX.style(BX('t_page_extent'), 'display','none'); // hide
            BX.style(BX('t_table_of_contents'), 'display','none'); // hide
            BX.style(BX('t_performed_by'), 'display','none'); // hide
            BX.style(BX('t_performance_type'), 'display','none'); // hide
            BX.style(BX('t_format'), 'display','none'); // hide
            BX.style(BX('t_storage'), 'display','none'); // hide
            BX.style(BX('t_recording_length'), 'display','none'); // hide
            BX.style(BX('t_artist'), 'display','none'); // hide
            BX.style(BX('t_title'), 'display','none'); // hide
            BX.style(BX('t_media'), 'display','none'); // hide
            BX.style(BX('t_starring'), 'display','none'); // hide
            BX.style(BX('t_director'), 'display','none'); // hide
            BX.style(BX('t_originalname'), 'display','none'); // hide
            BX.style(BX('t_country'), 'display','table-row'); // display
            BX.style(BX('t_worldregion'), 'display','table-row'); // display
            BX.style(BX('t_region'), 'display','table-row'); // display
            BX.style(BX('t_days'), 'display','table-row'); // display
            BX.style(BX('t_datatour'), 'display','table-row'); // display
            BX.style(BX('t_hotel_stars'), 'display','table-row'); // display
            BX.style(BX('t_room'), 'display','table-row'); // display
            BX.style(BX('t_meal'), 'display','table-row'); // display
            BX.style(BX('t_included'), 'display','table-row'); // display
            BX.style(BX('t_transport'), 'display','table-row'); // display
            BX.style(BX('t_place'), 'display','none'); // hide
            BX.style(BX('t_hall_plan'), 'display','none'); // hide
            BX.style(BX('t_date'), 'display','none'); // hide
            BX.style(BX('t_is_premiere'), 'display','none'); // hide
            BX.style(BX('t_is_kids'), 'display','none'); // hide
            break;
        case "event-ticket":
            BX.style(BX('t_typeprefix'), 'display','none'); // hide
            BX.style(BX('t_model'), 'display','none'); // hide
            BX.style(BX('t_store'), 'display','table-row'); // display
            BX.style(BX('t_pickup'), 'display','table-row'); // display
            BX.style(BX('t_delivery'), 'display','table-row'); // display
            BX.style(BX('t_name'), 'display','table-row'); // display
            BX.style(BX('t_description'), 'display','table-row'); // display
            BX.style(BX('t_description_frm'), 'display','table-row'); // display
            BX.style(BX('t_vendor'), 'display','none'); // hide
            BX.style(BX('t_vendorсode'), 'display','none'); // hide
            BX.style(BX('t_local_delivery_cost'), 'display','none'); // hide
            BX.style(BX('t_sales_notes'), 'display','none'); // hide
            BX.style(BX('t_manufacturer_warranty'), 'display','none'); // hide
            BX.style(BX('t_seller_warranty'), 'display','none'); // hide
            BX.style(BX('t_country_of_origin'), 'display','none'); // hide
            BX.style(BX('t_adult'), 'display','none'); // hide
            BX.style(BX('t_downloadable'), 'display','none'); // hide
            BX.style(BX('t_rec'), 'display','none'); // hide
            BX.style(BX('t_barcode'), 'display','table-row'); // display
            BX.style(BX('t_expiry'), 'display','none'); // hide
            BX.style(BX('t_weight'), 'display','none'); // hide
            BX.style(BX('t_dimensions'), 'display','none'); // hide
            BX.style(BX('t_param'), 'display','none'); // hide
            BX.style(BX('t_cpa'), 'display','none'); // hide
            BX.style(BX('t_author'), 'display','none'); // hide
            BX.style(BX('t_publisher'), 'display','none'); // hide
            BX.style(BX('t_series'), 'display','none'); // hide
            BX.style(BX('t_year'), 'display','none'); // hide
            BX.style(BX('t_isbn'), 'display','none'); // hide
            BX.style(BX('t_volume'), 'display','none'); // hide
            BX.style(BX('t_part'), 'display','none'); // hide
            BX.style(BX('t_language'), 'display','none'); // hide
            BX.style(BX('t_binding'), 'display','none'); // hide
            BX.style(BX('t_page_extent'), 'display','none'); // hide
            BX.style(BX('t_table_of_contents'), 'display','none'); // hide
            BX.style(BX('t_performed_by'), 'display','none'); // hide
            BX.style(BX('t_performance_type'), 'display','none'); // hide
            BX.style(BX('t_format'), 'display','none'); // hide
            BX.style(BX('t_storage'), 'display','none'); // hide
            BX.style(BX('t_recording_length'), 'display','none'); // hide
            BX.style(BX('t_artist'), 'display','none'); // hide
            BX.style(BX('t_title'), 'display','none'); // hide
            BX.style(BX('t_media'), 'display','none'); // hide
            BX.style(BX('t_starring'), 'display','none'); // hide
            BX.style(BX('t_director'), 'display','none'); // hide
            BX.style(BX('t_originalname'), 'display','none'); // hide
            BX.style(BX('t_country'), 'display','none'); // hide
            BX.style(BX('t_worldregion'), 'display','none'); // hide
            BX.style(BX('t_region'), 'display','none'); // hide
            BX.style(BX('t_days'), 'display','none'); // hide
            BX.style(BX('t_datatour'), 'display','none'); // hide
            BX.style(BX('t_hotel_stars'), 'display','none'); // hide
            BX.style(BX('t_room'), 'display','none'); // hide
            BX.style(BX('t_meal'), 'display','none'); // hide
            BX.style(BX('t_included'), 'display','none'); // hide
            BX.style(BX('t_transport'), 'display','none'); // hide
            BX.style(BX('t_place'), 'display','table-row'); // display
            BX.style(BX('t_hall_plan'), 'display','table-row'); // display
            BX.style(BX('t_date'), 'display','table-row'); // display
            BX.style(BX('t_is_premiere'), 'display','table-row'); // display
            BX.style(BX('t_is_kids'), 'display','table-row'); // display
            break;
        default:
            BX.style(BX('t_typeprefix'), 'display','none'); // hide
            BX.style(BX('t_model'), 'display','none'); // hide
            BX.style(BX('t_store'), 'display','table-row'); // display
            BX.style(BX('t_pickup'), 'display','table-row'); // display
            BX.style(BX('t_delivery'), 'display','table-row'); // display
            BX.style(BX('t_name'), 'display','table-row'); // display
            BX.style(BX('t_description'), 'display','table-row'); // display
            BX.style(BX('t_description_frm'), 'display','table-row'); // display
            BX.style(BX('t_vendor'), 'display','table-row'); // display
            BX.style(BX('t_vendorсode'), 'display','table-row'); // display         
            BX.style(BX('t_local_delivery_cost'), 'display','table-row'); // display
            BX.style(BX('t_sales_notes'), 'display','table-row'); // display
            BX.style(BX('t_manufacturer_warranty'), 'display','table-row'); // display
            BX.style(BX('t_seller_warranty'), 'display','none'); // hide
            BX.style(BX('t_country_of_origin'), 'display','table-row'); // display
            BX.style(BX('t_adult'), 'display','table-row'); // display
            BX.style(BX('t_downloadable'), 'display','none'); // hide
            BX.style(BX('t_rec'), 'display','none'); // hide
            BX.style(BX('t_barcode'), 'display','table-row'); // display
            BX.style(BX('t_expiry'), 'display','none'); // hide
            BX.style(BX('t_weight'), 'display','none'); // hide
            BX.style(BX('t_dimensions'), 'display','none'); // hide
            BX.style(BX('t_param'), 'display','table-row'); // display
            BX.style(BX('t_cpa'), 'display','table-row'); // display
            BX.style(BX('t_author'), 'display','none'); // hide
            BX.style(BX('t_publisher'), 'display','none'); // hide
            BX.style(BX('t_series'), 'display','none'); // hide
            BX.style(BX('t_year'), 'display','none'); // hide
            BX.style(BX('t_isbn'), 'display','none'); // hide
            BX.style(BX('t_volume'), 'display','none'); // hide
            BX.style(BX('t_part'), 'display','none'); // hide
            BX.style(BX('t_language'), 'display','none'); // hide
            BX.style(BX('t_binding'), 'display','none'); // hide
            BX.style(BX('t_page_extent'), 'display','none'); // hide
            BX.style(BX('t_table_of_contents'), 'display','none'); // hide
            BX.style(BX('t_performed_by'), 'display','none'); // hide
            BX.style(BX('t_performance_type'), 'display','none'); // hide
            BX.style(BX('t_format'), 'display','none'); // hide
            BX.style(BX('t_storage'), 'display','none'); // hide
            BX.style(BX('t_recording_length'), 'display','none'); // hide
            BX.style(BX('t_artist'), 'display','none'); // hide
            BX.style(BX('t_title'), 'display','none'); // hide
            BX.style(BX('t_media'), 'display','none'); // hide
            BX.style(BX('t_starring'), 'display','none'); // hide
            BX.style(BX('t_director'), 'display','none'); // hide
            BX.style(BX('t_originalname'), 'display','none'); // hide
            BX.style(BX('t_country'), 'display','none'); // hide
            BX.style(BX('t_worldregion'), 'display','none'); // hide
            BX.style(BX('t_region'), 'display','none'); // hide
            BX.style(BX('t_days'), 'display','none'); // hide
            BX.style(BX('t_datatour'), 'display','none'); // hide
            BX.style(BX('t_hotel_stars'), 'display','none'); // hide
            BX.style(BX('t_room'), 'display','none'); // hide
            BX.style(BX('t_meal'), 'display','none'); // hide
            BX.style(BX('t_included'), 'display','none'); // hide
            BX.style(BX('t_transport'), 'display','none'); // hide
            BX.style(BX('t_place'), 'display','none'); // hide
            BX.style(BX('t_hall_plan'), 'display','none'); // hide
            BX.style(BX('t_date'), 'display','none'); // hide
            BX.style(BX('t_is_premiere'), 'display','none'); // hide
            BX.style(BX('t_is_kids'), 'display','none'); // hide
            break;
    }
}