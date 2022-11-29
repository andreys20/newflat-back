import 'owl.carousel';
import 'owl.carousel/dist/assets/owl.carousel.min.css';
import './styles/building.less';

initCarousel();

function initCarousel() {
    if ($().owlCarousel !== undefined) {
        $('.section_building_slider__list').owlCarousel({
            loop:false,
            nav:true,
            dot:true,
            items:1
        });
    }
}
