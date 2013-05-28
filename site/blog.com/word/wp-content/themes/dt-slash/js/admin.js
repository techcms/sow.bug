$(document).ready(function () {
   
   $("input[name=day_from], input[name=day_to]").bind('click change keyup mouseout blur focus', function () {
      $("#"+($(this).attr("id") == "day_to" ? "night_from" : "night_to")).val( $(this).val() );
   });
   
   $("input[name=skin]").click(function () {

      $("#switch_table").hide();
      $("#" + $(this).val() + "_table").show();

      if ( ($(this).val() == "day") || ($(this).val() == "switch") )
      {
         $("#logo_day").show();
      }
      else
      {
         $("#logo_day").hide();
      }
      if ( ($(this).val() == "night") || ($(this).val() == "switch") )
      {
         $("#logo_night").show();
      }
      else
      {
         $("#logo_night").hide();
      }
   });

   $(".pattern, .art").click(function () {
      var cl = ($(this).hasClass('pattern') ? 'pattern' : 'art');
      $("."+cl).removeClass('selected');
      $(this).addClass('selected');
      $(this).parent().find("input[type=hidden]").val( $(this).attr("s") );
   });
   
});
