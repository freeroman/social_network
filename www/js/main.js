$(function() {
    $.nette.init();
});

$(function() {
    $("#frm-employeeForm-birth_dt").datepicker({
        dateFormat: "yy-mm-dd"
    });
});

$(function() {
    $( "#frm-eventForm-starting_dt" ).datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        onClose: function( selectedDate ) {
            $( "#frm-eventForm-ending_dt" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#frm-eventForm-ending_dt" ).datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        onClose: function( selectedDate ) {
            $( "#frm-eventForm-starting_dt" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
});

$(document).ready(function(){
    var dom = $(".md-modal");
    
    $(".md-overlay").click(function(){
        dom.removeClass("md-show");
        location.reload();
    });
    
    $(".show-md").click(function(){
        dom.addClass("md-show");
    });
});