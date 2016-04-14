$(function(){
  $.datepicker.setDefaults(
    $.extend( $.datepicker.regional[ 'vn' ] )
  );
  $( '#datepicker' ).datepicker();
});