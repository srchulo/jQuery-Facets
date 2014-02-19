



  $(".scrollbar").scroller();

    
    

 // Alert Message


  $('#message_trigger_ok').on('click', function(e) {
    e.preventDefault();
    $.scojs_message('<i class="fa fa-check-circle"></i> <strong>Well done!</strong> You successfully read this important alert message.', $.scojs_message.TYPE_OK);
  });
  $('#message_trigger_err').on('click', function(e) {
    e.preventDefault();
    $.scojs_message('<i class="fa fa-exclamation-circle"></i> <strong>Oh snap!</strong> Change a few things up and try submitting again.', $.scojs_message.TYPE_ERROR);
    });


       

 // Tooltips

    $('.tooltip-demo').tooltip({
      selector: "[data-toggle=tooltip]",
      container: "body"
    })

    $('.tooltip-test').tooltip()
    $('.popover-test').popover()




