 $(document).ready(function(){

    $(document).on('click', '.bx-context-toolbar-button',function(){
		setTimeout(style,1000);
    })

    })

   function style() {
  	$('select').chosen();
   }