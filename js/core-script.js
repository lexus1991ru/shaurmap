/** 
*	Shaurmap core script
**/

var shaurmaShop = {
	name: 'Шаурма на средном',
	desc: 'Самая четкая шаурма в Нижнем! Отвечаю!',
	htmlBalloonContent: $('#balloon').html()
}

function initShaurmap() {
	var map = new ymaps.Map('map', {
		center: [56.3, 43.95],
		zoom: 12,
		behaviors: ['drag', 'scrollZoom']
	});
	
	// Ползунок изменения масштаба
	map.controls.add('zoomControl', { left : '15px', bottom: '15px' });

	// Создаем метку
	var placemark = null, latitude = 0, longitude = 0;
	
	for(var i = 0; i < 5; i++ ) {
		latitude = Math.random()*0.1;
		longitude = Math.random()*0.2;

		placemark = new ymaps.Placemark([56.25 + latitude, 43.85 + longitude], {
				balloonContent: shaurmaShop.htmlBalloonContent
			}, {
				iconImageHref: 'img/shop.png',
				iconImageSize: [20, 20],
				iconImageOffset: [-10, -10],
				hideIconOnBalloonOpen: false
			});
			
		map.geoObjects.add(placemark);
	}
}


