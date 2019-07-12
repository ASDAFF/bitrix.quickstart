$( document ).ready(function() { 
$(".staff_wrapp_dealers a.section_title").click(function () {
      $(".staff_wrapp_dealers a.opened").each(function () {
          $(this).removeClass('opened').next().slideToggle(333);
      });
      $(this).addClass('opened').next().slideToggle(333);
      opened = $(this);
  });
});

//Загружаем карту
    ymaps.ready(function(){



    	//рисуем карту

	        var coordinate = "55.76,37.64";
	        var myMap = new ymaps.Map("sidemap", {
	       // center: [coordinate[0],coordinate[1]],
	        center: coordinate.split(','),
	        zoom: 9,
	        controls: ['zoomControl']
	    	});

			coords = "92.58,56.02".split(',');
	      //  myMap.setZoom(14).panTo(coords);






	           // переход карты к другим координатам
    function clickGoto(pos) {

    	pos = pos.split(',');

    	var mypos = [];
    	mypos = [Number(pos[0]),Number(pos[1])];
        // переходим по координатам
       // console.log(mypos);
        myMap.panTo(mypos, {
            flying: 1
        });

        return false;
    }
    	//Выбор другого города
    	$('.section .section_title').click(function(){
    		coord = $(this).data('citycoord');
    		//console.log(coord);
    		clickGoto(coord);
    		createDealersOnMap();
    	});



//создаем точки по дилерам
function createDealersOnMap(){
	myMap.geoObjects.removeAll();

      $('input[type=radio][name=coord]').removeAttr('checked');
    	$('input[type=radio][name=coord]').each(function(){

        if($(this).val().length > 0){
                coord = $(this).val();
                coord = coord.split(',');
                adr = $(this).data('adr');
                var myPlace = new ymaps.Placemark(
                          [coord[0],coord[1]],
                          {balloonContent: adr},
                          {
                             balloonPanelMaxMapArea: 0,
                            preset: 'islands#darkOrangeDotIcon',

                            // openEmptyBalloon: true
                          }
                    );

                 myMap.geoObjects.add(myPlace);
            }


         }); }
    	createDealersOnMap();




    	$('input[type=radio][name=coord]').change(function(){
    		myMap.geoObjects.removeAll();
    		if($(this).val().length > 0){
                coord = $(this).val();
                coord = coord.split(',');
                adr = $(this).data('adr');
                var myPlace = new ymaps.Placemark(
                          [coord[0],coord[1]],
                          {   balloonContent: adr},
                          {
                             balloonPanelMaxMapArea: 0,
                            preset: 'islands#darkOrangeDotIcon',

                            // openEmptyBalloon: true,
                              // balloonContentBody: "Содержимое <em>балуна</em> метки",
                          }
                    );

                 myMap.geoObjects.add(myPlace);
            }

    	});


	});

/*
	$(function() {



	});

*/
console.log('changed');
