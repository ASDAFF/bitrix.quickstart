// Returns PHP-style serialized array/object
function serialize_php( obj )
{
    if( typeof(obj) == 'object' )
    {
        var str = '';
        var cnt = 0;
        for( var i in obj )
        {
            // if( __checkValidKey(i) ) // todo?
            ++cnt;
            str += serialize_php( i ) + serialize_php( obj[i] );
        }
        str = "a:" + cnt + ":{" + str + "}";
        return str;
    }
    else if( typeof(obj) == 'boolean' )
    {
        return 'b:' + (obj ? 1 : 0) + ';';
    }
    else if( null == obj )
    {
        return 'N;'
    }
    else if( typeof(obj) == 'number' )
    {
        //if( (Number(obj) == obj) && (obj != '') && (obj != ' '))
        //{
            if( Math.floor(obj) == obj )
                return 'i:' + obj + ';';
            else
                return 'd:' + obj + ';';
        //}
    }
    else if( typeof(obj) == 'string' )
    {
        obj = obj.replace(/\r\n/g, "\n");
        obj = obj.replace(/\n/g, "###RN###");

        var offset = 0;
        //if (window._global_BX_UTF) // unicode mode
        //{
            for( var q = 0, cnt = obj.length; q < cnt; q++ )
            {
                if( obj.charCodeAt(q) > 127 )
                    offset++;
            }
        //}

        return 's:' + (obj.length + offset) + ':"' + obj + '";';
    }
    else if( typeof(obj) == 'undefined' )
    {
        return '';
    }
    // what...?
    return '';
}
