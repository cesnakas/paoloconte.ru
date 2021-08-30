$(function (){

  var Sliders = {

    run: function() {
      this.runDetailCarousel();
    },
    resizeFunctions: function() {

    },

    loadFunctions: function() {

    },




      runDetailCarousel: function () {
              var carousel = $(".detail-item-big");

              $(carousel).owlCarousel({
                  items:1,
                  margin:0,
                  nav:false,
                  dots: true,
                  autoWidth: false,
                  responsiveRefreshRate: 1,
                  center: false,
                  loop:true,
                  mouseDrag: true
              });


              //nav start position select
              $(".detail-item-small .owl-item").eq(0).addClass("current");

              //scroll trigger
              /*
              carousel.on('mousewheel', '.owl-stage-outer', function (e) {
                  if (e.deltaY>0) {
                      carousel.trigger('prev.owl');
                  } else {
                      carousel.trigger('next.owl');
                  }
                  e.preventDefault();
              });*/

              //carousel is moved

              $(document).on("translate.owl.carousel", carousel, function(event) {
                  var item = event.item.index;
                  // console.log(item);
                  var items = event.item.count;
                  $(".detail-item-small").find(".current").removeClass("current");
                  $(".detail-item-small .owl-item").eq(item-1).addClass("current");

              });

              //add slides nav
              $('.detail-item-small .owl-item').click(function(){
                  $('.main-slider-nav .item').removeClass("current");
                  $(this).addClass("current");
                  carousel.trigger("to.owl.carousel", [$(this).closest(".owl-item").index(), 500, true]);
              });

              //when carousel is loaded
              $(carousel).on('initialize.owl.carousel', function () {
                  // console.log("1 slider start load");
              });

              $(carousel).on('initialized.owl.carousel', function () {
                  // console.log("2 slider is loaded!!!");

              });
      }


  }

  window.Application.addComponent("Sliders", Sliders);

});