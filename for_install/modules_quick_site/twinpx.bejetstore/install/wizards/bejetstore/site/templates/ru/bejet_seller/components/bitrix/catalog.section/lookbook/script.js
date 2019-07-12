(function($) {
	$(function() {
  
    var marginTop = 20;
    var imgSize = $( '.bj-lookbook img' ).length;
    var imgCounter = 0;
    
    $( '.bj-lookbook img' ).each( function() {
      var $img = $( this );
      if ( $img[0].complete ) {
        imgShow( $img );
        if ( ++imgCounter === imgSize ) {
          appendGreyBlock();
        }
      } else {
        $img.load( function() {
          imgShow( $img );
          if ( ++imgCounter === imgSize ) {
            appendGreyBlock();
          }
        });
      }
      //if($.browser.msie === true && ($.browser.version === "9.0" || $.browser.version === "10.0")) $img.attr({src: $img.attr("src")});//for IE9, 10
      
      
    });
    
    function imgShow( $img ) {
       $img.addClass( 'i-show' );
    }
    
    cutH();
    
    $( window ).resize( function() {
      $( '.bj-lookbook__after' ).remove();
      appendGreyBlock();
      cutH();
    });
    
    function appendGreyBlock() {
      $( '.bj-lookbook__col' ).each( function() {
        var $col = $( this );
        var dif = $col.parent().height() - $col.height();
        
        if ( dif > marginTop ) {
          $col.append( '<div class="bj-lookbook__after" style="height: ' + dif + 'px;"></div>' );
        }
      });
    }
    
    function cutH() {
      $( '.bj-lookbook__cover h2' ).each( function() {
        var $h2 = $( this );
        
        if ( !$h2.data( 'heading' )) {
          $h2.data( 'heading', $h2.text());
        }
        
        setTimeout( function() {
          cutLine({
            text: $h2.data( 'heading' ),
            height: 54,
            $container: $h2
          })
        }, 100);
      });
    }
    
    function cutLine(data) {
      /*data = {
        text: "text",
        height: $bg.width(),
        $container: $container
      }*/
      
      data.$container.text( data.text );
      
      while( data.text.length > 3 && data.$container.height() > data.height ) {
        data.text = data.text.slice( 0, data.text.length-2 );
        data.$container.text( data.text + "..." );
      }
    }
		
	});
}( jQuery ));