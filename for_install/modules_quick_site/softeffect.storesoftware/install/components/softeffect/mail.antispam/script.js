function antispan(params) {
	// собираем обратно email
	mail = params['name']+'@'+params['domen']+'.'+params['zone'];
	obj = document.getElementById(params['id']);
	obj.innerHTML = mail;
	obj.removeAttribute('id');
	
	// для ссылки ставим mailto:
	if (obj.getAttribute('href')!=null) {
		obj.setAttribute('href', 'mailto:'+mail);
	}
}