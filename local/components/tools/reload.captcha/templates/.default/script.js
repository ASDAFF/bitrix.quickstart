/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

function getXmlHttp()
{
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e)
	{
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} 
		catch (E){
			xmlhttp = false;
		}	
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') 
		xmlhttp = new XMLHttpRequest();
	return xmlhttp;
}

function findParent(elem, name)
{
	if (elem.parentNode.tagName != "HTML"){
		if (elem.parentNode.name == name && elem.parentNode.tagName == 'FORM')
			return true;
		if (findParent(elem.parentNode, name))
			return true;
	}else
		return false;
}

function Bind(event){
	event = event || window.event
        var t = event.target || event.srcElement

	var capNode = '';
	var inputNode = '';
	if (!prev(prev(t)) ){ //forum & blog
		var myDiv= getMyElementsByClassName ('forum-reply-field forum-reply-field-captcha')[0];
		inputNode = first (myDiv);
	}
	else {
		inputNode = prev(prev(t));
	}
	cap_name = prev(t);

	//alert (cap_name);	
	var req = getXmlHttp();
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			if(req.status == 200) { 
				cap_name.src = "/bitrix/tools/captcha.php?"+inputNode.name+"="+req.responseText;
				inputNode.value = req.responseText;
			}
	}
	req.open('GET',pathExec+"?mode="+inputNode.name, true);
	req.send(null);
}
function prev(elem){
	do {
		elem = elem.previousSibling;
        } while (elem && elem.nodeType != 1);
        return elem;
}
function first( elem ) { 
	elem = elem.firstChild;
	return elem && elem.nodeType != 1 ? elem.nextSibling : elem;
}
function addImageObj(pNode)
{
	var pos = pNode.getElementsByTagName('IMG')[0];
	if (pos){
		var imgObj = document.createElement('img');
		imgObj.src = imgPath;
		imgObj.onmouseup = Bind;
		insertAfter(imgObj, pos);
	}
}

function getMyElementsByClassName(cl){
	var retnode = [];
	var myclass = new RegExp('\\b'+cl+'\\b');
	var elem = document.getElementsByTagName('*');
	for (var i = 0; i < elem.length; i++) {
		var classes = elem[i].className;
		if (myclass.test(classes)) 
			retnode.push(elem[i]);
	}
	return retnode;
}; 

function insertAfter(newElement,targetElement) {
		var parent = targetElement.parentNode;
		 if(parent.lastchild == targetElement)
			 parent.appendChild(newElement);
		 else
			 parent.insertBefore(newElement, targetElement.nextSibling);
}