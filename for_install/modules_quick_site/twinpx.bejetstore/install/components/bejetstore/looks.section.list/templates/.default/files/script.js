(function($) {
	$(function() {
  
    var marginTop = 27;
    
    appendGreyBlock();
    
    cutH3();
    
    $( window ).resize( function() {
      $( '.bj-lookbook__after' ).remove();
      appendGreyBlock();
      cutH3();
    });
    
    function appendGreyBlock() {
      $( '.bj-lookbook__col' ).each( function() {
        var $col = $( this );
        var dif = $col.parent().height() - $col.height();
        
        if ( dif > marginTop ) {
          $col.append( '<div class="bj-lookbook__after" style="height: ' + ( dif - marginTop ) + 'px;"></div>' );
        }
      });
    }
    
    function cutH3() {
      $( '.bj-lookbook__cover h3' ).each( function() {
        var $h3 = $( this );
        
        if ( !$h3.data( 'heading' )) {
          $h3.data( 'heading', $h3.text());
        }
        
        setTimeout( function() {
          cutLine({
            text: $h3.data( 'heading' ),
            height: 54,
            $container: $h3
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