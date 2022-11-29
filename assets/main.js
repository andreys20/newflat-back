import 'owl.carousel';
import 'owl.carousel/dist/assets/owl.carousel.min.css';
initCarousel();

function initCarousel() {
    if ($().owlCarousel !== undefined) {
        $('.section_articles_slider__list').owlCarousel({
            loop:false,
            nav:true,
            dot:false,
            responsive:{
                0:{
                    items:1,
                    center: true,
                    dot: true,
                },
                768:{
                    items:2
                },
                1000:{
                    items:3
                },
                1300:{
                    items:4
                },
            }
        });
    }
}
