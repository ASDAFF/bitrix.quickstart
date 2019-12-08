/************************************
*
* Universal JavaScript Extensions
* v1.3.1b
* last update 16.06.2014
*
************************************/

// formating function for numbers
function RSDevFunc_NumberFormat(number,decimals,dec_point,thousands_sep){
	var i, j, kw, kd, km;
	if(isNaN(decimals = Math.abs(decimals))){decimals = 2;}
	if(dec_point == undefined){dec_point = ".";}
	if(thousands_sep == undefined){thousands_sep = " ";}
	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";
	if( (j = i.length) > 3 ){j = j % 3;}else{j = 0;}
	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");
	return km + kw + kd;
}

// get url vars
function RSDevFunc_GetUrlVars(){
    var vars = [],hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i=0;i<hashes.length;i++){
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

// ending of the word depending on the number
function RSDevFunc_BasketEndWord(num,end1,end2,end3){
	if(!end1) end1 = RSDevFunc_BasketEndWord_end1;
	if(!end2) end2 = RSDevFunc_BasketEndWord_end2;
	if(!end3) end3 = RSDevFunc_BasketEndWord_end3;
	var val = num % 100;
	if (val>10 && val<20){
		return end3;
	}else{
		val = num % 10;
		if (val == 1){
			return end1;
		}else if((val > 1) && (val < 5)){
			return end2;
		}else{return end3;}
	}
}

var RSDevFunc_PHONETABLET = false;
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|Windows Phone/i.test(navigator.userAgent) ) { RSDevFunc_PHONETABLET = true };