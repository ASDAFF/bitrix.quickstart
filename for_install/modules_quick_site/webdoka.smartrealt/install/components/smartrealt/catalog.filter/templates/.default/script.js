function SmartRealtFilter()
{
    var oFilterForm = $('#CatalogFilter');
    var oRegionAreaId = $('#RegionAreaId');
    var oCityId = $('#CityId');
    var oTownId = $('#TownId'); 
    var oRoomQuantityContainer = $('#RoomQuantityContainer');
    var oEstateMarketContainer = $('#EstateMarketContainer');
    var oLocationTypeCity = $('#LocationTypeCity');
    var oLocationTypeRegionArea = $('#LocationTypeRegionArea');
    var oCityAreaContainer = $('#CityAreaContainer');
    var oCityAreaIdContainer = $('#CityAreaIdContainer');
    var sTypeId = null;   
    var arTownByRegionArea = null;
    var arTownByCity = null;
    var arCityAreaByCity = null;
    var sDefRegionAreaId = null;
    var sDefCityId = null;
    var sDefTownIdByCity = null;
    var sDefTownIdByRegionArea = null;
    var filterName = null;      
    var filterFormName = null;        
    
    function initLocationHandlers()
    {    
        oLocationTypeCity.bind('click', function(){
                onChangeLocationType();
            });
        oLocationTypeRegionArea.bind('click', function(){
                onChangeLocationType();
            });
        oRegionAreaId.bind('change', function(){
                if (oRegionAreaId.val().length > 0)
                    fillTownByRegionArea(oRegionAreaId.val(), sDefTownIdByRegionArea);
                else
                    oTownId.hide();
                checkSelectStyles(); 
            });
        oCityId.bind('change', function(){
                if (oCityId.val().length > 0)
                {
                    fillTownByCity(oCityId.val(), sDefTownIdByCity);
                    fillCityAreaByCityId(oCityId.val(), '');
                }
                else
                    oTownId.hide();
                checkSelectStyles(); 
            });
        oTownId.bind('change', function(){
                checkSelectStyles();
            });
    }   
    
    function setVisibleFields()
    {
        var arTypes = sTypeId.split(';');
        
        for (var i=0;i<arTypes.length;i++)
        {
            switch (arTypes[i])
            {
                case '2':
                case '4':
                case '5':
                case '19':
                    oRoomQuantityContainer.show();
                    oEstateMarketContainer.show();
                    break;
                default:
                    oRoomQuantityContainer.hide();
                    oEstateMarketContainer.hide();
                    break;
            }
        }
    }   
    
    function checkSelectStyles()
    {
        if (oRegionAreaId.val().length == 0)
            oRegionAreaId.addClass('notSelect');
        else
            oRegionAreaId.removeClass('notSelect');

        if (oCityId.val().length == 0)
            oCityId.addClass('notSelect');
        else
            oCityId.removeClass('notSelect');
            
        var sTownId = oTownId.val();
        if (sTownId != null && sTownId.length == 0)
            oTownId.addClass('notSelect');
        else
            oTownId.removeClass('notSelect');
    }
    
    function onChangeLocationType()
    {
        if (oLocationTypeCity.eq(0).attr("checked"))
        {
            oRegionAreaId.hide();
            oCityId.show();
            
            if (oRegionAreaId.val().length > 0) 
                sDefRegionAreaId = oRegionAreaId.val();
            if (oTownId.val() && oTownId.val().length > 0)
                sDefTownIdByRegionArea = oTownId.val();
            
            oTownId.empty();
            
            oRegionAreaId.val("");
            oCityId.val(sDefCityId);
            oCityId.change();
        }
        else
        {                   
            oCityId.hide();
            oCityAreaContainer.hide();
            oRegionAreaId.show();
            
            if (oCityId.val().length > 0)
                sDefCityId = oCityId.val();
            
            if (oTownId.val() && oTownId.val().length > 0)
                sDefTownIdByCity = oTownId.val();

            oTownId.empty();
            
            oCityId.val("");
            oRegionAreaId.val(sDefRegionAreaId);
            oRegionAreaId.change();
        }
    }
    
    function fillTownByCity(sCityId, _sTownIdSelected)
    {
        oTownId.empty();
        oTownId.append('<option class="label" value="">Населенный пункт</option>');
        
        if (arTownByCity != null && arTownByCity[sCityId] != null)
        {
            oTownId.show();
            for (var townId in arTownByCity[sCityId])
            {
                var selected = townId == _sTownIdSelected?'selected="selected"':'';
                oTownId.append('<option value="' + townId + '" ' + selected + '>' + arTownByCity[sCityId][townId] + '</option>');   
            }    
        }
        else
        {
            oTownId.hide();
        }
    }
    
    function fillCityAreaByCityId(sCityId, _sCityAreaIdSelected)
    {
        oCityAreaIdContainer.empty();
        
        if (arCityAreaByCity != null && arCityAreaByCity[sCityId] != null)
        {
            oCityAreaContainer.show();
            for (var cityAreaId in arCityAreaByCity[sCityId])
            {
                oCityAreaIdContainer.append('<input class="ch" value="' + cityAreaId +'" id="' + cityAreaId +'" name="' + filterName + '[CityAreaId][]" type="checkbox">'+
                    '<label class="ch" for="' + cityAreaId + '">' + arCityAreaByCity[sCityId][cityAreaId] + '</label><br>');
            }    
        }
        else
        {
            oCityAreaContainer.hide();
        }
    }
    
    function fillTownByRegionArea(sRegionAreaId, _sTownIdSelected)
    {
        oTownId.empty();
        oTownId.append('<option class="label" value="">Населенный пункт</option>');
        
        if (arTownByRegionArea != null && arTownByRegionArea[sRegionAreaId] != null)
        {
            oTownId.show();
            for (var townId in arTownByRegionArea[sRegionAreaId])
            {
                var selected = townId == _sTownIdSelected?'selected="selected"':'';        
                
                oTownId.append('<option value="' + townId + '" ' + selected + '>' + arTownByRegionArea[sRegionAreaId][townId] + '</option>');   
            }
        }
        else
        {
            oTownId.hide();
        }
    }
    
    function PublicFilter()
    {
        this.Init = function()
        {
            initLocationHandlers();
            checkSelectStyles();
        }
        
        this.SetTownByRegionArea = function(_arTownByRegionArea)
        {
            arTownByRegionArea = _arTownByRegionArea;
        }
        
        this.SetTownByCity = function(_arTownByCity)
        {
            arTownByCity = _arTownByCity;
        }
        
        this.SetCityAreaByCity = function(_arCityAreaByCity )
        {
            arCityAreaByCity = _arCityAreaByCity ;
        }
        
        this.SetDefRegionAreaId = function(_sDefRegionAreaId)
        {
            sDefRegionAreaId = _sDefRegionAreaId;
        }
        
        this.SetDefCityId = function(_sDefCityId)
        {
            sDefCityId = _sDefCityId;
        }
        
        this.SetDefTownId = function(_sDefTownId)
        {
            sDefTownIdByCity = _sDefTownId;
            sDefTownIdByRegionArea = _sDefTownId;
        }
        
        this.SetFilterName = function(_filterName)
        {
            filterName = _filterName;
        } 
        
        this.SetTypeId = function(_sTypeId)
        {
            sTypeId = _sTypeId;
        }
        
        this.SetFilterFormName = function(_filterFormName)
        {
            filterFormName = _filterFormName;
        }   
        
        this.ClearFilter = function(submit)
        {
            var frm_elements = oFilterForm.get(0).elements;
            for (i = 0; i < frm_elements.length; i++)
            {
                field_type = frm_elements[i].type.toLowerCase();
                switch (field_type)
                {
                    case "text":
                    case "password":
                    case "textarea":
                    case "hidden":
                        frm_elements[i].value = "";
                    break;
                    case "radio":
                    case "checkbox":
                        if (frm_elements[i].checked)
                        {
                            frm_elements[i].checked = false;
                        }
                    break;
                    case "select-one":
                    case "select-multi":
                        frm_elements[i].selectedIndex = -1;
                    break;
                    default:
                    break;
                }
            }
            if (submit)
                oFilterForm.submit(); 
        }
    }
    
    return new PublicFilter();
}