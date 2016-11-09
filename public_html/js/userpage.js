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

$(document).ready(function () {
    $('[data-toggle="popover"]').popover();

});

document.getElementById("addBtn").addEventListener("click", myFunction);

function myFunction() {
    document.getElementById("newRide").style.display = "block";
    window.scrollBy(0, 1000)
}

var idCount = 1;
$(document).ready(function () {
    $('.addmore').on('click', function () {

        var element = $(".mytemplate").clone();
        element.removeClass("mytemplate").show().appendTo(".dates");
        console.log(element.children()[0]);
        var datepickerEl = element.children()[0];
        datepickerEl.name = "exdatepicker" + idCount;
        idCount++;
        ;
    });
    $(document).on("focus", ".datepicker", function () {
        $(this).datepicker();
    });
});
document.getElementById("addBtn").addEventListener("click", myFunction);

function myFunction() {
    document.getElementById("addBtn").style.display = "none";
    document.getElementById("newRide").style.display = "block";
    window.scrollBy(0, 1000)
}