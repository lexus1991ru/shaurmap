/** 
*	Shaurmap core script
**/

var globalMap;
var upperLeftCorner = [];
var currentLocation = [];
var placeMarkCoordinates = [];
var axis = {
    x: 1,
    y: 0
}

var shaurmaShop = {
    name: 'Шаурма на средном',
    desc: 'Самая четкая шаурма в Нижнем! Отвечаю!',
    htmlBalloonContent: $('#balloon').html()
}

var debug = true;

function globalInitialization(){
    globalMap = initShaurmap();
    globalMap.events.add('click',function(event){
        console.debug('changed from global');
    });
    var mapTimer = setInterval(isUpperLeftChanged, 300);
}

/*
* initiates all the necessary data for map creating i.e. placemarks,
* user-location, main event etc.
* */
function initShaurmap() {

	var Mymap = new ymaps.Map('map', {
		center: [56.3, 43.95],
		zoom: 12,
		behaviors: ['drag', 'scrollZoom']
	});

    // Scale slider
    Mymap.controls.add('zoomControl', { left : '15px', bottom: '15px' });

    currentLocation[0] = ymaps.geolocation.latitude;
    currentLocation[1] = ymaps.geolocation.longitude;

    var currentLocationPlacemark = new ymaps.Placemark(currentLocation,{
        balloonContentHeader: ymaps.geolocation.country,
        balloonContent: ymaps.geolocation.city,
        balloonContenFooter: ymaps.geolocation.region
    });

	// Some shit on the map
    var placemark = new ymaps.Placemark([56.26, 43.86]); //for navigation debugging
    var latitude = 0, longitude = 0;

    /*------------------------------------- Logic ------------------------------------------*/
    Mymap.geoObjects.add(currentLocationPlacemark);
    Mymap.geoObjects.add(placemark);

	for(var i = 0; i < 5; i++ ) {
		latitude = Math.random()*0.1;
		longitude = Math.random()*0.2;

		placemark = new ymaps.Placemark([56.25 + latitude, 43.85 + longitude], {
				balloonContent: shaurmaShop.htmlBalloonContent,
                hintContent: 'show info'
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



   /*
    Mymap.events.add('click',function(event){
        console.debug('changed');
        console.debug(placeMarkCoordinates);
       // var somevar = event.get('newMap');
        //somevar.getBounds();
        //console.debug(somevar[1]);
    });*/

      /*
     map.events.add('mapchange',function(){
         console.debug('changed');
         var mapData = map.getBounds();
         console.debug(mapData[1]);
     });*/


    /*
    * taking the coordinates of map's upper left corner.
    * yandex API contains x in [1] element and y in [0]
    * getBounds will get bottomleft[0] and upperright[1] points
    * here will get the y from upperright and x from bottomleft
    * */
    var mapcoords = Mymap.getBounds();

    upperLeftCorner[axis.y] = mapcoords[1][axis.y];
    upperLeftCorner[axis.x] = mapcoords[0][axis.x];

    if(debug){
        console.debug('leftBottom: '+mapcoords[0]+' rightUpper: '+mapcoords[1]);
        console.debug('upperLeft: '+upperLeftCorner);
        placemark = new ymaps.Placemark(upperLeftCorner);
        Mymap.geoObjects.add(placemark);
    }

    return Mymap;
}

/*
* checks if upperleft coordinates was changed
* */
function isUpperLeftChanged(){
    var currentCoords = globalMap.getBounds();
    if(upperLeftCorner[axis.y]!=currentCoords[1][axis.y] && upperLeftCorner[axis.x]!=currentCoords[0][axis.x]){
        upperLeftCorner[axis.y] = currentCoords[1][axis.y];
        upperLeftCorner[axis.x] = currentCoords[0][axis.x];
        console.log('changed');
    }
}

/*
* gets the route from currenlocation to currently selected placemark
* */
function getRoute(){
    //console.debug(globalMap.getRoute(1));
    ymaps.route([[ymaps.geolocation.latitude, ymaps.geolocation.longitude],placeMarkCoordinates]).then(
        function (route){
            globalMap.geoObjects.add(route);
        },
        function (error){
            alert('some shit just happend: '+ error.message);
        }
    );
}