function scroll_up_page()
{
        var ua = navigator.userAgent.toLowerCase();
          if (navigator.userAgent.indexOf ("MSIE 9")== -1 && navigator.userAgent.indexOf ("MSIE 8")== -1 && navigator.userAgent.indexOf ("MSIE 7")== -1 && navigator.userAgent.indexOf ("MSIE 6") !=-1 && ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1 && ua.indexOf("webtv") == -1) {
           if(altasib_up_pos == 1)
           pos = "top: expression(eval(document.documentElement.scrollTop || document.body.scrollTop)+"+altasib_up_pos_xy+"+'px'); right: "+altasib_up_pos_xy+"px;";
        else if(altasib_up_pos == 2)
           pos = "top: expression(eval(document.documentElement.scrollTop || document.body.scrollTop) +(document.documentElement.clientHeight || document.body.clientHeight ) - 38 - "+altasib_up_pos_xy+"+'px'); left: "+altasib_up_pos_xy+"px;";
        else if(altasib_up_pos == 3)
           pos = "top: expression(eval(document.documentElement.scrollTop || document.body.scrollTop) +(document.documentElement.clientHeight || document.body.clientHeight ) - 38 - "+altasib_up_pos_xy+"+'px'); right: "+altasib_up_pos_xy+"px;";
        else if(altasib_up_pos == 4)
           pos = "top: expression(eval(document.documentElement.scrollTop || document.body.scrollTop) +(document.documentElement.clientHeight || document.body.clientHeight ) - 38 - "+altasib_up_pos_xy+"+'px'); left: 50%";
        else
           pos = "top: expression(eval(document.documentElement.scrollTop || document.body.scrollTop)+"+altasib_up_pos_xy+"+'px'); left: "+altasib_up_pos_xy+"px;";
          }
        else{

        if(altasib_up_pos == 1)
           pos = "top: "+altasib_up_pos_xy+"px; right: "+altasib_up_pos_xy+"px;";
        else if(altasib_up_pos == 2)
           pos = "bottom: "+altasib_up_pos_xy+"px; left: "+altasib_up_pos_xy+"px;";
        else if(altasib_up_pos == 3)
           pos = "bottom: "+altasib_up_pos_xy+"px; right: "+altasib_up_pos_xy+"px;";
        else if(altasib_up_pos == 4)
           pos = "bottom: "+altasib_up_pos_xy+"px; left: 50%";
        else
           pos = "top: "+altasib_up_pos_xy+"px; left: "+altasib_up_pos_xy+"px;";
        }
        var newDiv = document.createElement('div')
        newDiv.className="up_page_block";
        newDiv.innerHTML = '<div class="alx_up_page_button" id="alx_up_page_button" style="'+pos+'" onclick="click_button_up()"><img src="'+altasib_up_button+'" alt="Up!" /></div>';
        body_id = document.getElementsByTagName('BODY')[0];
        //if(!body_id)alert("Error! No tag 'body' on this page!");
        body_id.appendChild(newDiv);
        if(body_id.currentStyle) {
               bg_body = body_id.currentStyle.backgroundImage;

        }
        else
        {
                bg_body = getComputedStyle(body_id,'').getPropertyValue('background-image')
        }
        if(bg_body= 'none')
        {
                 body_id.style.backgroundImage = 'url(/bitrix/images/altasib.up/spacer.gif)';
        }
       document.getElementById("alx_up_page_button").style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=80)"
        scroll_top_page = self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
        if(scroll_top_page>0)
        {
                document.getElementById("alx_up_page_button").style.display='block';
        }
        window.onscroll = function(){
        scroll_top_page = self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
                if(scroll_top_page>0)
                {
                        document.getElementById("alx_up_page_button").style.display='block';
                }
                else
                {
                        document.getElementById("alx_up_page_button").style.display='none';
                }
        };
}
function click_button_up()
{
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
}
window.onload = scroll_up_page;
