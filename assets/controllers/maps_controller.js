import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['mapContainer'];

    static values = {
        data: Array
    }

    connect() {
        this.initMap();
    }

    initMap() {
        var data = this.dataValue;

        ymaps.ready(function(){
            var myMap = new ymaps.Map('map', {
                center: [51.1525297721169,71.48677055029289],
                zoom: 11
            }, {
                searchControlProvider: 'yandex#search'
            });

            data.forEach(function(item) {
                ymaps.geocode(item.location,{results:1}).then(
                    function(res){
                        var MyGeoObj = res.geoObjects.get(0);

                        var latitude = MyGeoObj.geometry.getCoordinates()[0];
                        var longitude = MyGeoObj.geometry.getCoordinates()[1];

                        if (data.length === 1) {
                            myMap.setCenter([latitude, longitude]);
                        }

                        var myGeoObject = new ymaps.GeoObject({
                            // Описание геометрии.
                            geometry: {
                                type: "Point",
                                coordinates: [latitude, longitude]
                            },
                            properties: {
                                iconContent: item.title,
                                balloonContentHeader: item.title,
                                balloonContentBody: "<strong>Адрес:</strong>" + item.location,
                            }
                        }, {
                            preset: 'islands#blackStretchyIcon',
                            iconColor: '#003290',
                            draggable: false
                        });

                        console.log(item.title);
                        myMap.geoObjects.add(myGeoObject);
                    });

            });
        });
    }
}
