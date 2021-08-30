$(function () {

    var Main = {

        run: function () {

            this.footerMove();
            this.labelcheck();
            this.runMovedPanel();
            this.runReadMore();
            this.runStaticModals();
            this.runInputFile();
            this.runTofavorite();

            this.runRating();

            this.sertificateChoose();
            this.setAsideSize();
            this.mainFixed();

            $(".close-modal").on("click", function () {
                $('#side-login').modal('hide');
                BXMobileApp.UI.Slider.setState(BXMobileApp.UI.Slider.state.CENTER);
            });

            $('[data-hide]').click(function () {
                $el_id = $(this).attr("href");
                if ($($el_id).hasClass("active") || $('non-active').length > 1) {
                    $(this).closest("li").addClass('non-active');
                    $($el_id).removeClass("active");
                }
                else {
                    $(this).closest("li").removeClass('non-active');
                    $($el_id).addClass("active");
                }
            });

            this.cityAlfSelect();
            this.setMaskedInputs();
            this.initSortCatalog();
            this.initSortCatalog();
            this.addToBasketHandler();
            this.addToBasketSubscribePriceHandler();
            this.basketShowSizeHandler();
            this.editAddressHandler();
            this.subscribePriceHandlers();
            this.searchHandlers();

        },

        resizeFunctions: function () {
            this.setAsideSize();
            this.runPanelHeight();
        },

        scrollFunctions: function () {

        },

        loadFunctions: function () {
            this.runLabelHelpIcons();
        },

        footerMove: function () {
            var ua = navigator.userAgent.toLowerCase();
            var isOpera = (ua.indexOf('opera') > -1);
            var isIE = (!isOpera && ua.indexOf('msie') > -1);
            var viewportHeight = getViewportHeight();
            var wrapper = $("main");
            var footer = $("footer");

            function getViewportHeight() {
                return ((document.compatMode || isIE) && !isOpera)
                    ? (document.compatMode == 'CSS1Compat') ? document.documentElement.clientHeight : document.body.clientHeight : (document.parentWindow || document.defaultView).innerHeight;
            }

            wrapper.css("min-height", viewportHeight - footer.outerHeight(true));
        },

        runIeFix: function () {

            $(".cell-link").each(function () {
                var h = $(this).closest(".main-grid-cell").outerHeight();
                $(this).height(h);
            })

        },

        runSelect2: function () {
            $("select").select2();
        },

        mainFixed: function () {
            var $menu = $(".main-menu");
            var $menuFix = $(".fixed-menu-wrapper");
            $(window).scroll(function () {
                if ($(this).scrollTop() > 100 && !$menu.hasClass("fixed")) {
                    $menu.addClass("fixed");
                    $menuFix.addClass('active');
                    $menuFix.css('height', $menu.outerHeight());
                }
                else if ($(this).scrollTop() <= 100 && $menu.hasClass("fixed")) {
                    $menu.removeClass("fixed");
                    $menuFix.removeClass('active');
                }
            });
        },


        labelcheck: function () {

            var label = $(this).parents("label");
            label.addClass("active");

            $("input[type='checkbox']").on("change", function () {

                if ($(this).parent().hasClass("lost")) {
                    return false;
                }
                var label = $(this).parents("label");
                var name = $(this).attr("name");
                label.toggleClass("active");

            });


            var label = $(this).parents("label");
            label.addClass("active");

            $("input[type='radio']").on("change", function () {
                if ($(this).parent().hasClass("lost")) {
                    return false;
                }
                var label = $(this).parents("label");
                var name = $(this).attr("name");

                $('input[name=' + name + ']').parents("label").removeClass("active");
                label.addClass("active");

            });

        },

        runInputFile: function () {

            $(document).on("change", ".upload_file :file", function () {
                var file = this.files[0].name;
                $(document).find('.upload_file span.text').html(file);
            });
            $(document).on("click", "#chose_file", function () {
                $(document).find('.file_upl').click();
                return false;
            });

        },

        runHeaderFix: function () {


            var fix = $('header');

            $(window).scroll(function () {
                if ($(this).scrollTop() > 90) {
                    fix.addClass("fixed");


                    fix.css({
                        'left': -1 * $(this).scrollLeft()
                        //Why this 15, because in the CSS, we have set left 15, so as we scroll, we would want this to remain at 15px left
                    });

                } else {
                    fix.removeClass("fixed");
                    fix.css({
                        'left': 0
                    });
                }
            });


        },

        runPanelHeight: function () {
            var panel = $(".moved-panel");

            panel.each(function () {
                var h1 = $(this).height();
                var h2 = $(this).find(".moved-panel-head").outerHeight();
                //console.log(h2);
                $(this).find(".moved-panel-body").height(h1 - h2);
            });

        },

        getSearch: function () {
            var search = $(".search-line");

            $(".to-search").on("click", function () {

                search.toggleClass("open");
                if (!search.hasClass("open")) {
                    search.fadeOut(300);
                } else {
                    search.fadeIn(300);
                }
            });

            $(document).click(function (event) {

                // console.log($(event.target).closest(".top-search-wrap").length);

                if ($(event.target).closest(".top-search-wrap").length) return false;

                search.removeClass("open");
                //console.log(search);
                search.fadeOut(300);

                //indexClick = 0;
                //event.stopPropagation();
            });
        },

        runMovedPanel: function () {
            $("#content-container").append("<div class='main-overlay'></div>");
            var wrapper = $("#wrapper"),
                overlay = wrapper.find(".main-overlay"),
                container = wrapper.find("#content-container"),
                closebtn = $(".close-moved-pannel"),
                openpanel = $(".moved-panel.open"),
                btn = $('[data-type="getMovedPanel"]');
          btn.on("click", function () {
              if ($(this).hasClass('inactive') === false) {
                  //console.log(overlay);
                  //console.log("add");
                  //console.log("btn click");
                  var attr = $(this).data('product-id');
                  var e = $(this);
                  if (typeof attr !== typeof undefined && attr !== false && attr != ''
                      || (
                          typeof attr === typeof undefined || attr === false
                      )
                  ) {
                      target = $(e.attr("data-target"));
                      //console.log(target);
                      // $("#side-cart, #content-container, #wrapper").addClass("open");
                      $(".moved-panel.open").removeClass("open").removeClass("from-top");


                      target.addClass("open");
                      wrapper.addClass("open");
                      container.addClass("open");
                      if (target.hasClass("from-top")) {
                          wrapper.addClass("from-top");
                          container.addClass("from-top");
                      }
                      overlay.fadeIn(300);

                      Application.Components.Main.runPanelHeight();
                  }
                  else
                      $('#chooseSizeModal').modal('show');
              }
          });


            $(overlay).on("click", function () {
                //console.log("overlay click");
                if (wrapper.hasClass("open")) {
                    //$("#side-cart, #content-container, #wrapper").removeClass("open");
                    wrapper.removeClass("open");
                    container.removeClass("open");
                    target.removeClass("open");
                    wrapper.removeClass("from-top");
                    container.removeClass("from-top");
                    $(this).fadeOut(300);
                }
            });

            $(closebtn).on("click", function () {
                //console.log("closebtn click");
                if (wrapper.hasClass("open")) {
                    // $("#side-cart, #content-container, #wrapper").removeClass("open");
                    wrapper.removeClass("open");
                    container.removeClass("open");
                    target.removeClass("open");
                    wrapper.removeClass("from-top");
                    container.removeClass("from-top");
                    $(overlay).fadeOut(300);
                }
            });

        },

        setAsideSize: function () {
            var mainWrap = $(".right-aside");
            var w = mainWrap.parent().width();
            var w1 = $(".left-aside").outerWidth();
            /*
             console.log(w);
             console.log(w1);
             console.log(w-w1);
             */
            //console.log(mainWrap.parent());
            mainWrap.css("width", w - w1);


            var h = mainWrap.parent().height();
            mainWrap.css("min-height", h);

        },

        runScrollToTop: function () {

            $(function () {

                $("body").append("<div id='toTop'><i class='fa fa-chevron-up'></i></div>");

                $(window).scroll(function () {

                    if ($(this).scrollTop() != 0) {

                        $('#toTop').fadeIn();

                    } else {
                        $('#toTop').fadeOut();


                    }

                });

                $('#toTop').click(function () {

                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);

                });


            });

        },

        runStaticModals: function () {
            var modal = $(".action-modal-wrap");

            modal.each(function () {
                var e = $(this);
                var modalbody = $(this).find(".action-modal");
                var close = $(this).find(".close");

                e.find(".get-modal").on("click", function () {
                    if (e.hasClass("open")) {
                        modalbody.fadeOut(300);
                    } else {
                        modalbody.fadeIn(300);
                    }
                    e.toggleClass("open");
                    return false;
                });
                close.on("click", function () {
                    modalbody.fadeOut(300);
                    e.toggleClass("open");
                    return false;
                });
            });
        },

        runReadMore: function () {
            var container = $(".read-more-wrap");

            container.each(function () {

                var e = $(this),
                    h = e.find(".read-more-text").height(),
                    hs = e.find(".read-more-text-wrap").height();

                e.find(".read-more").on("click", function () {
                    if (e.hasClass("open")) {
                        e.find(".read-more-text-wrap").height(hs);
                        // console.log(hs);

                    } else {
                        e.find(".read-more-text-wrap").height(h);
                        //console.log(h);
                    }
                    e.toggleClass("open");
                    return false;
                });

            });

        },
        runTofavorite: function () {
            /*$(".to-favorite").on("click", function () {
             $(this).toggleClass("add-in-favorite")
             $(this).find("i").each(function (){
             if ($(this).hasClass("active")){
             $(this).fadeOut(300);
             }else{
             $(this).fadeIn(300);
             }
             });

             $(this).find("i").toggleClass("active");

             if(!$(this).hasClass("add-in-favorite")){
             return false;
             }
             });*/
        },

        runAccordions: function () {
            $('.panel-group').on('shown', function () {
                Application.Components.main.runPanelHeight();
            })
        },

        runLabelHelpIcons: function () {
            var icon = $(".label-icon");
            icon.each(function () {
                var e = $(this);
                var text = e.attr("data-text");
                if (text) {
                    e.append("<div class='label-icon-text'>" + text + "</div>");
                }
                e.hover(
                    function () {
                        $(this).find(".label-icon-text").stop().fadeIn(300);

                    }, function () {
                        $(this).find(".label-icon-text").stop().fadeOut(300);
                    }
                );

            });
        },
        sertificateChoose: function () {

            var item = $(".design-list li");
            var target = $(".sertificate-view");

            var select = $(".sertificate-form select");


            var value = select.val();
            target.find(".count").html(value);

            select.on("change", function () {
                value = $(this).val();
                target.find(".count").html(value);
            });

            item.on("click", function () {

                item.removeClass("active");
                $(this).addClass("active");

                var img_src = $(this).find('img').attr('src');
                var img_alt = $(this).find('img').attr('alt');

                //change big image in active thumb
                target.find("img").attr({'src': img_src, 'alt': img_alt});

            });
        },

        runRating: function () {
            $('.rating_stars.star').rating();
        },

        cityAlfSelect: function () {
            var $letters = $("#cityModal .word-box a");
            //console.log($letters);
            $letters.on("click", function () {
                var letter = $(this).data( "letter" );
                $letters.removeClass("active");
                $(this).addClass("active");
                $('.city-list-wrap .city-list').removeClass('active');
                $('.city-list-wrap .city-list[data-letter="'+letter+'"]').addClass('active');
            });
        },

        setMaskedInputs: function () {
            $('.mask-phone').inputmask("+7(999)-999-99-99");
            $('.mask-date').mask('99.99.9999',{placeholder:"дд.мм.гггг"});
            $('.mask-price').mask('999?999 руб.',{placeholder:" "});
        },

        initSortCatalog: function () {
            $(document).on("change", "#sort_catalog", function () {
                window.location = $(this).val();
            });
        },


        //добавление в корзину
        addToBasketHandler: function () {
            $(document).on('click', '.btn-tobasket', function() {
                //console.log('addToBasket');
                var product_id = $(this).data('product-id');
                var url_template = BX.message('BUY_URL');
                if(product_id != '') {
                    var obj = {product_id: product_id};
                    BX.ajax.loadJSON(
                        '/include/ajax_check_offer_inbasket.php',
                        obj,
                        function (res) {
                            //console.log(res);
                            if (res.HAS_OFFER == true){
                                $('#offerInbasketModal').modal('show');
                            }
                            else{
                                var buy_url = url_template.replace('#ID#', product_id) + '&ajax_basket=Y';
                                buy_url = buy_url.replace(/&amp;/g, '&');
                                BX.ajax.loadJSON(
                                    buy_url,
                                    '',
                                    function (res) {
                                        BX.onCustomEvent('OnBasketChange');
                                        $('#addToBasketModal').modal('show');
                                        /* $('.info-tobasket').fadeIn();
                                        setTimeout(function(){$('.info-tobasket').fadeOut();}, 3000); */
                                    }
                                );
                            }
                        },
                        function (res) {
                            console.log('error');
                        }
                    );
                }
                else{
                    $('#chooseSizeModal').modal('show');
                }
                return false;
            });
        },


        //добавление в корзину в листе ожиданий и в листе желаний
        addToBasketSubscribePriceHandler: function () {

            $(document).on('click', '.btn-tobasket-insubscribe, .btn-tobasket-infavorites', function() {
                var product_id = $(this).data('product-id');
                var url = $(this).attr('href');
                if((product_id != '')) {
                    //overlay.show();
                    BX.ajax.loadJSON(
                        url,
                        '',
                        function (res) {
                            BX.onCustomEvent('OnBasketChange');
                            $('#addCartMobile').modal('show');
                        }
                    );
                }
                return false;
            });
        },


        // Показ размеров в корзине
        basketShowSizeHandler: function () {
            $(document).on('click', '.change-size.inbasket', function() {
                var sizeblock = $(this).parent().find('.bx_size');
                sizeblock.slideToggle();
                return false;
            });
        },


        // Адреса доставки в личном кабинете
        editAddressHandler: function () {
            $(".editAddress").on('click', function () {
                $(this).parents('.address-cont').find('.edit-address-cont').slideToggle();
                return false;
            });

            $('body').on('click', ".edit-address-cont .save-link", function () {
                var link = $(this);
                var element_id = link.data('element-id');
                var location_id = link.parent('.edit-address-cont').find('input[name="LOCATION"]').val();
                var address = link.parent('.edit-address-cont').find('input[name="address"]').val();
                var selected = (link.parents('.address-cont').find('input[name="ch1"]').prop('checked') == true? '1':'0');
                var obj = {
                    TYPE: "EDIT_ADDRESS",
                    element_id: element_id,
                    location_id: location_id,
                    address: address,
                    selected: selected
                };
                $.post("/include/ajax_handler.php", obj)
                    .done(function (outData) {
                        var result = JSON.parse(outData);
                        if (result.status) {

                        }
                        else {
                            console.log("ОШИБКА! - " + result.error);
                        }
                    })
                    .complete(function () {
                        //overlay.hide();
                        location.reload();
                    });

                return false;
            });


            $('body').on('click', ".delete-address", function () {
                var link = $(this);
                var element_id = link.data('element-id');
                var obj = {
                    TYPE: "DELETE_ADDRESS",
                    element_id: element_id
                };
                $.post("/include/ajax_handler.php", obj)
                    .done(function (outData) {
                        var result = JSON.parse(outData);
                        if (result.status) {

                        }
                        else {
                            console.log("ОШИБКА! - " + result.error);
                        }
                    })
                    .complete(function () {
                        //overlay.hide();
                        location.reload();
                    });

                return false;
            });

            // ДОБАВЛЕНИЕ АДРЕСА
            $(".addAddress").on('click', function () {
                $('.add-address-cont').slideToggle();
                return false;
            });
            $('body').on('click', ".add-address-cont .save-link", function () {
                var link = $(this);
                var location_id = link.parent('.add-address-cont').find('input[name="LOCATION"]').val();
                var address = link.parent('.add-address-cont').find('input[name="address"]').val();
                var obj = {
                    TYPE: "ADD_ADDRESS",
                    location_id: location_id,
                    address: address
                };
                $.post("/include/ajax_handler.php", obj)
                    .done(function (outData) {
                        var result = JSON.parse(outData);
                        if (result.status) {

                        }
                        else {
                            console.log("ОШИБКА! - " + result.error);
                        }
                    })
                    .complete(function () {
                        //overlay.hide();
                        location.reload();
                    });

                return false;
            });


            $('body').on('change', ".address-cont input[name='ch1']", function () {
                var link = $(this);
                var element_id = link.data('element-id');
                var obj = {
                    TYPE: "SELECT_ADDRESS",
                    element_id: element_id
                };
                $.post("/include/ajax_handler.php", obj)
                    .done(function (outData) {
                        var result = JSON.parse(outData);
                        if (result.status) {

                        }
                        else {
                            console.log("ОШИБКА! - " + result.error);
                        }
                    })
                    .complete(function () {
                        //overlay.hide();
                        //location.reload();
                    });
            });
        },

        // Страница подписок на снижение цены
        subscribePriceHandlers: function () {
            $(document).on('click', '.del-subscribe', function() {
                var element_id = $(this).data('element-id');
                var obj = {
                    TYPE: "DEL_SUBSCRIBE_PRICE",
                    element_id: element_id
                };
                if (element_id != '') {
                    $.post("/include/ajax_handler.php", obj)
                        .done(function (outData) {
                            var result = JSON.parse(outData);
                            if (result.status) {

                            }
                            else {
                                console.log("ОШИБКА! - " + result.error);
                            }
                        })
                        .complete(function () {
                            location.reload();
                        });
                }

                return false;
            });
        },


        searchHandlers: function () {
            $(document).on('click', '.search-btn', function() {
                var input = $(this).parent().find('input');
                if (input.val().length > 0){
                    input.parent('form').submit();
                }
                else {

                }
            });
        }

    };

    // TODO: Remove `DopComponents` after fix hard coded dependency in retail rocket
    window.Application.addComponent(['Main', 'DopComponents'], Main);

});

$(document).ready(function(){
    $('.table-title').each(function() {
        $(this).next().hide();
    });
    $('.show-hide').each(function() {
        $(this).next().hide();
    });
    $('.table-title').click(function() {
        $(this).next().toggle();
    });
    $('.show-hide').click(function() {
        $(this).next().toggle();
    });
});