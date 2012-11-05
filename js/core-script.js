/** 
*	Shaurmap core script
**/

function initShaurmap() {
	var map = new ymaps.Map('map', {
		center: [56.28, 44.0],
		zoom: 11,
		behaviors: ['drag', 'scrollZoom']
	});
	
	// Ползунок изменения масштаба
	map.controls.add('zoomControl', { left : '15px', bottom: '15px' });

	// Создаем метку
	var placemark = null, latitude = 0, longitude = 0;

	for(var i = 0; i < 100; i++ ) {
		latitude = Math.random()*0.1;
		longitude = Math.random()*0.1;

		placemark = new ymaps.placemark([56.3 + latitude, 43.9 + longitude], {
				balloonContent: '<img src="http://img-fotki.yandex.ru/get/6114/82599242.2d6/0_88b97_ec425cf5_M" />'
			}, {
				iconImageHref: 'http://avatars.yandex.net/get-avatar/3156681/3242aafbd6f41f88a8783a56d04091e7.3908-middle',
				iconImageSize: [20, 20],
				iconImageOffset: [-10, -10],
				hideIconOnBalloonOpen: false
			});
			
		map.geoObjects.add(placemark);
	}
}