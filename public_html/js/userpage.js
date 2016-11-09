/**
 * Created by kirichek on 11/2/16.
 */

function initialize_start() {
    var input = document.getElementById('searchTextFieldStart');
    var autocomplete = new google.maps.places.Autocomplete(input);
}
google.maps.event.addDomListener(window, 'load', initialize_start);
function initialize_end() {
    ;
    var input = document.getElementById('searchTextFieldEnd');
    var autocomplete = new google.maps.places.Autocomplete(input);
}
google.maps.event.addDomListener(window, 'load', initialize_end);

$(function () {
    $('#datetimepicker1').datetimepicker();
});