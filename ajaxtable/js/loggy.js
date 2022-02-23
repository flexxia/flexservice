// implement JS callback from 'library' 'ajaxtable/loggy'

(function($) {
  $.fn.loggy = function(data) {
    window.alert("From invoke, You input : " + data);
    console.log(data);
  };
})(jQuery);
