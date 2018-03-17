
$(window).scroll(function() {    
                    var scroll = $(window).scrollTop();
                    if (scroll >=50) {
                         $(".nav-1").addClass("nav-1-1");
                         $(".nav-1-1").removeClass("nav-1");
                     } else {
                         $(".nav-1-1").addClass("nav-1");
                         $(".nav-1").removeClass("nav-1-1");
                     }
              });

jQuery(document).ready(function($) {
      $(".scroll").click(function(event){   
        event.preventDefault();
        $('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
      });
    });
 

new WOW().init();

 $(document).ready(function() {
 
  $("#owl-demo").owlCarousel({
 
      autoPlay: 3000, 
      pagination:false,
      items : 3,
      navigation : true, 
      navigationText:["<img src=\"img/leftar.png\">","<img src=\"img/rightar.png\">"],
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [979,3]

      
  });
 
});

  $(document).ready(function() {
 
  $("#owl-demo5").owlCarousel({
 
      pagination:true,
      items : 1,
      navigation : false, 
      itemsCustom : [
        [450, 1],
        [600, 1],
      ],
      
  });
 
});

  $(document).ready(function() {
 
  $("#owl-demot").owlCarousel({
 
      autoPlay: 3000, //Set AutoPlay to 3 seconds
 
      items : 3,
      itemsCustom : [
        [0, 1],
        [450, 1],
        [600, 1],
        [700, 2],
        [1199, 3],
      ],
      navigation:false,
      pagination:true,
      transitionStyle:true,
  });
 
});

  $(document).ready(function() {
 
  $("#owl-demo4").owlCarousel({
 
      pagination:false,
      items : 3,
      navigation : true, 
      navigationText:["<img src=\"img/leftar.png\">","<img src=\"img/rightar.png\">"],
      
  });
 
});
$(document).ready(function() {
 
  $("#owl-demol").owlCarousel({
 
      autoPlay: 3000, //Set AutoPlay to 3 seconds
 
      items : 3,
      itemsCustom : [
        [0, 1],
        [450, 1],
        [600, 1],
        [700, 2],
        [1199, 3],
      ],
      navigation:true,
      pagination:false,

      navigationText:["<img src=\"img/leftar.png\">","<img src=\"img/rightar.png\">"],
 
  });
 
});

$(document).ready(function() {
 
  $("#owl-demo2").owlCarousel({
 
      // autoPlay: 3000, //Set AutoPlay to 3 seconds
      items : 3,
      navigation : true,
      pagination : true,
      navigationText  :  ["",""] ,
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [991,2],
      itemsTablet :[600,1],
      itemsMobile :[320,1],
      stopOnHover:true,
      addClassActive : true,
      navigationText:["<img src=\"img/leftar.png\">","<img src=\"img/rightar.png\">"],
      afterMove:function(){
            //reset transform for all item
            $(".owl-item").find(".single-member").removeClass("active");
            //add transform for 2nd active slide
            $(".active").eq(1).find(".single-member").addClass("active");

        },
        //set init transform
        afterInit:function(){
            $(".active").eq(1).find(".single-member").addClass("active");
        }
 
  }); 
});



// var k = 
$("#owl-demo2").find(".owl-item.active").eq(1).addClass("hhh");
// console.log(k);
 // k.addClass("hhh");

$('article').readmore({
    speed: 500,
    collapsedHeight: 80
});



$("body").on("click",".single-member",function(){

        $('.single-member').removeClass('active');
        $(this).addClass('active');   


        //var k = $("body").find('.single_member').length;
        //console.log(k); 

});




// $("body").on("click",".search_box",function(){
//  $(this).attr("value","");
// });
