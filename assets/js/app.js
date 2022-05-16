/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)

const $ = require('jquery');
require('es6-promise').polyfill();
var axios = require('axios');

require('../css/app.scss');
require('bootstrap-select/dist/css/bootstrap-select.min.css')
require('bootstrap');
require('bootstrap-select');
require('@fortawesome/fontawesome-free/js/all.min.js');
global.moment = require('moment');
require('tempusdominus-bootstrap-4');
var bsStepper = require("bs-stepper")

const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);

require('./main.js');