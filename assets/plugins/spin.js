import {Spinner} from 'spin.js';
import "spin.js/spin.css";

const defaults = {
    lines: 10, // The number of lines to draw
    length: 0, // The length of each line
    width: 7, // The line thickness
    radius: 13, // The radius of the inner circle
    scale: 1, // Scales overall size of the spinner
    corners: 1, // Corner roundness (0..1)
    speed: 1, // Rounds per second
    rotate: 0, // The rotation offset
    animation: 'spinner-line-fade-quick', // The CSS animation name for the lines
    direction: 1, // 1: clockwise, -1: counterclockwise
    color: '#ffffff', // CSS color or array of colors
    fadeColor: 'transparent', // CSS color or array of colors
    top: '50%', // Top position relative to parent
    left: '50%', // Left position relative to parent
    shadow: '0 0 1px transparent', // Box-shadow for the lines
    zIndex: 100, // The z-index (defaults to 2e9)
    className: 'spinner', // The CSS class to assign to the spinner
    position: 'absolute', // Element positioning
};

function startSpin(el, opts) {
    if (!opts) {
        opts = {};
    }

    let id = 'loader_' + Math.random();
    $(el).append('<div id="'+id+'" class="loader"></div>');
    let target = document.getElementById(id);
    new Spinner($.extend(defaults, opts)).spin(target);
}
function stopSpin(el) {
    $(el).find('.loader').remove();
}

export {startSpin, stopSpin};
