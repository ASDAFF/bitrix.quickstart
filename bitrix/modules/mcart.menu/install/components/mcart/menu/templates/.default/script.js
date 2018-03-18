  function newSubLiObjToBot(Li, subLi){
    
      var SubLiObj = new Object();

      SubLiObj.Li = Li;
      SubLiObj.subLi = subLi;

      SubLiObj.goLeft = function() {
       this.subLi.hide();
          if( this.widthWindow < this.widthSubLiRight ){
              this.subLi.css( "left", this.widthLi - this.widthSubLi)
           };
      }

      SubLiObj.constuct = function() {

          this.widthLi = this.Li.width();
          this.widthSubLi = this.subLi.outerWidth();
          this.subLiLeft = this.Li.offset().left;
          this.widthWindow = $(window).width();
          this.widthSubLiRight = this.widthSubLi + this.subLiLeft;

      };

      SubLiObj.destruct = function() { 

          delete this.widthLi;
          delete this.widthSubLi;
          delete this.subLiLeft;
          delete this.widthWindow;
          delete this.widthSubLiRight;

          delete this.Li;
          delete this.subLi;
      };

   SubLiObj.constuct();
   SubLiObj.goLeft();
      SubLiObj.destruct();
  };


  function goodbayMouseMenu(Li){
          var subLi = Li.find( ".MCArt_bot__subLevelMenuBox" );
          subLi.hide();
  };

  function helloMouseMenu(Li){
          var subLi = Li.find( ".MCArt_bot__subLevelMenuBox" );
          subLi.show();    
  };

  

  function createBindToBot() {
      $("#MCArt_bot__topLevelMenu > li").each(function(){
          _Li = $( this );
          
          _Li.find( ".MCArt_bot__subLevelMenuBox" ).each(function(){
			_subLi = $(this);
     		newSubLiObjToBot(_Li, _subLi);
          });
      }) 
  };

  $(document).ready(function(){


   $('#MCArt_bot .MCArt_bot__flag').click(function(){
    $('#MCArt_bot').css("bottom", 0);
    $(this).hide();
   });

   $('html').click(function() {
    $(" #MCArt_bot ").css("bottom", -49);
    $('#MCArt_bot .MCArt_bot__flag').show();
   });

   $('#MCArt_bot').click(function(event){
    event.stopPropagation();
   });

		   $(" .MCArt_bot__subLevelMenuBox ").each(function(){

				 var menuBox = $(this),
				 newWidth = 0;
				
				 menuBox.find(" .MCArt_bot__subLevelMenu ").each(function(){
				 newWidth += $(this).width() + 40;
				});

				menuBox.width(newWidth - 20);

				menuBoxHeight = menuBox.height();
				menuBox.css("top", - (menuBoxHeight + 40));
				menuBox.css('left', 0);
		   });


	$("#MCArt_bot__topLevelMenu > li").mouseenter(function(){
          var _Li = $( this );
          helloMouseMenu(_Li);

      }).mouseleave(function(){
           var _Li = $( this );
           goodbayMouseMenu(_Li);
      });

   createBindToBot();
   $(window).resize(function(){
          createBindToBot();
      });
  });