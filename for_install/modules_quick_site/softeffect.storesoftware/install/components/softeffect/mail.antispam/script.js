function antispan(params) {
	// �������� ������� email
	mail = params['name']+'@'+params['domen']+'.'+params['zone'];
	obj = document.getElementById(params['id']);
	obj.innerHTML = mail;
	obj.removeAttribute('id');
	
	// ��� ������ ������ mailto:
	if (obj.getAttribute('href')!=null) {
		obj.setAttribute('href', 'mailto:'+mail);
	}
}