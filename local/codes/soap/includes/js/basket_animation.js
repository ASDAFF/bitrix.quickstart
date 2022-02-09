 $(document).ready(
        function(){
            buy_btns = $('a[href*="ADD2BASKET"]');
            buy_btns.each(
                function(){
                    $(this).attr("rel", $(this).attr("href"));
                }
            );
            buy_btns.attr("href","javascript:void(0);");
            function getBasketHTML(html)
            {
                txt = html.split('<!--start--><div id="bid">');
                txt = txt[2];
                txt = txt.split('</div><!--end-->');
                txt = txt[0];
                return txt;
            }

            $('a[rel*="ADD2BASKET"]').click(                
                function(){
                var imgid = $(this).attr("id");
                    $.ajax({
                      type: "GET",
                      url: $(this).attr("rel"),
                      dataType: "html",
                      success: function(out){

                                $("#bid").html(getBasketHTML(out));
    var imageElement =  document.getElementById(imgid);
    var imageToFly = $(imageElement);
    var position = imageToFly.position();
    var flyImage = imageToFly.clone().insertBefore(imageToFly);
    var basketposition = $("div .b-cart-mini").position();

    flyImage.css({ "position": "absolute", "left": position.left, "top": position.top });
    flyImage.animate({ width: 0, height: 0, left: basketposition.left, top: basketposition.top}, 1000, 'linear');
 
       function myfunc(id)
         {
          imageElement = document.getElementById(id);
          imageElement.parentNode ? imageElement.parentNode.removeChild(imageElement) : imageElement;
         }
 
       setTimeout(function() {myfunc(imageToFlyId)}, timeout);
 
    return false;
                      }

                    });
                }
            );
            
        }
    );
