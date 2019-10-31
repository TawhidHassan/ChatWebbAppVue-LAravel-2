window.Vue = require('vue');
//moment js
window.moment = require('moment');

Vue.filter('timeformet',function (arg) {
  return  moment(arg).format('MMMM Do YYYY, h:mm:ss a')
});

