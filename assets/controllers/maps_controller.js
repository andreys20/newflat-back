import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['mapContainer'];

    static values = {
        data: Array,
        center: String
    }

    connect() {
        this.initMap();
    }

    initMap() {
        var data = this.dataValue;
        var centerOption = this.centerValue;
        var geoList = [];
        var numArray = 0;

        ymaps.ready(function(){
            var myMap = new ymaps.Map('map', {
                center: [43.263113407323516,76.94720125390623],
                zoom: 11
            }, {
                searchControlProvider: 'yandex#search'
            }),
                clusterer = new ymaps.Clusterer({
                    preset: 'islands#invertedBlueClusterIcons',
                    clusterHideIconOnBalloonOpen: false,
                    geoObjectHideIconOnBalloonOpen: false,
                    clusterIconColor: '#003290',
                });

            data.forEach(function(item, index) {
                ymaps.geocode(item.location,{results:1}).then(
                    function(res){
                        var MyGeoObj = res.geoObjects.get(0);

                        var latitude = MyGeoObj.geometry.getCoordinates()[0];
                        var longitude = MyGeoObj.geometry.getCoordinates()[1];

                        if (data.length === 1) {
                            myMap.setCenter([latitude, longitude]);
                        }

                        geoList[index] = new ymaps.Placemark(
                            [latitude, longitude],
                            {
                                iconContent: item.title,
                                balloonContentHeader: item.title,
                                balloonContentBody: "<p><strong>Адрес: </strong>" + item.location + "</p>" +
                                    "<p><strong>Цена: </strong>" + item.priceTotal + " 〒</p>"
                                ,
                                balloonContentFooter: "<a href='" + item.detail + "'>Подробная информации</a>"
                            },
                            {
                                preset: 'islands#blueCircleDotIconWithCaption',
                                iconCaptionMaxWidth: '50',
                                iconColor: '#003290',
                                draggable: false
                            }
                        );

                        numArray++;
                        if (numArray === data.length) {
                            clusterer.add(geoList);
                            myMap.geoObjects.add(clusterer);

                            if (centerOption === 'custom')
                            {
                                console.log('almaty');
                                myMap.setCenter([43.263113407323516,76.94720125390623]);
                                myMap.setZoom(10);
                            } else {
                                myMap.setBounds(clusterer.getBounds(), {
                                    checkZoomRange: true
                                });
                            }
                        }
                    });

            });
        });
    }
}
