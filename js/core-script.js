/** 
*	Shaurmap core script
**/

var shaurmaShop = {
	name: 'Шаурма на средном',
	desc: 'Самая четкая шаурма в Нижнем! Отвечаю!',
	htmlBalloonContent: $('#balloon').html()
	
}

var debug = true;

function initShaurmap() {

    var placeMarkCoordinates = [];
	var map = new ymaps.Map('map', {
		center: [56.3, 43.95],
		zoom: 12,
		behaviors: ['drag', 'scrollZoom']
	});
    var currentLocation = new ymaps.Placemark([ymaps.geolocation.latitude, ymaps.geolocation.longitude],{
        balloonContentHeader: ymaps.geolocation.country,
        balloonContent: ymaps.geolocation.city,
        balloonContenFooter: ymaps.geolocation.region
    });

	// Scale slider
	map.controls.add('zoomControl', { left : '15px', bottom: '15px' });

	// Some shit on the map
    var placemark = new ymaps.Placemark([56.26, 43.86]); //for navigation debugging
    var latitude = 0, longitude = 0;

    /*------------------------------------- Logic ------------------------------------------*/
    map.geoObjects.add(currentLocation);
    map.geoObjects.add(placemark);

	for(var i = 0; i < 5; i++ ) {
		latitude = Math.random()*0.1;
		longitude = Math.random()*0.2;

		placemark = new ymaps.Placemark([56.25 + latitude, 43.85 + longitude], {
				balloonContent: shaurmaShop.htmlBalloonContent,
                hintContent: 'я хочу, чтоб ты меня нажал'
			}, {
				iconImageHref: 'http://shaurmap/shaurmap/img/shop.png',
				iconImageSize: [20, 20],
				iconImageOffset: [-10, -10],
				hideIconOnBalloonOpen: false
			});

        placemark.events.add('click',function(e){
            var thisPlacemark = e.get('target');
            placeMarkCoordinates = thisPlacemark.geometry.getCoordinates();
            console.debug('Placemarkcoord '+ placeMarkCoordinates)
        });
		map.geoObjects.add(placemark);
        console.debug()
	}










    //taking the coordinates of map's upper left corner
    var mapcoords = map.getBounds();
    var leftUpperCorner = [];
    leftUpperCorner[0] = mapcoords[1][0];
    leftUpperCorner[1] = mapcoords[0][1];





    if(debug){
        console.debug('leftBottom: '+mapcoords[0]+' rightUpper: '+mapcoords[1]);
        console.debug('leftUpper: '+leftUpperCorner);
        placemark = new ymaps.Placemark(leftUpperCorner);
        map.geoObjects.add(placemark);
    }
}


