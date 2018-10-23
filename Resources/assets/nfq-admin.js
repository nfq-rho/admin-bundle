// create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

require('bootstrap-sass');
require('jquery-slimscroll');
require('select2/dist/js/select2.full');

require('@fortawesome/fontawesome-free/js/all.min');

const Moment = require('moment');
global.moment = Moment;
require('daterangepicker');

// ------ AdminLTE framework ------
global.$.AdminLTE = {};
global.$.AdminLTE.options = {};
require('admin-lte/dist/js/adminlte.min');

// ------ Theme itself ------
// require('./default_avatar.png');
require('./../public/js/plugins/jquery.simplyCountable')
require('./../public/js/scripts')
