/* 
==========================================================================
  Juno 1.0.1 - 05 Mar 2014
  by Simon Marussi
  http://codecanyon.net/user/simon81/portfolio
==========================================================================
*/
$(document).ready(function()
{
  var $menu = $('.ewMenuColumn');
  if(getCookie('juno')!=""){
    $menu.addClass(getCookie('juno'));
  }else{
    $menu.addClass('stato1');
  }
  $menu.addClass('statoForzato');
  var $btn = $('<a/>')
    .html('')
    .attr('href','#')
    .addClass('button-mobile')
    .prependTo('#ewContentColumn')
    .on('click',function(event)
    {
      event.preventDefault();
      if ($menu.is('.stato1')){
        $menu.removeClass('stato1');
        $menu.addClass('stato2');
        $menu.removeClass('statoForzato');
        createCookie("juno", "stato2", 1); 
      }else{
        $menu.removeClass('stato2');
        $menu.addClass('stato1');
        $menu.removeClass('statoForzato');
        createCookie("juno", "stato1", 1); 
      }
    });

  //looking for the table with breadcrumb
  $(".breadcrumb").closest("table").addClass('tbl_breadcrumb');

  //fix a bug in the export list buttons for the view pages
    if ( $('.ewListExportOptions > .ewExportOption').size() == 0) {
        $('.ewViewExportOptions').addClass('displaynone');
    };

  //style the search button
  if ($('.ewSearchTable a.accordion-toggle').length){
      $('ul.breadcrumb').addClass('spingiDx');
  }

//at the end, let's show the body :-)
  $('body').css( "visibility", "visible" );
});

function createIcon(name, icon){
    if(name!='' && icon!=''){
      $('#RootMenu > li a').filter(function() { return $.text([this]) === name; }).addClass(icon);  
    }
}

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}
