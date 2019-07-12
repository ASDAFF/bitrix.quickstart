/************************************
*
* Universal JavaScript Extensions
* v1.0.0
* last update 28.08.2013
*
************************************/

// formating function for numbers
function RSDevFunc_NumberFormat(val,separPor,separDes)
{
	if (!separPor) separPor = ' ';
	if (!separDes) separDes = ',';
	var res = val.toString().replace(/\s/g, '');
	var minusovoe = (val < 0);
	var len = res.lastIndexOf('.');
	len = (len > -1) ? len : res.length;
	var tmp = res.substring(len);
	var count = -1;
	for(var index=len;index>0;index--)
	{
		count++;
		if ( (count%3)===0 && index!==len && ( !minusovoe || (index>1) ) )
		{
			tmp = separPor + tmp;
		}
		tmp = res.charAt(index - 1) + tmp;
	}
	return tmp.replace('.', separDes);
}

// get url vars
function RSDevFunc_GetUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

// ending of the word depending on the number
function RSDevFunc_BasketEndWord(num,end1,end2,end3)
{
	if(!end1) end1 = RSDevFunc_BasketEndWord_end1;
	if(!end2) end2 = RSDevFunc_BasketEndWord_end2;
	if(!end3) end3 = RSDevFunc_BasketEndWord_end3;
	var val = num % 100;
	if (val > 10 && val < 20)
	{
		return end3;
	} else {
		val = num % 10;
		if (val == 1)
		{
			return end1;
		} else if ((val > 1) && (val < 5))
		{
			return end2;
		} else { return end3; }
	}
}

