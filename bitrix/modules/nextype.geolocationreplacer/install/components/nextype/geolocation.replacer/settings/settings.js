var Base64 = {


    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",


    encode: function(input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },


    decode: function(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    _utf8_encode: function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    _utf8_decode: function(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while (i < utftext.length) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}

function TableToJSON(table) {
    var rawObj = {}, obj = {}, counter = 0;
    table.find('.city input').each(function (index) {
        rawObj[index] = {
            city: $(this).val(),
            region: '',
            text: ''
        };
    });
    
    table.find('.region input').each(function (index) {
        rawObj[index]['region'] = $(this).val();
    });
    
    table.find('.text textarea').each(function (index) {
        rawObj[index]['text'] = $(this).val();
    });
    
    $.each(rawObj, function (index, value) {
        if (value['city'] != "" && value['text'] != "") {
            obj[counter] = value;
            counter++;
        }
    });
    
    return Base64.encode(JSON.stringify(obj));
}

function JSONToTable(data) {

    var obj = {};
    if (data === undefined || data == "") {
        for (var i = 0; i < 3; i++) {
            obj[i] = {
                city: '',
                region: '',
                text: ''
            };
        }
    } else {
        var counter = 0;
        data = JSON.parse(data);
        
        $.each(data, function (index, value) {
            obj[counter] = {
                city: value.city,
                region: value.region,
                text: value.text
            };
            counter++;
        });
        
        obj[counter] = {
            city: '',
            region: '',
            text: ''
        };
    }

    return obj;
}

function NextypeGeolocationDeleteRow(el) {
    $(el).parents('tr.row_item').remove();
}

function OnNextypeGeolocationStart(arParams) {
  this.arParams = arParams;
   var wrap = $(this.arParams.oCont).parents('.bxcompprop-prop-tr'); 
   var bxwrap = $(this.arParams.oCont).parents('.bxcompprop-wrap');
   
   // Hide aside menu
   bxwrap.find('.bxcompprop-left').hide();
   bxwrap.find('.bxcompprop-right').css({
       width: '95%',
       float: 'none',
       left: '0px'
   });
   
   var tr = $(this.arParams.oCont).parent();
   tr.find('td').remove().append('<td colspan="2"></td>');
   
   // Header
   var  table = '<table width="100%" id="NextypeGeolocationTable">';
        table += '<tr class="header-geolocation">';
        table += '<td width="20%" class="bxcompprop-cont-table-title"><b>&#1043;&#1086;&#1088;&#1086;&#1076;</b></td>';
        table += '<td width="20%" class="bxcompprop-cont-table-title"><b>&#1056;&#1077;&#1075;&#1080;&#1086;&#1085;</b></td>';
        table += '<td width="55%" class="bxcompprop-cont-table-title"><b>&#1058;&#1077;&#1082;&#1089;&#1090;</b></td>';
        table += '<td class="bxcompprop-cont-table-title"></td>';
        table += '</tr>';
        table += '</table>';
   
   
   tr.html("<td colspan='2'>"+table+"</td>");
   table = tr.find('table');
   var input = $('<input type="hidden" name="CITIES" data-bx-property-id="CITIES" data-bx-comp-prop="true" value="" />');
   table.append(input);
   
   var obj = JSONToTable(Base64.decode(arParams.oInput.defaultValue));

   $.each(obj, function (index, value) {
       var  tr = '<tr class="row_item">';
            tr += '<td class="city"><input style="width:90%;" value="'+value.city+'" type="text" /></td>';
            tr += '<td class="region"><input style="width:90%;" value="'+value.region+'" type="text" /></td>';
            tr += '<td class="text"><textarea style="width:98%;">'+value.text+'</textarea></td>';
            tr += '<td class="delete"><input type="button" value="x" /></td>';
            tr += '</tr>';
       table.append(tr);
   });
   
   var trClone = table.find('tr:last-child').clone();
   var trAddButton = $('<input type="button" value="+ &#1044;&#1086;&#1073;&#1072;&#1074;&#1080;&#1090;&#1100; &#1089;&#1090;&#1088;&#1086;&#1082;&#1091;" />');
   tr.find('>td').append(trAddButton);
   
   $("body").on('click', '#NextypeGeolocationTable .delete input', function (event) {
       event.preventDefault();
        $(this).parents('tr.row_item').remove();
        
        input.val(TableToJSON(table));
        return false;
   });
   
   $.getScript("https://kladr-api.ru/jsplugin/jquery.kladr.min.js", function () {
       table.find('.city input').kladr({
            type: $.kladr.type.city,
            withParents: true,
            select: function (obj) {
                if (typeof obj.parents == 'object' && typeof obj.parents[0] == 'object') {
                    $(this).parents('tr.row_item').find('.region input').val(obj.parents[0].name + ' ' + obj.parents[0].type);
                }
                
            }
       });
       
       table.find('.region input').kladr({
            type: $.kladr.type.region,
            valueFormat: function (obj, query) {
                return obj.name + ' ' + obj.type;
            },
       });
   });
   
   
   
   trAddButton.on('click', function (event) {
       event.preventDefault();

       var clone = trClone.clone();
       table.append(clone);
       clone.find('.city input').kladr({
            type: $.kladr.type.city,
            withParents: true,
            select: function (obj) {
                if (typeof obj.parents == 'object' && typeof obj.parents[0] == 'object') {
                    $(this).parents('tr.row_item').find('.region input').val(obj.parents[0].name + ' ' + obj.parents[0].type);
                }
            }
       });
       
       clone.find('.region input').kladr({
            type: $.kladr.type.region,
            valueFormat: function (obj, query) {
                return obj.name + ' ' + obj.type;
            },
       });
       return false;
   });
   
   $("body").on('keyup', '#NextypeGeolocationTable input[type=text], #NextypeGeolocationTable textarea', function (event) {
        var base64 = TableToJSON(table);
        console.log(base64);
        input.val(base64);
   });
   
   
   input.val(TableToJSON(table));
}

function OnNextypeGeolocationEdit(arParams) {
    
    if(!window.jQuery){
      var script = document.createElement('script');
      script.src = "//yastatic.net/jquery/2.2.3/jquery.min.js";
      document.body.appendChild(script);
      
      script.onload = function() {
        OnNextypeGeolocationStart.call(this, arParams);
      };

    }
    else
    {
      OnNextypeGeolocationStart.call(this, arParams);
    }

    
   
   
}