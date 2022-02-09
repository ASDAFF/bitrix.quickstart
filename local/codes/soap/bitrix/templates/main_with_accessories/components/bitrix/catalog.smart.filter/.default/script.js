function JCSmartFilter(ajaxURL)
{
    this.ajaxURL = ajaxURL;
    this.form = null;
    this.timer = null;
}

JCSmartFilter.prototype.keyup = function(input)
{
    if(this.timer)
        clearTimeout(this.timer);
    this.timer = setTimeout(BX.delegate(function(){
                this.reload(input);
            }, this), 1000);
}

JCSmartFilter.prototype.click = function(checkbox)
{
    if(this.timer)
        clearTimeout(this.timer);
    this.timer = setTimeout(BX.delegate(function(){
                this.reload(checkbox);
            }, this), 1000);

}

JCSmartFilter.prototype.reload = function(input)
{
    this.position = BX.pos(input, true);
    this.form = BX.findParent(input, {'tag':'form'});
    if(this.form)
        {
        var values = new Array;
       // values[0] = {name: 'ajax', value: 'y'};
        this.gatherInputsValues(values, BX.findChildren(this.form, {'tag':'input'}, true));
        window.input = input;
        BX.ajax.loadJSON(
            this.ajaxURL,
            this.values2post(values),
            BX.delegate(this.postHandler, this)
        );
        // document._form.submit();
    }
}

JCSmartFilter.prototype.postHandler = function (result)
{
    if(result.ITEMS)
        {
        for(var PID in result.ITEMS)
        {
            var arItem = result.ITEMS[PID];
            
            if(arItem.PROPERTY_TYPE == 'N' || arItem.PRICE)
                {
            }
            else if(arItem.VALUES)
                {
                
                for(var i in arItem.VALUES)
                {
                    var ar = arItem.VALUES[i];
                    var control = BX(ar.CONTROL_ID);
                    if(control)
                        {
                        //alert(ar);
                        control.parentNode.className = ar.DISABLED? 'b-checkbox2 b-checked': 'b-checkbox';
                        control.parentNode.className = ar.CHECKED? 'b-checkbox b-checked': 'b-checkbox';
                    }
                }
            }
        }
        var modef = BX('modef');
        var modef_num = BX('modef_num');
        if(modef && modef_num)
            {
            modef_num.innerHTML = result.ELEMENT_COUNT;
            var hrefFILTER = BX.findChildren(modef, {tag: 'A'}, true);
            if(result.FILTER_URL && hrefFILTER)
                hrefFILTER[0].href = BX.util.htmlspecialcharsback(result.FILTER_URL);
            curProp = BX.findParent(window.input, {'class':'cnt'});
            var PropCode = $(curProp).attr("id");

            if(modef.style.display == 'none')
                modef.style.display = 'block';
            $("."+PropCode).prepend(modef);
        }

    }
}

JCSmartFilter.prototype.gatherInputsValues = function (values, elements)
{
    if(elements)
        {
        for(var i = 0; i < elements.length; i++)
        {
            var el = elements[i];
            if (el.disabled || !el.type)
                continue;

            switch(el.type.toLowerCase())
            {
                case 'text':
                case 'textarea':
                case 'password':
                case 'hidden':
                case 'select-one':
                    if(el.value.length)
                        values[values.length] = {name : el.name, value : el.value};
                    break;
                case 'radio':
                case 'checkbox':
                    if(el.checked)
                        values[values.length] = {name : el.name, value : el.value};
                    break;
                case 'select-multiple':
                for (var j = 0; j < el.options.length; j++)
                {
                    if (el.options[j].selected)
                        values[values.length] = {name : el.name, value : el.options[j].value};
                }
                break;
                default:
                    break;
            }
        }
    }
}

JCSmartFilter.prototype.values2post = function (values)
{
    var post = new Array;
    var current = post;
    var i = 0;
    while(i < values.length)
    {
        var p = values[i].name.indexOf('[');
        if(p == -1)
            {
            current[values[i].name] = values[i].value;
            current = post;
            i++;
        }
        else
            {
            var name = values[i].name.substring(0, p);
            var rest = values[i].name.substring(p+1);
            if(!current[name])
                current[name] = new Array;

            var pp = rest.indexOf(']');
            if(pp == -1)
                {
                //Error - not balanced brackets
                current = post;
                i++;
            }
            else if(pp == 0)
                {
                //No index specified - so take the next integer
                current = current[name];
                values[i].name = '' + current.length;
            }
            else
                {
                //Now index name becomes and name and we go deeper into the array
                current = current[name];
                values[i].name = rest.substring(0, pp) + rest.substring(pp+1);
            }
        }
    }
    return post;
}
