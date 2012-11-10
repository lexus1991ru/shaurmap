/** 
*	Shaurmap core script
**/

var shaurmaShop = {
	name: 'Шаурма на средном',
	desc: 'Самая четкая шаурма в Нижнем! Отвечаю!',
	htmlBalloonContent: $('#balloon').html()
}

var axis = {
    x: 1,
    y: 0
}

var debug = true;

function initShaurmap() {

    var placeMarkCoordinates = [];
	var Mymap = new ymaps.Map('map', {
		center: [56.3, 43.95],
		zoom: 12,
		behaviors: ['drag', 'scrollZoom']
	});

    var mapTimer = setInterval(getUpperLeftCorner, 300);

    var currentLocation = new ymaps.Placemark([ymaps.geolocation.latitude, ymaps.geolocation.longitude],{
        balloonContentHeader: ymaps.geolocation.country,
        balloonContent: ymaps.geolocation.city,
        balloonContenFooter: ymaps.geolocation.region
    });



	// Scale slider
	Mymap.controls.add('zoomControl', { left : '15px', bottom: '15px' });

	// Some shit on the map
    var placemark = new ymaps.Placemark([56.26, 43.86]); //for navigation debugging
    var latitude = 0, longitude = 0;

    /*------------------------------------- Logic ------------------------------------------*/
    Mymap.geoObjects.add(currentLocation);
    Mymap.geoObjects.add(placemark);

	for(var i = 0; i < 5; i++ ) {
		latitude = Math.random()*0.1;
		longitude = Math.random()*0.2;

		placemark = new ymaps.Placemark([56.25 + latitude, 43.85 + longitude], {
				balloonContent: shaurmaShop.htmlBalloonContent,
                hintContent: 'я хочу, чтоб ты меня нажал'
			}, {
				iconImageHref: 'img/shop.png',
				iconImageSize: [20, 20],
				iconImageOffset: [-10, -10],
				hideIconOnBalloonOpen: false
			});

        placemark.events.add('click',function(e){
            var thisPlacemark = e.get('target');
            placeMarkCoordinates = thisPlacemark.geometry.getCoordinates();
            console.debug('Placemarkcoord '+ placeMarkCoordinates)
        });
		Mymap.geoObjects.add(placemark);
        console.debug()
	}




    Mymap.events.add('click',function(event){
        console.debug('changed');
       // var somevar = event.get('newMap');
        //somevar.getBounds();
        //console.debug(somevar[1]);
    });

      /*
     map.events.add('mapchange',function(){
         console.debug('changed');
         var mapData = map.getBounds();
         console.debug(mapData[1]);
     });*/





    //taking the coordinates of map's upper left corner
    var mapcoords = Mymap.getBounds();
    var upperLeftCorner = [];
    upperLeftCorner[axis.y] = mapcoords[1][axis.y];
    upperLeftCorner[axis.x] = mapcoords[0][axis.x];


    function getUpperLeftCorner(){
        var currentCoords = Mymap.getBounds();
        if(upperLeftCorner[axis.y]!=currentCoords[1][axis.y] && upperLeftCorner[axis.x]!=currentCoords[0][axis.x]){
            upperLeftCorner[axis.y] = currentCoords[1][axis.y];
            upperLeftCorner[axis.x] = currentCoords[0][axis.x];
            console.log('changed');
        }
    }


    if(debug){
        console.debug('leftBottom: '+mapcoords[0]+' rightUpper: '+mapcoords[1]);
        console.debug('upperLeft: '+upperLeftCorner);
        placemark = new ymaps.Placemark(upperLeftCorner);
        Mymap.geoObjects.add(placemark);
    }
}


