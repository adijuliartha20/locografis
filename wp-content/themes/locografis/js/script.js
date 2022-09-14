jQuery(document).ready(function(){

    set_slide();



    jQuery(window).scroll(function(){

        set_event_header_scroll()

    })  



});





function set_event_header_scroll(){

    var state_header = jQuery('#header').attr('data-rel');

    if(state_header=='') return;



    var ww = jQuery(window).width();

    var wh = jQuery("#slide").height();

    var sh = 100;

    if(ww>768) var hms = Math.round(wh/sh);

    else var hms = Math.floor(wh/sh) - 2;

    //var hms = Math.floor(wh/sh) - 2;

    var mxs = hms * sh;

    var half_mxs = mxs/2;

    var mxo = 6;//8 tingkatan



    var single_opacity = hms/mxo;

    var pos = jQuery(window).scrollTop();

    var state = pos/sh;



    var curr_opacity = 1;

    //console.log(pos+'<='+mxs);

    if(pos<=mxs){

        var curr_opacity = (state * single_opacity)/10;

    }

    jQuery('#header').css('background','rgba(255,255,255,'+curr_opacity+')');



    if(pos>=half_mxs) jQuery('body').addClass('white-state-header');

    else jQuery('body').removeClass('white-state-header');

}



function set_event_header_scroll_old(){    

    var ww = jQuery(window).width();

    var wh = jQuery("#slide").height();

    var sh = 100;

    if(ww>650) var hms = Math.round(wh/sh);

    else var hms = Math.floor(wh/sh) - 2;

    var mxs = hms * sh;

    var half_mxs = mxs/2;

    var mxo = 8;//8 tingkatan



    var single_opacity = hms/mxo;

    var pos = jQuery(window).scrollTop();

    var state = pos/sh;



    var curr_opacity = 0.8;

    //console.log(pos+'<='+mxs);

    if(pos<=mxs){

        var curr_opacity = (state * single_opacity)/10;

    }

    jQuery('#header').css('background','rgba(255,255,255,'+curr_opacity+')');



    if(pos>=half_mxs) jQuery('body').addClass('white-state-header');

    else jQuery('body').removeClass('white-state-header');

}









function set_slide(){

    if(jQuery('#slide input').length>0){

        var slide = [];

        jQuery('#slide input').each(function(){

            var src = jQuery(this).val();

            var slide_object = new Object();

            slide_object.src = src;

            slide.push(slide_object);

        })





        var np = jQuery('#slide input').length;

        if(np > 1){

            var btn = '';

            for(i=0; i<np; i++){

                btn += '<button onClick="goto_slide(event)" data-i="'+i+'"></button>';

                

            }

            //jQuery('#slide').append('<div id="pagging-slide" class="pagging-slide">'+btn+'</div>');

        }



        jQuery("#slide").vegas({

            delay: 5000,

            slides:slide,

            transition: 'fade',

            timer: false,

            walk: function (nb) {

                

                //jQuery('#pagging-slide button').removeClass('active').eq(nb).addClass('active');

            },

            init: function (globalSettings) {},

        });

    }

}







function filter_portofolio(event){

    jQuery('#overlay').fadeIn(300)

    jQuery('#list-portofolio .item-portofolio').fadeOut(300,function (){

        jQuery('#list-portofolio .item-portofolio').remove();

        var show_item = jQuery(event.target).attr('data-filter');

        var item = '';

        jQuery('#list-portofolio-clone').find(show_item).each(function(){

            jQuery(this).clone().appendTo('#list-portofolio')

        })



        setTimeout(function(){

             setTimeout(function(){

                jQuery('#list-portofolio').append(item);

                jQuery('#filter-portofolio button').removeClass('current-state');

                jQuery(event.target).addClass('current-state');

            }, 400); 

            jQuery('#overlay').fadeOut(300); 

        }, 600); 

        

    });    

}



function fbs_click(id) {

    var leftPosition, topPosition;

    var width = 400;

    var height = 300;

    //Allow for borders.

    leftPosition = (window.screen.width / 2) - ((width / 2) + 10);

    //Allow for title and status bars.

    topPosition = (window.screen.height / 2) - ((height / 2) + 50);

    var windowFeatures = "status=no,height=" + height + ",width=" + width + ",resizable=yes,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no";

    u=location.href;

    t=document.title;

    var url_app = jQuery('#'+id).attr('data-url');

    //console.log(url_app);

    window.open(url_app+encodeURIComponent(u)+'?v=1&t='+encodeURIComponent(t),'sharer', windowFeatures);

    return false;

}





function scroll_to(event,id,mt){

    //var id = jQuery(event.target).attr('rel');

     //console.log(id);

    var target = jQuery('#'+id)

    //console.log(target);

    

    jQuery('html, body').stop().animate({

        scrollTop: target.offset().top - mt

    }, 1000,function (){

        //var strId = id.replace('#','');

        //setTimeout('scroll_hover_menu',150)

    });

    

}





// HAMBURGLERv2

function togglescroll () {

  $('body').on('touchstart', function(e){

    if ($('body').hasClass('nnoscroll')) {

        //scroll = false

        //e.preventDefault();

    }

  });

}



$(document).ready(function () {

    togglescroll()

   

    var timeoutId;

    $(".icon").click(function () {

        if(timeoutId ){

            clearTimeout(timeoutId );  

        }

        timeoutId = setTimeout(function(){

            if(!jQuery('body').hasClass('nnoscroll')){

                jQuery("#mobilenav").removeAttr( 'style' ); 

                jQuery(".mobilenav").slideDown(500);

                jQuery('.header').addClass('hashowmobile');

            }else{

                jQuery(".mobilenav").slideUp(500);

                jQuery('.header').removeClass('hashowmobile');

            }



            $(".top-menu").toggleClass("top-animate");

            $("body").toggleClass("nnoscroll");

            if ($('body').hasClass('nnoscroll')) scroll = false

            else scroll = true

        



            $(".mid-menu").toggleClass("mid-animate");

            $(".bottom-menu").toggleClass("bottom-animate");        

        }, 250);

    });



    /*jQuery('.mobilenav li').mouseenter(function(event){

        var index = jQuery('.mobilenav li').index(this);

        if(index>0) jQuery('.mobilenav li:eq('+(index-1)+')').addClass('has-hover');

    }).mouseleave(function (event){

        jQuery('.mobilenav li.has-hover').removeClass('has-hover');

    });*/



    



    /*$(document).bind('touchmove', function(){

        scroll = false

    }).unbind('touchmove', function(){

        scroll = true

    })*/



    /*$(window).scroll(function() {

        //console.log(scroll)

        if ($('body').hasClass('nnoscroll') && scroll == false) {

            $(document).scrollTop(0);

        }

    })*/



});







// PUSH ESC KEY TO EXIT



$(document).keydown(function(e) {

    if (e.keyCode == 27) {

        $(".mobilenav").slideUp(500);

        $(".top-menu").removeClass("top-animate");

        $("body").removeClass("nnoscroll");

        $(".mid-menu").removeClass("mid-animate");

        $(".bottom-menu").removeClass("bottom-animate");

    }

});