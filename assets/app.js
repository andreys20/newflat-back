/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.less in this case)
import 'owl.carousel';
import 'owl.carousel/dist/assets/owl.carousel.min.css';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap';
import './styles/app.less';

// start the Stimulus application
import './bootstrap';
import $ from 'jquery';
global.$ = global.jQuery = $;
