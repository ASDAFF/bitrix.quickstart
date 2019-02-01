
/* Данная функция создаёт кроссбраузерный объект XMLHTTP */
function getXmlHttp() {
    var xmlhttp;
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
      xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

function sendDataHighLoadBlock(idProduct, idUser, postPath){	
	var xmlhttp = getXmlHttp();
	
	xmlhttp.open('POST', postPath, true); 
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
	postUrl = 'idProduct='+ idProduct 
			 +'&idUser=' + idUser;
	xmlhttp.send( postUrl );
	xmlhttp.onreadystatechange = function() { 
      if (xmlhttp.readyState == 4) { 
        if(xmlhttp.status == 200) {         
        	console.log('Данные успешно отправлены!');       
        }else{
        	console.log('В процессе отправки произошла ошибка');
        }
      }
    };
	
}

function DelWishProduct(idProduct, idUser, postPath){
	var xmlhttp = getXmlHttp();
	
	xmlhttp.open('POST', postPath, true); 
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
	postUrl = 'idProduct='+ idProduct 
			 +'&idUser=' + idUser;
	xmlhttp.send( postUrl );
	xmlhttp.onreadystatechange = function() { 
      if (xmlhttp.readyState == 4) { 
        if(xmlhttp.status == 200) {         
        	console.log('Данные успешно отправлены для удаления!');       
        }else{
        	console.log('В процессе отправки произошла ошибка');
        }
      }
    };
    	
}



	
var elCollection = document.getElementsByClassName('delElementSmallBasket');
for(var i = 0; i < elCollection .length; i++ ){
	elCollection[i].onclick = function(event){
		var productBasketID = event.target.getAttribute('data-prodbasketid');			
		var xmlhttp = getXmlHttp();
		var postPath = '/bitrix/ajax/ajax_del_product.php';
		
		xmlhttp.open('POST', postPath, true); 
		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
		postUrl = 'productBasketID='+ productBasketID;
				
						
		xmlhttp.send( postUrl );
		xmlhttp.onreadystatechange = function() { 
	      if (xmlhttp.readyState == 4) { 
	        if(xmlhttp.status == 200) {         
	        	if( xmlhttp.response == 'OK' ){
	        		console.log('Данные успешно отправлены для удаления!');      
	        		event.target.parentElement.parentElement.style.display="none";	
	        	}		        	
	        }else{
	        	console.log('В процессе отправки произошла ошибка');
	        }
	      }
	    };
	}
}
