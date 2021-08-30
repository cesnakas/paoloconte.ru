$(function (){
    
    var Sliders = {
        
        run: function() {
            this.runMainCarousel();
            this.runPromoCarousel();
            this.runMainItemCarousel();
            this.runMapCarousel();
            // this.runAddDetailCarousel();
            
            this.detailSimilarCarousel();
            this.detailWatchedCarousel();
            this.basketCarousel();
            this.runDetailCarousel();
            this.runDetailClothesCarousel();

        },
        resizeFunctions: function() {
            
        },
        
        loadFunctions: function() {
            this.iniNavTooltip();
            
        },
        
        
        runMainCarousel: function () {
            // console.log("brand carousel ini!");
            
            // Lazyload slider
            
            var carousel = $(".main-slider");
            
            carousel.owlCarousel({
                items:1,
                margin:0,
                nav: true,
                navText: ["<svg class='i-icon'><use xlink:href='#arrow-slider'/></svg>","<svg class='i-icon'><use xlink:href='#arrow-slider'/></svg>"],
                dots: true,
                autoWidth: false,
                responsiveRefreshRate: 1,
                center: false,
                loop:true,
                mouseDrag: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplaySpeed: 2000

            });
            carousel.on('translated.owl.carousel', function (e) {
                if (!!Application.Components.Main) {
                    Application.Components.Main.lazy.revalidate();
                }
            })
            
            //nav start position select
            //$(".main-slider-nav .item-box").eq(0).find(".item").addClass("active");
            
            //scroll trigger
            /*
             carousel.on('mousewheel', '.owl-stage-outer', function (e) {
             if (e.deltaY>0) {
             carousel.trigger('prev.owl');
             } else {
             carousel.trigger('next.owl');
             }
             e.preventDefault();
             });
             
             */
            
            
            
            //when carousel is loaded
            // $(carousel).on('initialize.owl.carousel', function () {
            //     // console.log("1 slider start load");
            // });

            // $(carousel).on('initialized.owl.carousel', function () {
            //     // console.log("2 slider is loaded!!!");
            //
            // });
            
            
        },
        runPromoCarousel: function () {
            // console.log("brand carousel ini!");
            
            var carousel = $(".promo-slider");
            
            carousel.owlCarousel({
                items:1,
                margin:0,
                nav:false,
                navText: '',
                dots: true,
                autoWidth: false,
                responsiveRefreshRate: 1,
                center: false,
                loop:false,
                mouseDrag: true
                
            });
            carousel.on('translated.owl.carousel', function (e) {
                if (!!Application.Components.Main) {
                    Application.Components.Main.lazy.revalidate();
                }
            })
        },
        runMainItemCarousel: function () {
            // console.log("brand carousel ini!");
            
            var carousel = $(".main-item-slider");
            
            carousel.owlCarousel({
                //items:7,
                margin:0,
                nav:true,
                navText: '',
                dots: true,
                autoWidth: true,
                //responsiveRefreshRate: 1,
                center: true,
                loop:true,
                mouseDrag: true,
                responsive:{
                    0:{
                        items:5
                    },
                    1300:{
                        items:7
                    }
                }
            });

            $(document).on("initialized.owl.carousel", carousel, function(event) {
                selectAsideElem();
            });

            $(document).on("translated.owl.carousel", carousel, function(event) {
                selectAsideElem();
                if (!!Application.Components.Main) {
                    Application.Components.Main.lazy.revalidate();
                }
            });



            $(document).on("prev.owl.carousel", carousel, function(event) {
                //selectAsideElem();
                /*var center = carousel.find(".active.center")
                 center.addClass("prev").removeClass("next");*/
            });
            $(document).on("next.owl.carousel", carousel, function(event) {
                //  selectAsideElem();
                /* var center = carousel.find(".active.center")
                 center.addClass("next").removeClass("prev");*/
            });
            
            $(document).on('click', '.main-item-slider .owl-item:not(.center) a', function(event){
                //carousel.trigger("to.owl.carousel", [$(this).parents(".owl-item").index()+2, 200, true]);
                //$(this).addClass("current");
                //return false;
            });
            
            
            function selectAsideElem () {
                var center = carousel.find(".active.center");
                center.removeClass("prev").removeClass("next");
                center.prev().addClass("prev").removeClass("next");
                center.prev().prev().addClass("prev").removeClass("next");
                center.prev().prev().prev().addClass("prev").removeClass("next");
                center.prev().prev().prev().prev().addClass("prev").removeClass("next");
                center.prev().prev().prev().prev().prev().addClass("prev").removeClass("next");
                center.next().addClass("next").removeClass("prev");
                center.next().next().addClass("next").removeClass("prev");
                center.next().next().next().addClass("next").removeClass("prev");
                center.next().next().next().next().addClass("next").removeClass("prev");
                center.next().next().next().next().next().addClass("next").removeClass("prev");
            }
        },
        
        runMapCarousel: function () {
            
            var carousel = $(".shops-slider__top");
            var carouselNav = $(".shops-slider__bottom");
            
            
            carousel.owlCarousel({
                items:1,
                margin:0,
                nav:true,
                navText: '',
                dots: false,
                autoWidth: false,
                responsiveRefreshRate: 1,
                center: false,
                loop:true,
                mouseDrag: true
            });
            
            carouselNav.owlCarousel({
                items:2,
                margin:7,
                nav:true,
                navText: '',
                dots: false,
                autoWidth: false,
                responsiveRefreshRate: 1,
                center: false,
                loop:false,
                mouseDrag: true,
                responsive:{
                    501:{
                        items:3
                    },
                    1023:{
                        items:4
                    }
                }
            });
            
            //nav start position select
            carouselNav.find(".owl-item").eq(0).addClass("current");
            
            //carousel is moved
            
            $(document).on("translate.owl.carousel", carousel, function(event) {
                var item = event.item.index;
                //console.log(item);
                var items = event.item.count;
                carouselNav.find(".current").removeClass("current");
                carouselNav.find(".owl-item").eq(item-1).addClass("current");
            });
            
            //add slides nav
            carouselNav.find(".owl-item").click(function(){
                carousel.find('.item').removeClass("current");
                $(this).addClass("current");
                carousel.trigger("to.owl.carousel", [$(this).closest(".owl-item").index(), 500, true]);
            });
            
            
            //when carousel is loaded
            carousel.on('initialize.owl.carousel', function () {
                // console.log("1 slider start load");
            });
            
            carousel.on('initialized.owl.carousel', function () {
                console.log("2 slider is loaded!!!");
                carousel.trigger('to.owl.carousel', [1,-1]);
            });
        },
        
        runAddDetailCarousel: function () {
            
            
            var carousel = $(".add-slider");
            
            $(carousel).owlCarousel({
                items:6,
                nav:true,
                navText: '',
                dots: false,
                autoWidth: false,
                responsiveRefreshRate: 1,
                center: false,
                margin:20,
                loop:true,
                mouseDrag: true,
                responsive:{
                    0:{
                        items:4
                    },
                    1100:{
                        items:5
                    },
                    1300:{
                        items:6
                    }
                    
                }
            });
            
            
            //nav start position select
            $(".main-slider-nav .item-box").eq(0).find(".item").addClass("active");
            
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
            
            $(document).on("translated.owl.carousel", carousel, function(event) {
                var item = event.item.index;
                // console.log(item);
                var items = event.item.count;
                $(".main-slider-nav").find(".active").removeClass("active");
                $(".main-slider-nav .item-box").eq(item-1).find(".item").addClass("active");
                
            });
            
            //add slides nav
            $('.main-slider-nav .image').click(function(){
                $('.main-slider-nav .item').removeClass("active");
                $(this).parent().addClass("active");
                carousel.trigger("to.owl.carousel", [$(this).closest(".item-box").index(), 500, true]);
                
                
            });
            
            
            //when carousel is loaded
            $(carousel).on('initialize.owl.carousel', function () {
                // console.log("1 slider start load");
            });
            
            $(carousel).on('initialized.owl.carousel', function () {
                // console.log("2 slider is loaded!!!");
                
            });
            
            
        },
    
        detailSimilarCarousel: function () {
            var carousel = $(".product-similar__slider");
    
            $(carousel).owlCarousel({
                items:2,
                nav:false,
                navText: '',
                dots: false,
                autoWidth: false,
                responsiveRefreshRate: 1,
                center: false,
                margin:50,
                loop:true,
                mouseDrag: true,
                responsive:{
                    500:{
                        items:3
                    },
                    1024:{
                        items:4
                    }
                }
            });
        },
    
        detailWatchedCarousel: function () {
            var carousel = $(".product-watched__slider");
    
            $(carousel).owlCarousel({
                items: 4,
                nav: false,
                navText: '',
                dots: false,
                autoWidth: false,
                responsiveRefreshRate: 1,
                center: false,
                margin: 20,
                loop: false,
                mouseDrag: true,
                responsive:{
                    768: {
                        item: 5,
                        margin: 85
                    },
                    1024:{
                        items:6
                    }
                }
            });
        },
    
        basketCarousel: function () {
            var carousel = $(".basket-slider__inner");
    
            $(carousel).owlCarousel({
                items: 2,
                nav: true,
                navText: '',
                dots: false,
                autoWidth: false,
                responsiveRefreshRate: 1,
                center: false,
                margin: 15,
                loop: true,
                mouseDrag: true,
                responsive:{
                    // 0:{
                    //     items: 2
                    // },
                    500:{
                        items:3,
                        margin: 30
                    },
                    768:{
                        items:2
                    },
                    1023:{
                        items:3,
                        margin: 50
                    },
                    1280:{
                        items:4,
                        margin: 85
                    }
                }
            });
        },
        
        iniNavTooltip: function() {
            
            var carousel=$(".promo-slider");
            
            carousel.each(function () {
                
                
                var slide = $(this).find(".slide");
                var nav = $(this).find(".owl-dot");
                
                
                nav.hover(
                    
                    /////////create tooltip
                    function() {
                        
                        
                        createPreview(this);
                        
                        
                        //////create actual prev or next image
                        
                        
                    }, function() {
                        ///////remove tooltip
                        removePreview(this);
                    }
                );
                
                
                
                
                nav.on("click", function () {
                    removePreview(this);
                    //createPreview(this);
                    
                });
                
                
                function createPreview (e) {
                    var index = $(e).index();
                    var image = slide.eq(index).css("background-image");
                    var text = slide.eq(index).data("description");
                    
                    console.log(index);
                    console.log(image);
                    
                    $(e).append( $(
                        "<div class='preview-block'>" +
                        "<div class='preview-img' style='background-image:"+image+"'></div>" +
                        "<div class='preview-text'>"+text+"</div>" +
                        "</div>" ) );
                    
                    $(e).find(".preview-block" ).fadeIn(300);
                    
                };
                
                function removePreview (e) {
                    $(e).find(".preview-block" ).fadeOut(300);
                    
                    setTimeout(function (){
                        $(e).find(".preview-block" ).remove();
                    },300);
                    
                };
                
                
            });
            
            
            
            
        },

        runDetailCarousel: function () {
            /* Swiper */

            if(!$('[data-detail-main]').length) {
                return;
            }

            if(!$('[data-detail-preview-items]').length) {
                $('.product-slider__arrow').hide();
                return;
            }

            var items = $('[data-detail-preview-items]');
            // var init = true;

            var zoomSize = (function () {
                if (window.innerWidth > 1400)
                    return 650;
                else if (window.innerWidth > 1279)
                    return 500;
                else
                    return 470;
            })();

            // WROOM-ZOOM-ZOOM-ZOOM (legacy)
            var elevateZoomOptions = {
                borderSize: 1,
                borderColour: '#DEDEDE',
                zoomWindowWidth: zoomSize,
                zoomWindowHeight: zoomSize,
                cursor: 'crosshair'
            };


            var previewSlider = new Swiper('[data-detail-preview]', {
                        slidesPerView: 5,
                        direction: 'vertical',
                        watchOverflow: true,
                        slideToClickedSlide: true,
                        centeredSlides: true,
                        initialSlide:0,
                        loop: true,
                        navigation: {
                            nextEl: '[data-detail-preview-next]',
                            prevEl: '[data-detail-preview-prev]'
                        },
                        /*on: {
                            'init': function(){
                                setTimeout(function () {
                                    setActivePreview(0)
                                }, 100)
                            }
                        },*/
                        on: {
                            'init': function () {
                                var index=items.length;
                                var block_parent;
                                if (index < 5){
                                    block_parent=items.parent();
                                    block_parent.addClass('disable');
                                }
                            },
                            'transitionEnd': function () {
                                if (!!Application.Components.Main) {
                                    Application.Components.Main.lazy.revalidate();
                                }
                            }
                        },
                        breakpoints: {
                            499: {
                                direction: 'horizontal',
                                slidesPerView: 4,
                                centeredSlides: false,
                                slideToClickedSlide: false,
                                initialSlide: 0
                            },
                            767: {
                                slidesPerView: 3
                            }
                        }
                    });

            var mainSlider = new Swiper('[data-detail-main]', {
                        slidesPerView: 1,
                        initialSlide: 0,
                        loop: true,
                        pagination: {
                            el: '.swiper-pagination',
                            type: 'bullets',

                        },
                        on: {
                            'transitionStart': function() {
                                setActivePreview(this.realIndex);
                            },
                            'transitionEnd': function() {
                                // if(!init)
                                //     setPreview(this.activeIndex);
                                //
                                // init = false;

                                hoverZoom();
                                // setActivePreview($('[data-detail-preview-items].swiper-slide-active').data('swiper-slide-index'));
                                if (!!Application.Components.Main) {
                                    Application.Components.Main.lazy.revalidate();
                                }
                            },
                            'init': function () {
                                previewItemClickHandler();

                                if(window.innerWidth < 1024)
                                    imgModal();
                            }
                        }
                    });

            function previewItemClickHandler() {
                $('[data-detail-preview-items]').on('click', function () {
                    var index = $(this).data('swiper-slide-index');
                    setMain(index);
                });
            }

            function setMain(index) {
                mainSlider.slideToLoop(index);
            }


            function hoverZoom() {
                var $activeSlide = $('[data-detail-main] .swiper-slide-active');

                $('.zoomContainer').remove();

                $activeSlide
                    .find('img')
                    .removeData('elevateZoom')
                    .removeData('zoomImage');

                if(window.innerWidth > 1023) {
                    var activeImg = $activeSlide.find('img');
                    activeImg.elevateZoom(elevateZoomOptions);
                }
            }

            function setActivePreview(index) {
                $('[data-detail-preview-items]').removeClass('active');
                $('[data-detail-preview-items][data-swiper-slide-index=' + index + ']').addClass('active');
            }

            function imgModal() {
                $(document).on('click', '[data-zoom-image]', function () {
                    var imgSrc = $(this).data('zoom-image');

                    $('#imagepreview').attr('src', imgSrc);
                    $('#imagemodal').modal('show');
                })
            }
        },

        /*Этот слайдер для раздела Одежда. Он должен срабатывать при наличии таких дата-атрибутах, как data-detail-preview-clothes и data-main-preview-clothes.*/
        runDetailClothesCarousel: function () {
            /*Swiper */
            if(!$('[data-detail-main-clothes]').length) {
                return;
            } else if(!$('[data-detail-preview-items]').length) {
                $('.product-slider__arrow').hide();
                return;
            }

            var items = $('[data-detail-preview-items]');
            // var init = true;

            var zoomSize = (function () {
                if (window.innerWidth > 1400)
                    return 650;
                else if (window.innerWidth > 1279)
                    return 500;
                else
                    return 470;
            })();

            // WROOM-ZOOM-ZOOM-ZOOM (legacy)
            var elevateZoomOptions = {
                borderSize: 1,
                borderColour: '#DEDEDE',
                zoomWindowWidth: zoomSize,
                zoomWindowHeight: zoomSize,
                cursor: 'crosshair'
            };


            var previewSlider = new Swiper('[data-detail-preview-clothes]', {
                slidesPerView: 5,
                direction: 'vertical',
                watchOverflow: true,
                slideToClickedSlide: true,
                centeredSlides: true,
                initialSlide:1,
                loop: true,
                navigation: {
                    nextEl: '[data-detail-preview-next]',
                    prevEl: '[data-detail-preview-prev]'
                },
                /*on: {
                    'init': function(){
                        setTimeout(function () {
                            setActivePreview(0)
                        }, 100)
                    }
                },*/
                on: {
                    'init': function () {
                        var index=items.length;
                        var block_parent;
                        if (index < 5){
                            block_parent=items.parent();
                            block_parent.addClass('disable');
                        }
                    },
                    'transitionEnd': function () {
                        if (!!Application.Components.Main) {
                            Application.Components.Main.lazy.revalidate();
                        }
                    },
                },
                breakpoints: {
                    499: {
                        direction: 'horizontal',
                        slidesPerView: 4,
                        centeredSlides: false,
                        slideToClickedSlide: false,
                        initialSlide: 0
                    },
                    767: {
                        slidesPerView: 3
                    }
                }
            });

            var mainSlider = new Swiper('[data-detail-main-clothes]', {
                slidesPerView: 1,
                initialSlide: 1,
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    type: 'bullets',

                },
                on: {
                    'transitionStart': function() {
                        setActivePreview(this.realIndex);
                    },
                    'transitionEnd': function() {
                        // if(!init)
                        //     setPreview(this.activeIndex);
                        //
                        // init = false;

                        hoverZoom();
                        if (!!Application.Components.Main) {
                            Application.Components.Main.lazy.revalidate();
                        }
                        // setActivePreview($('[data-detail-preview-items].swiper-slide-active').data('swiper-slide-index'));
                    },
                    'init': function () {
                        previewItemClickHandler();

                        if(window.innerWidth < 1024)
                            imgModal();
                    }
                }
            });
            function previewItemClickHandler() {
                $('[data-detail-preview-items]').on('click', function () {
                    var index = $(this).data('swiper-slide-index');
                    setMain(index);
                });
            }

            function setMain(index) {
                mainSlider.slideToLoop(index);
            }


            function hoverZoom() {
                var $activeSlide = $('[data-detail-main-clothes] .swiper-slide-active');

                $('.zoomContainer').remove();

                $activeSlide
                    .find('img')
                    .removeData('elevateZoom')
                    .removeData('zoomImage');

                if(window.innerWidth > 1023) {
                    var activeImg = $activeSlide.find('img');
                    activeImg.elevateZoom(elevateZoomOptions);
                }
            }

            function setActivePreview(index) {
                $('[data-detail-preview-items]').removeClass('active');
                $('[data-detail-preview-items][data-swiper-slide-index=' + index + ']').addClass('active');
            }

            function imgModal() {
                $(document).on('click', '[data-zoom-image]', function () {
                    var imgSrc = $(this).data('zoom-image');

                    $('#imagepreview').attr('src', imgSrc);
                    $('#imagemodal').modal('show');
                })
            }
        }

    };
    
    window.Application.addComponent("Sliders", Sliders);
});
