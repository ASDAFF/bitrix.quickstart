
function print_r( arr, level, maxlevel )
{
    var print_red_text = '';
    if( (level == undefined) || (level == null) )
    {
        level = 0;
        maxlevel = 1;
    }
    if( level >= maxlevel )
        return '';
    var level_padding = '';
    for(var j=0; j<level+1; j++)
        level_padding += '    ';
    if( typeof(arr) == 'object' )
    {
        for(var item in arr)
        {
            var value = arr[item];
            if(typeof(value) == 'object')
            {
                print_red_text += level_padding + "'" + item + "' :\n";
                print_red_text += print_r( value, level+1, maxlevel ); // Warning! recursion!
            }
            else 
                print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
        }
    }
    else if( typeof(arr) == 'undefined' ) {
        return '';
    }

    else  print_red_text = "===>"+arr+"<===("+typeof(arr)+")";
    return print_red_text;
}
