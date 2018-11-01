document.addEventListener('DOMContentLoaded', function(){	                        		
	document.getElementById('authorization-btn').click();
});

document.addEventListener('DOMContentLoaded', function(){
	document.getElementById('oneclickelement').onclick = function(){		
		paramsToModal.buyOneClick($(this)); 
	};
});

document.addEventListener("DOMContentLoaded", function (event) {     	
	elemsCollection = document.getElementsByClassName('buy-offers');
	for( var i=0; i < elemsCollection.length; i++  ){
	  elemsCollection[i].value = "1";		 
	}
});

//сортировка option по значению
$('.selected_box_RAZMER').each(function(){  
	  selectOptions = $(this).find('option');
	  selectOptions.sort(function(a,b){
	      a = a.value;
	      b = b.value;
	
	      return a-b;
	  });
	  $(this).html(selectOptions);
});

//удаления дубликатов
$("select option").val(function(i,v){
  $(this).siblings("[value="+ v +"]").remove();
});

//удаления дубликатов + замена 1 пробела на _
$("select option").val(function(i,v){
  $(this).siblings("[value="+ v.replace(/\s{1,}/g, '_') +"]").remove();
});

//обновления параметров URL
function UpdateQueryString(key, value, url) {
    if (!url) url = window.location.href;
    var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
        hash;

    if (re.test(url)) {
        if (typeof value !== 'undefined' && value !== null)
            return url.replace(re, '$1' + key + "=" + value + '$2$3');
        else {
            hash = url.split('#');
            url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
            if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
                url += '#' + hash[1];
            return url;
        }
    }
    else {
        if (typeof value !== 'undefined' && value !== null) {
            var separator = url.indexOf('?') !== -1 ? '&' : '?';
            hash = url.split('#');
            url = hash[0] + separator + key + '=' + value;
            if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
                url += '#' + hash[1];
            return url;
        }
        else
            return url;
    }
}


//удаление параметров с url
function delPrm(Url,Prm) 
{
	var a=Url.split('?');
	var re = new RegExp('(\\?|&)'+Prm+'=[^&]+','g');
	Url=('?'+a[1]).replace(re,'');
	Url=Url.replace(/^&|\?/,'');
	var dlm=(Url=='')? '': '?';
	return a[0]+dlm+Url;
};
