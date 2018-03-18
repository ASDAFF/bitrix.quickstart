/*
wwww.tigir.com - 06.07.2006

Source: http://www.tigir.com/js/opacity.js

Библиотека opacity.js к статье "CSS прозрачность (css opacity, javascript opacity)" - http://www.tigir.com/opacity.htm

setElementOpacity - установка прозрачности
getOpacityProperty - проверка, есть ли возможность менять прозрачность
fadeOpacity - плавное изменение прозрачности
*/

/* Функция кроссбраузерной установки прозрачности

Пример: setElementOpacity(document.body, 0.5); //сделать документ прозрачным на половину
*/
function setElementOpacity(oElem, nOpacity)
{
	var p = getOpacityProperty();
	(setElementOpacity = p=="filter"?new Function('oElem', 'nOpacity', 'nOpacity *= 100;	var oAlpha = oElem.filters["DXImageTransform.Microsoft.alpha"] || oElem.filters.alpha;	if (oAlpha) oAlpha.opacity = nOpacity; else oElem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")";'):p?new Function('oElem', 'nOpacity', 'oElem.style.'+p+' = nOpacity;'):new Function)(oElem, nOpacity);
}

// Функция getOpacityProperty() возвращает свойство которое используется для смены прозрачности или undefined, и может использоваться для проверки возможности изменения прозрачности
function getOpacityProperty()
{
	var p;
	if (typeof document.body.style.opacity == 'string') p = 'opacity';
	else if (typeof document.body.style.MozOpacity == 'string') p =  'MozOpacity';
	else if (typeof document.body.style.KhtmlOpacity == 'string') p =  'KhtmlOpacity';
	else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) p =  'filter';

	return (getOpacityProperty = new Function("return '"+p+"';"))();
}

/* Функции для плавного изменения прозрачности:

1) fadeOpacity.addRule('opacityRule1', 1, 0.5, 30); //вначале создаем правило, задаем имя правила, начальную прозрачность и конечную, необязательный параметр задержки, влийяющий на скорость смены прозрачности
2) fadeOpacity('elemID', 'opacityRule1'); // выполнить плавную смену прозрачности элемента с id равным elemID, по правилу opacityRule1
3) fadeOpacity.back('elemID'); //вернуться в исходное сотояние прозрачности
*/
function fadeOpacity(sElemId, sRuleName, bBackward)
{
	var elem = document.getElementById(sElemId);
	if (!elem || !getOpacityProperty() || !fadeOpacity.aRules[sRuleName]) return;

	var rule = fadeOpacity.aRules[sRuleName];
	var nOpacity = rule.nStartOpacity;

	if (fadeOpacity.aProc[sElemId]) {clearInterval(fadeOpacity.aProc[sElemId].tId); nOpacity = fadeOpacity.aProc[sElemId].nOpacity;}
	if ((nOpacity==rule.nStartOpacity && bBackward) || (nOpacity==rule.nFinishOpacity && !bBackward)) return;

	fadeOpacity.aProc[sElemId] = {'nOpacity':nOpacity, 'tId':setInterval('fadeOpacity.run("'+sElemId+'")', fadeOpacity.aRules[sRuleName].nDalay), 'sRuleName':sRuleName, 'bBackward':Boolean(bBackward)};
}

fadeOpacity.addRule = function(sRuleName, nStartOpacity, nFinishOpacity, nDalay){fadeOpacity.aRules[sRuleName]={'nStartOpacity':nStartOpacity, 'nFinishOpacity':nFinishOpacity, 'nDalay':(nDalay || 30),'nDSign':(nFinishOpacity-nStartOpacity > 0?1:-1)};};

fadeOpacity.back = function(sElemId){fadeOpacity(sElemId,fadeOpacity.aProc[sElemId].sRuleName,true);};

fadeOpacity.run = function(sElemId)
{
	var proc = fadeOpacity.aProc[sElemId];
	var rule = fadeOpacity.aRules[proc.sRuleName];

	proc.nOpacity = Math.round(( proc.nOpacity + .1*rule.nDSign*(proc.bBackward?-1:1) )*10)/10;
	setElementOpacity(document.getElementById(sElemId), proc.nOpacity);

	if (proc.nOpacity==rule.nStartOpacity || proc.nOpacity==rule.nFinishOpacity) clearInterval(fadeOpacity.aProc[sElemId].tId);
}
fadeOpacity.aProc = {};
fadeOpacity.aRules = {};
