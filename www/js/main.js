$(function() {
    $.nette.init();
});

$(function() {
    $("#frm-newEvent-starting_dt").datepicker();
});

$(function() {
    $( "#frm-newEvent-starting_dt" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        onClose: function( selectedDate ) {
            $( "#frm-newEvent-ending_dt" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#frm-newEvent-ending_dt" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        onClose: function( selectedDate ) {
            $( "#frm-newEvent-starting_dt" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
    $( "#frm-newEvent-starting_dt" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
    $( "#frm-newEvent-ending_dt" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
    $( "#frm-employeeForm-birth_dt" ).datepicker();
    $( "#frm-employeeForm-birth_dt" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
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