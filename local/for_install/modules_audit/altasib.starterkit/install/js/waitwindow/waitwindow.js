jQuery.fn.showWaitWindow = function(options)
{
        var options = jQuery.extend({Class:'alx_waitwindowlocalshadow',MinHeight:0,MinWidth:0},options);
        return this.each(function()
        {
                var w = jQuery(this).width();
                var h = jQuery(this).height();

                function GetWindowSize()
                {
                        var innerWidth, innerHeight;

                        if (self.innerHeight) // all except Explorer
                        {
                                innerWidth = self.innerWidth;
                                innerHeight = self.innerHeight;
                        }
                        else if (document.documentElement && document.documentElement.clientHeight) // Explorer 6 Strict Mode
                        {
                                innerWidth = document.documentElement.clientWidth;
                                innerHeight = document.documentElement.clientHeight;
                        }
                        else if (document.body) // other Explorers
                        {
                                innerWidth = document.body.clientWidth;
                                innerHeight = document.body.clientHeight;
                        }

                        var scrollLeft, scrollTop;
                        if (self.pageYOffset) // all except Explorer
                        {
                                scrollLeft = self.pageXOffset;
                                scrollTop = self.pageYOffset;
                        }
                        else if (document.documentElement && document.documentElement.scrollTop) // Explorer 6 Strict
                        {
                                scrollLeft = document.documentElement.scrollLeft;
                                scrollTop = document.documentElement.scrollTop;
                        }
                        else if (document.body) // all other Explorers
                        {
                                scrollLeft = document.body.scrollLeft;
                                scrollTop = document.body.scrollTop;
                        }

                        var scrollWidth, scrollHeight;

                        if ( (document.compatMode && document.compatMode == "CSS1Compat"))
                        {
                                scrollWidth = document.documentElement.scrollWidth;
                                scrollHeight = document.documentElement.scrollHeight;
                        }
                        else
                        {
                                if (document.body.scrollHeight > document.body.offsetHeight)
                                        scrollHeight = document.body.scrollHeight;
                                else
                                        scrollHeight = document.body.offsetHeight;

                                if (document.body.scrollWidth > document.body.offsetWidth ||
                                        (document.compatMode && document.compatMode == "BackCompat") ||
                                        (document.documentElement && !document.documentElement.clientWidth)
                                )
                                        scrollWidth = document.body.scrollWidth;
                                else
                                        scrollWidth = document.body.offsetWidth;
                        }

                        return  {"innerWidth" : innerWidth, "innerHeight" : innerHeight, "scrollLeft" : scrollLeft, "scrollTop" : scrollTop, "scrollWidth" : scrollWidth, "scrollHeight" : scrollHeight};
                };

                function GetRealPos(el)
                {

                        if (el.getBoundingClientRect)
                        {
                                var obRect = el.getBoundingClientRect();
                                var obWndSize = GetWindowSize();
                                var arPos = {
                                        left: obRect.left + obWndSize.scrollLeft,
                                        top: obRect.top + obWndSize.scrollTop,
                                        right: obRect.right + obWndSize.scrollLeft,
                                        bottom: obRect.bottom + obWndSize.scrollTop
                                };
                                return arPos;
                        }


                        var res = Array();

                        res["left"] = el.offsetLeft;
                        res["top"] = el.offsetTop;
                        var objParent = el.offsetParent;

                        while(objParent && objParent.tagName != "BODY")
                        {
                                res["left"] += objParent.offsetLeft;
                                res["top"] += objParent.offsetTop;
                                objParent = objParent.offsetParent;
                        }
                        return res;
                };

                arPosition = GetRealPos(this);


                if(w < options.MinWidth)w = options.MinWidth;
                if(h < options.MinHeight)h = options.MinHeight;

                jQuery("body").append("<div id='ajaxLoader_"+this.id+"' style='display:none; top:"+arPosition["top"]+"px; left:"+arPosition["left"]+"px; width:"+w+"px;height:"+h+"px; position:absolute; z-index:1000;' class='"+options.Class+"'></div>");
                $("#ajaxLoader_"+this.id).css("display","block");

        });
}

jQuery.fn.closeWaitWindow = function()
{
        return this.each(function()
        {
                $('#ajaxLoader_'+this.id).remove();
        });
}

jQuery.fn.ajaxLoader = function(options)
{
        var options = jQuery.extend({Class:'NONE',MinHeight:0,MinWidth:0},options);
        return this.each(function()
        {
                jQuery(this).ajaxStart(function()
                {

                        function RealPosition(obj)
                        {
                                var l = 0;
                                var t = 0;
                                while (obj)
                                {
                                        l+=obj.offsetLeft;
                                        t+=obj.offsetTop;
                                        obj=obj.offsetParent;
                                }
                                return {"left":l, "top":t};
                        }
                        var pos=RealPosition(this);
                        var w=jQuery(this).width();
                        var h=jQuery(this).height();
                        if(w<options.MinWidth)w=options.MinWidth;
                        if(h<options.MinHeight)h=options.MinHeight;
                        var t=pos.top;
                        var l=pos.left;
                        jQuery("body").append("<div id='ajaxLoader"+this.id+"' style='width:"+w+"px;height:"+h+"px;position:absolute;top:"+t+"px;left:"+l+"px;z-index:1000;' class="+options.Class+"></div>");
                });
                jQuery(this).ajaxStop(function()
                {
                        $('#ajaxLoader'+this.id).remove();
                });
        });
}
// $Id: textarea.js,v 1.11.2.1 2007/04/18 02:41:19 drumm Exp $

resizeArea = function()
{

        $('textarea.resizable:not(.processed)').each(function()
        {
                var textarea = $(this).addClass('processed'), staticOffset = null;

                // When wrapping the text area, work around an IE margin bug.  See:
                // http://jaspan.com/ie-inherited-margin-bug-form-elements-and-haslayout
                $(this).wrap('<div class="resizable-textarea"><span></span></div>').parent().append($('<div class="grippie"></div>').mousedown(startDrag));

                var grippie = $('div.grippie', $(this).parent())[0];
                grippie.style.marginRight = (grippie.offsetWidth - $(this)[0].offsetWidth) +'px';

                function startDrag(e)
                {
                        staticOffset = textarea.height() - mousePosition(e).y;
                        textarea.css('opacity', 0.25);
                        $(document).mousemove(performDrag).mouseup(endDrag);
                        return false;
                }

                function performDrag(e)
                {
                        textarea.height(Math.max(32, staticOffset + mousePosition(e).y) + 'px');
                        return false;
                }

                function mousePosition(e)
                {
                        return { x: e.clientX + document.documentElement.scrollLeft, y: e.clientY + document.documentElement.scrollTop };
                }

                function endDrag(e)
                {
                        $(document).unbind('mousemove', performDrag);
                        $(document).unbind('mouseup', endDrag);
                        textarea.css('opacity', 1);
                }
        });

}