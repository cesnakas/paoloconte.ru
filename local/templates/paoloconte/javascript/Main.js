
$(function () {

    var Main = {

        run: function () {
            this.runScrollToTop();
            this.runHeaderFix();
            this.footerMove();
            this.labelcheck();
            this.runMovedPanel();
            this.runPanelHeight();
            this.getSearch();
            this.runReadMore();
            this.runStaticModals();
            this.runSelect2();
            this.runInputFile();
            this.runTofavorite();
            this.runImage360();
            this.runRating();
            this.gotoHref();
            this.sertificateChoose();
            this.RRSetEmail();
            this.mapListCollapse();
            this.initFancybox();
            this.cityAlfSelect();
            this.setMaskedInputs();
            this.fastViewModalHandler();
            this.addToBasketHandler();
            this.addToBasketSubscribePriceHandler();
            this.basketShowSizeHandler();
            this.subscribePriceHandlers();
            this.productReviewHandlers();
            this.uploadAvatar();
            this.editAddressHandler();

            this.toggleDefault();
            this.toggleSort();
            this.mMenu();
            this.catalogGridHover();
            this.catalogFilterToggle();
            this.mNav();
            this.mainParallax();
            this.footerSeo();
            this.topMenu();
            this.blockFixOnScroll();
            this.promocodeValid();

            window.objectFitImages();
        },

        resizeFunctions: function () {
            //this.setAsideSize();
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
        runImage360: function () {
            var productImage360 = $(".catalog_image_360");
            if(productImage360.length) {
                var source = productImage360.data('images').split(',');
                productImage360.spritespin({
                    source: source,
                    height: 665,
                    width: 498,
                    module: '360',
                    behavior: 'move',
                    renderer: 'background',
                    loop: true,
                    animate : true,
                    sense : -1,
                    frameTime : 95,
                    preloadCount: 30
                });
            }
        },

        runImage360_fastview: function () {
            var productImage360 = $(".catalog_image_360_fastview");
            if(productImage360.length) {
                var source = productImage360.data('images').split(',');
                productImage360.spritespin({
                    source: source,
                    width: 282,
                    height: 376,
                    module: '360',
                    behavior: 'move',
                    renderer: 'background',
                    loop: true,
                    animate : false,
                    sense : -1,
                    frameTime : 95,
                    preloadCount: 30
                });
            }
        },

        runRating: function () {
            $('.rating_stars.star').rating();
        },

        /**
         * прокрутка к элементам если ссылка начинается с #go_
         * у элемента к которому прокручиваем должен быть указан id равный ссылке
         * если у ссылки указать data атрибут data-click="true" то вместе с прокруткой произойдет клик по элементу
         * это удобно когда необходимо сделать прокрутку к не активному табу и активировать его
         * пример:
         * <a href="#go_c3" class="get-modal" data-click="true">Как узнать размер</a> - прокрутит до элемента и сделает клик
         * <a href="#c3" aria-controls="c3" role="tab" data-toggle="tab" id="go_c3">ТАБЛИЦА РАЗМЕРОВ</a>
         */
        gotoHref: function () {
            $(document).on("click", 'a[href^="#go_"]', function () {
                if($(this).hasClass("disabled"))
                    return false;

                var target = $(this).attr('href');
                var $fixedHeader = $(window).width() < 767 ? $('[data-m-fixed-header]') : $('[data-fixed-header]');
                var offsetFix = $fixedHeader.outerHeight() + 2;

                $('html, body').animate({
                    scrollTop: $(target).offset().top - offsetFix
                }, 500);

                if($(this).data('click') == true) {
                    $(target).click();
                }
                return false;
            });
        },

        runIeFix: function () {

            $(".cell-link").each(function () {
                var h = $(this).closest(".main-grid-cell").outerHeight();
                $(this).height(h);
            })

        },


        runSelect2: function () {
            $("select").select2({
                minimumResultsForSearch: -1
            });
            var $sizeSelects = $(".basket-item__cell.basket-item__cell--size select");
            if ($sizeSelects.length > 0) {
                $sizeSelects.each(function (ind, item) {
                    if ($(item).find('option').length == 1) {
                        $(item).closest('.basket-item__cell.basket-item__cell--size').find('.select-size').addClass('disabled');
                    }
                })
            }
        },

        labelcheck: function () {

            var label = $(this).parents("label");
            label.addClass("active");

            $("input[type='checkbox']").on("change", function () {
                var label = $(this).parents("label");
                var name = $(this).attr("name");

                label.toggleClass("active");

            });


            var label = $(this).parents("label");
            label.addClass("active");

            $("input[type='radio']").on("change", function () {
                var label = $(this).parents("label");
                var name = $(this).attr("name");

                $('input[name=' + name + ']').parents("label").removeClass("active");
                label.addClass("active");

            });

        },

        mapListCollapse: function () {
            var toggleBtn = $(".map-list a"),
                toggleBox = $(".map-link-list"),
                toggleBoxBody = toggleBox.find(".list-wrap"),
                menuHeight = $(".header-add").height(),
                cityConts = $('.city-cont');


            toggleBtn.on("click", function () {
                /*var city_id = $(this).data('city-id');
                 cityConts.hide();
                 cityConts.filter('[data-city-id="'+city_id+'"]').show();

                 var h = toggleBoxBody.height();
                 toggleBox.animate({height: h}, 200);
                 $('html, body').animate({
                 scrollTop: $(".map-link-list").offset().top-menuHeight
                 }, 1000);
                 return false;*/
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


            var elem = $('.header__inner');
            var top = $(this).scrollTop();

            if(top > 40){
                elem.css('top', 0);
            }

            $(window).scroll(function(){
                top = $(this).scrollTop();

                if (top+0 < 40) {
                    elem.css('top', (40-top));
                } else {
                    elem.css('top', 0);
                }
            });

            
            var $headerNav = window.innerWidth > 1023 ? $('.header-nav') : $('.header__inner');
            var navOfTop = window.innerWidth > 1023 ? $headerNav.offset().top : 0;
            // var $actions = $('.header__actions');
            // var $navActions = $('.header-nav__actions');

            setHeaderHeight();
            $(window).on('resize', setHeaderHeight);

            $(window).scroll(function () {

                if ($(window).scrollTop() > navOfTop) {
                    $headerNav.addClass("fixed");


                    // $headerInner.css({
                    //     'left': -1 * $(this).scrollLeft()
                    //     //Why this 15, because in the CSS, we have set left 15, so as we scroll, we would want this to remain at 15px left
                    // });

                } else {
                    $headerNav.removeClass("fixed");
                    // $headerInner.css({
                    //     'left': 0
                    // });
                }
            });

            function setHeaderHeight() {

                if(window.innerWidth < 768)
                    return;

                $('.header').height('auto');
                $('.header').height($('.header').height());
            }
        },


        runPanelHeight: function () {
            var panel = $(".moved-panel"),
                h1 = panel.height(),
                h2 = panel.find(".moved-panel-head").outerHeight(),
                h3 = panel.find(".moved-panel-footer").outerHeight();
            panel.find(".moved-panel-body").height(h1 - h2 - h3);
        },

        getSearch: function () {
            var search = $(".search-line");
            var $headerTop = $('.header__top');
            var $headerSearch = $('.header__search');

            $(".to-search").on("click", function () {
                var $this = $(this);
                var input = $this.prev().find('input');
                var $mask = $('.search-mask');

                if (input && input.val().length > 0){
                    input.parent('form').submit();
                }
                else {
                    var formRight = $headerTop.offset().left + $headerTop.innerWidth() - $headerSearch.offset().left - $headerSearch.innerWidth() - 6;
                    search.css('right',formRight);
                    search.toggleClass("open");

                    if($(window).width() < 500) {
                        $mask.toggle();
                        $('html').toggleClass('locked');
                    }

                    if (!search.hasClass("open")) {
                        search.fadeOut(300);
                    } else {
                        search.fadeIn(300)/*.find('input').focus()*/;
                    }
                }
            });

            $(document).click(function (event) {
                if ($(event.target).closest(".header__search").length)
                    return false;

                search.removeClass("open");
                search.fadeOut(300);
                $('html').removeClass('locked');

                if($(window).width() < 500)
                    $('.search-mask').hide();

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
                btn = $('[data-type="getMovedPanel"]');

            var target;

            $('body').on("click", '[data-type="getMovedPanel"]', function () {
                var attr = $(this).data('product-id');
                if (typeof attr !== typeof undefined && attr !== false && attr != ''
                    || (
                        typeof attr === typeof undefined || attr === false
                    )
                ) {
                    target = $($(this).attr("data-target"));
                    //$("#side-cart, #content-container, #wrapper").addClass("open");
                    wrapper.addClass("open");
                    container.addClass("open");
                    target.addClass("open");
                    overlay.fadeIn(300);

                    Application.Components.Main.runPanelHeight();
                }
            });


            $(overlay).on("click", function () {
                if (wrapper.hasClass("open")) {
                    //$("#side-cart, #content-container, #wrapper").removeClass("open");
                    wrapper.removeClass("open");
                    container.removeClass("open");
                    target.removeClass("open");
                    $(this).fadeOut(300);
                    Application.Components.Main.setAsideSize();
                }
            });

            $('body').on("click", '.close-moved-pannel', function () {
                if (wrapper.hasClass("open")) {
                    //$("#side-cart, #content-container, #wrapper").removeClass("open");
                    wrapper.removeClass("open");
                    container.removeClass("open");
                    target.removeClass("open");
                    $(overlay).fadeOut(300);
                    Application.Components.Main.setAsideSize();
                }
            });


        },

        setAsideSize: function () {
            var mainWrap = $(".right-aside");
            var w = mainWrap.parent().width();
            var w1 = $(".left-aside").outerWidth();

            mainWrap.css("width", w - w1);

            var h = mainWrap.parent().height();
            mainWrap.css("min-height", h);

        },


        runScrollToTop: function () {

            $(function () {

                $("body").append("<div id='toTop'></div>");

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

        hideZoomContainer: function () {
            $('.zoomContainer').addClass('hidden');
        },

        showZoomContainer: function () {
            $('.zoomContainer').removeClass('hidden');
        },

        runStaticModals: function () {
            var modal = $(".action-modal-wrap");
            var self = this;

            modal.each(function () {
                var e = $(this);
                var modalbody = $(this).find(".action-modal");
                var close = $(this).find(".close");

                if (e.hasClass('hover-mod')){
                    e.hover(
                        function() {
                            modalbody.stop().fadeIn(300);
                            e.toggleClass("open");
                            self.hideZoomContainer();
                        }, function() {
                            modalbody.stop().fadeOut(300);
                            e.toggleClass("open");
                            self.showZoomContainer();
                        }
                    );
                    /*
                     modalbody.hover(
                     function() {

                     }, function() {

                     modalbody.fadeOut(300);
                     e.toggleClass("open");
                     }

                     );
                     */
                }


                e.find(".get-modal").on("click", function () {
                    /*if (e.hasClass("open")) {
                        modalbody.fadeOut(300);
                        self.showZoomContainer();
                    } else {
                        modalbody.fadeIn(300);
                        self.hideZoomContainer();
                    }
                    e.toggleClass("open");
                    return false;*/

                    //e.preventDefault();

                    $('.modal-price').load('/include/ajax_subscribe_price_popup.php?ELEMENT_ID='+$(this).data('item-id'), function () {
                        $('#priceModal').modal('show');
                        Application.Components.Main.setMaskedInputs();
                    });
                });
                close.on("click", function () {
                    modalbody.fadeOut(300);
                    e.toggleClass("open");
                    self.showZoomContainer();
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

                    } else {
                        e.find(".read-more-text-wrap").height(h);
                    }
                    e.toggleClass("open");
                    return false;
                });

            });

        },
        runTofavorite: function () {
            /*$(".to-favorite").on("click", function () {
             $(this).toggleClass("add-in-favorite")
             $(this).find("i").each(function () {
             if ($(this).hasClass("active")) {
             $(this).fadeOut(300);
             } else {
             $(this).fadeIn(300);
             }
             });

             $(this).find("i").toggleClass("active");

             if (!$(this).hasClass("add-in-favorite")) {
             return false;
             }
             });*/
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
        RRSetEmail: function () {
            $('body').on('blur', 'input[name="EMAIL"], input[name="USER_EMAIL"], #ORDER_PROP_2', function(){
                var regex = /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
                if(regex.test($(this).val())) {
                    try {
                        rrApi.setEmail($(this).val());
                    }
                    catch(e){}
                }
            });
        },

        uploadAvatar: function () {
            $(document).on("click", "#choose_ava", function () {
                $(document).find('#file_upl_ava').click();
                return false;
            });

            var url = '/include/ajax_fileupload/fileupload.php';
            $('#file_upl_ava').fileupload({
                url: url,
                dataType: 'json',
                maxFileSize: 5000000,
                acceptFileTypes: '/^image\/(gif|jpeg|png)$/',
                //previewSourceFileTypes: '/^image\/(gif|jpeg|png)$/',
                loadImageFileTypes: '/^image\/(gif|jpeg|png)$/',
                done: function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        $('input[name="FILE_AVA"]').val(file.name);
                        var result_cont = $('.success_cont_ava');
                        var errors_cont = $('.errors_cont_ava');
                        $.ajax({
                            type: "POST",
                            url: "/include/ajax_handler.php",
                            data: {
                                TYPE: "CHANGE_AVA",
                                filename: file.name,
                                files_path: '/include/ajax_fileupload/files/'
                            },
                            success: function (data) {
                                var json = JSON.parse(data);
                                result_cont.html('');
                                errors_cont.html('');
                                if (json.status) {
                                    if (json.file_src != ''){
                                        $('.avatar.image').attr({
                                            "style": "background-image: url('" + json.file_src + "')"
                                        });
                                    }
                                }
                                else {
                                    //console.log("ОШИБКА! - " + result.error);
                                }
                                /*if (json.errors.length > 0) {
                                 for (var key in json.errors) {
                                 errors_cont.append('<p class="">' + json.errors[key] + '</p>');
                                 }
                                 errors_cont.hide().fadeIn();
                                 }
                                 else {
                                 for (var key in json.result) {
                                 //result_cont.append('<p class="">' + json.result[key] + '</p>');
                                 }
                                 //result_cont.hide().fadeIn();
                                 //form[0].reset();
                                 //window.location.reload();
                                 }*/
                            }
                        });
                    });
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                },
                fail: function (e, data) {
                    //console.log(data);
                    //console.log(data.errorThrown);
                    //console.log(data.textStatus);
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        },

        initFancybox: function () {
            // Detail page
            /*$(".owl-item:not(.cloned) a.fancybox-detail").fancybox({
             tpl: {
             closeBtn: '<a title="Закрыть" class="fancybox-item fancybox-close" href="javascript:;"></a>',
             next: '<a title="Следующая" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
             prev: '<a title="Предыдущая" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
             }
             });*/
        },

        cityAlfSelect: function () {
            var $letters = $("#cityModal .word-box a");
            $letters.on("click", function () {
                var letter = $(this).data( "letter" );
                $letters.removeClass("active");
                $(this).addClass("active");
                $('.city-list-wrap .city-list').removeClass('active');
                $('.city-list-wrap .city-list[data-letter="'+letter+'"]').addClass('active');
            });
        },

        setMaskedInputs: function () {
            $('.mask-phone').mask('+7 (999) 999-99-99');
            $('.mask-date').mask('99.99.9999',{placeholder:"дд.мм.гггг"});
            $('.mask-price').mask('999?999 руб.',{placeholder:" "});
            $('.mask-card').mask('9999-9999-9999-9');
            $.mask.definitions['~']='[37]';
            $('.mask-card-all').mask('~~~9-9999-9999-9');
        },

        fastViewLoad: function (ID, param) {
            if (ID) {
                var nameModal = '#fastViewModal';
                var modal = $(nameModal);
                var backUrl = window.location.href;
                modal.find('.modal-content').html("");
                var page = '/catalog/ajax_fastview.php?ELEMENT_ID='+ID;
                if (param != undefined && param != '') {
                    page = page + '&' + param;
                }else{
                    page = page + '&BACK_URL='+backUrl;
                }
                modal.find('.modal-content').load(page, function () {
                    Application.Components.Main.runStaticModals();
                    Application.Components.Main.runSelect2();
                    Application.Components.Main.labelcheck();
                    Application.Components.Main.runLabelHelpIcons();
                    Application.Components.Sliders.runDetailCarousel();
                    Application.Components.Main.runImage360_fastview();
                    Application.Components.Main.setMaskedInputs();
                    $('input[type=radio].star').rating();
                });
            }
        },

        fastViewModalOpen: function (ID, param) {
            if (!param) {
                var param = '';
            }
            this.fastViewLoad(ID, param);
            $('#fastViewModal').modal('show');
        },

        fastViewModalHandler: function () {
            var self = this;
            $(document).on('click', ".fastViewButton", function () {
                self.fastViewLoad($(this).data('item-id'));
            });
        },

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
            $("body").on('click', '.addAddress', function () {
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

        toggleDefault: function() {
            var $allBtns = $('[data-toggle-btn-m], [data-toggle-btn]');
            var $btn = $(window).width() < 767 ? $allBtns : $('[data-toggle-btn]');

            $allBtns.off('click.toggle');
            $btn.on('click.toggle', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var $this = $(this);
                var $wrap = $this.parents('[data-toggle-wrap]:first');
                var $btn = $this.parent().children('[data-toggle-btn], [data-toggle-btn-m]');
                var $menu  = $wrap.find('[data-toggle-list]:first');

                if ($wrap.hasClass("active")) {
                    $wrap.removeClass("active");
                    $this.removeClass("active");
                    $menu.slideUp();
                } else {
                    $wrap.addClass("active");
                    $btn.addClass("active");
                    $menu.slideDown();
                }

                $(document).mouseup('click', function (e) {
                    if (!$btn.is(e.target) && !$menu.is(e.target) && $menu.has(e.target).length === 0) {
                        var $plus = $btn.find('.plus, .check');
                        if ($plus.length > 0 && $plus.is(e.target)) {
                            return;
                        }
                        $wrap.removeClass("active");
                        $btn.removeClass("active");
                        $menu.slideUp();
                    }
                });
            });


        },

        toggleSort: function() {
            var $btn = $('[data-s-toggle-btn]');

            if(!$btn)
                return;

            $btn.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var $this = $(this);
                var $wrap = $this.parents('[data-s-toggle-wrap]');
                $wrap.toggleClass("active");
                $this.toggleClass("active");
                $wrap.find('[data-s-toggle-list]').slideToggle();

                $(document).on('click.sort', function(e) {

                    if(!$(e.target).parents('.page-top-sort').length) {
                        $wrap.removeClass("active");
                        $this.removeClass("active");
                        $wrap.find('[data-s-toggle-list]').slideToggle();

                        $(document).off('click.sort');
                    }
                });
            });
        },

        catalogGridHover: function () {
            var $items = $('.c-g-item');

            $items.mouseenter(function () {
                var $this = $(this);
                var $shadow = $this.find('.c-g-item__shadow');

                var overlayHeight = $this.find('.c-g-item__overlay').innerHeight() * -1;
                $shadow.css('bottom', overlayHeight);
            })
        },

        catalogFilterToggle: function () {
            var $btn = $('.page-top-filter__inner');
            var $aside = $('.aside');
            var $sidebar = $aside.find('.aside__sidebar');
            var $catalog = $('.c-g');
            var $catalogTop = $('.c-g-top')

            $btn.on('click', function () {
                if($(window).width() < 1024)
                    return;

                var $this = $(this);

                if ($(this).hasClass("active"))
                {
                    Cookies.set('BITRIX_SM_SECTION_FILTER_CONDITION', 'HIDE', { expires: 1 });
                }
                else
                {
                    Cookies.set('BITRIX_SM_SECTION_FILTER_CONDITION', 'SHOW', { expires: 1 });
                }
                $this.toggleClass('active');
                $aside.toggleClass('active');
                $sidebar.toggle();
                $catalogTop.toggleClass('active');
                //$catalog.toggleClass('c-g--small')
            })
        },

        mNav: function () {
            $(document).on('click','.aside-nav__link.active', function (e) {
                return;

                if($(window).width > 767)
                    return;
                e.preventDefault();
                $(this).toggleClass('opened');
                $('.aside-nav__link:not(.active)').toggle();
            })
        },

        mainParallax: function () { /* TODO ! */
            var $sliderWrap = $('.main-slider-wrap');
            var $slider = $('.main-slider');

            if(!$slider.length)
                return;

            $(window).on('load resize', setSliderHeight);

            $(window).on('scroll', function () {
                if($(window).width() < 1024) {
                    $slider.css({top: 0});
                    return;
                }

                var headerHeight = $('.header-nav').innerHeight();
                var lock = $(window).scrollTop() > $sliderWrap.offset().top - headerHeight;

                if(!$slider.hasClass('fixed') && lock) {
                    $slider.addClass('fixed').css({top: headerHeight});
                } else if(!lock) {
                    $slider.removeClass('fixed');
                } else {
                    return false;
                }
            });

            function setSliderHeight() {
                $sliderWrap.height('auto');
                $sliderWrap.height($slider.innerHeight());
            }
        },

        footerSeo: function () {
            if($(window).width() > 767)
                return;

            var $seo = $('.footer-seo__inner');
            var initHeight = $seo.innerHeight() + 15;
            var $btn = $('.footer-seo__btn');

            if(initHeight > 150) {
                hideSeo();
                $seo.addClass('big');
            }

            $btn.on('click', function(){
                $seo.toggleClass('active');

                $seo.hasClass('active') ? showSeo() : hideSeo();
            });

            function hideSeo() {
                $seo.css('maxHeight','150px')
            }
            function showSeo() {
                $seo.css('maxHeight',initHeight)
            }
        },

        topMenu: function () {

            var $item = $('.top-menu__item');

            $item.each(function () {
                var $this = $(this);
                var $dropdown = $this.find('.top-menu__dropdown');

                $this.on('mouseenter', function () {
                    $dropdown.show();
                //    calcPosition($dropdown);
                    $this.addClass('active');
                    $dropdown.animate({opacity: 1}, 100);
                });
                $this.on('mouseleave', function () {
                    $this.removeClass('active');
                    $dropdown.animate({opacity: 0},100).hide();
                })
            });

            /*
            function calcPosition($dropdown) {
                var itemLeft = $dropdown.offset().left;
                var itemWidth = $dropdown.innerWidth();
                var windowWidth = $(window).innerWidth();

                if(itemLeft + itemWidth > windowWidth) {
                    $dropdown.css('left',(itemLeft - (windowWidth - itemWidth)) * -1);
                }
            }

            /*$('.top-menu').menuAim({
                activate: activateSubmenu,
                deactivate: deactivateSubmenu,
                exitMenu: exitMenu
            });

            function activateSubmenu(row) {
                $(row).addClass('active');
                $(row).find('.top-menu__dropdown').fadeIn(100);
            }

            function deactivateSubmenu(row) {
                $(row).removeClass('active');
                $(row).find('.top-menu__dropdown').fadeOut(100);
            }

            function exitMenu() {
                $('.top-menu__item').removeClass('active');
                return true
            }*/
        },

        blockFixOnScroll: function() {
            var $item = $('[data-fix-item]:visible');
            if(!$item.length || window.innerWidth < 1024)
                return;
            var $sidebar = $('[data-fix-sidebar]');
            var headerMargin; //отступ от закрепленной шапки
            var headerHeight; //высота закрепленной шапки
            var sidebarHeight; //высота сайдбара
            var itemHeight; //высота элемента
            var sidebarOffset; //начало сайдбара
            var posStop; //точка остановки

            customScroll($item, $sidebar, headerMargin, headerHeight, sidebarHeight, itemHeight, sidebarOffset, posStop);
            setEventListeners($item, $sidebar, headerMargin, headerHeight, sidebarHeight, itemHeight, sidebarOffset, posStop);

            function customScroll($item, $sidebar, headerMargin, headerHeight, sidebarHeight, itemHeight, sidebarOffset, posStop) {
                $item = $('[data-fix-item]:visible');
                $sidebar = $('[data-fix-sidebar]');
                if (!$item || !$item.length) {
                    return;
                }
                var windowOffsetTop = $(window).scrollTop();
                headerMargin = $item.data('fix-item') || 0; //отступ от закрепленной шапки
                headerHeight = $('[data-fixed-header]').height(); //высота закрепленной шапки
                sidebarHeight = $sidebar.innerHeight(); //высота сайдбара
                itemHeight = $item.innerHeight(); //высота элемента
                sidebarOffset = $sidebar.offset().top; //начало сайдбара
                posStop = $sidebar.offset().top + sidebarHeight - itemHeight - headerHeight - headerMargin;
                /* факсация в конце род. блока */
                if(windowOffsetTop >= posStop) {
                    $sidebar.addClass('active');
                }
                /* факсация к шапке */
                else if(windowOffsetTop > sidebarOffset - headerHeight - headerMargin) {
                    $item.addClass('scroll').css({
                        'top': headerHeight + headerMargin
                    });
                    $sidebar.removeClass('active')
                } else {
                    $item.removeClass('scroll');
                    $item.css({
                        'top':'0'
                    });
                    $sidebar.removeClass('active');
                }
            }
            function setEventListeners($item, $sidebar, headerMargin, headerHeight, sidebarHeight, itemHeight, sidebarOffset, posStop) {
                window.addEventListener('resize', function () {customScroll($item, $sidebar, headerMargin, headerHeight, sidebarHeight, itemHeight, sidebarOffset, posStop)});
                window.addEventListener('scroll', function () {customScroll($item, $sidebar, headerMargin, headerHeight, sidebarHeight, itemHeight, sidebarOffset, posStop)});
            }

        },

        promocodeValid: function() {
            if ($('[data-promocode]').length > 0) {
                var $promocode = $('[data-promocode]');
                var promocodeLenght = $promocode.length - 1;
                var procomodeValue = $($promocode[promocodeLenght]).data('promocode').toString();
                var $promocodeContainer = $promocode.closest('.basket-coupon__item');
                var $form = $('.basket-coupon__form');
                var $input = $form.find('input');
                if ($promocodeContainer.hasClass('basket-coupon__item--error')) {
                    $input[0].value = procomodeValue;
                }
                if ($('.last-promocode-error').length > 0) {
                    $form.addClass('error');
                } else {
                    $form.removeClass('error');
                }
                $input.on('focus', function () {
                    $form.removeClass('error');
                })
            }
        },

        mMenu: function () {
            var $links = $('[data-menu-link]');
            var $html = $('html');
            var $closeBtns = $('[data-menu-mask], [data-menu-close]');
            var $mask = $('[data-menu-mask]');
            var $header = $('.header');
            var $footer = $('.footer');
            var $main = $('main');
            var $asideContainer = $('.aside.aside--catalog');

            $links.on('click', function (e) {
                e.stopPropagation();
                if($(window).width() > 1023)
                    return;

                var $this = $(this);
                var link = $this.data('menu-link');

                $('[data-menu=' + link + ']').addClass('active');
                if ($asideContainer && $asideContainer.find('.aside__sidebar')) {
                    $asideContainer.addClass("active");
                    $asideContainer.find('.aside__sidebar').show();
                }
                $mask.delay(400).fadeIn();
                $header.css('filter', 'blur(3px)');
                $footer.css('filter', 'blur(3px)');
                if (link == 'filter') {
                    $main.find('.page-top, .aside__main, .title-page').css('filter', 'blur(3px)');
                } else {
                    $main.find('.page-top, > div.container').css('filter', 'blur(3px)');
                }
                $html.addClass('locked');

                $closeBtns.one('click', function () {
                    $('[data-menu].active').removeClass('active');
                    if ($asideContainer && $asideContainer.find('.aside__sidebar')) {
                        $asideContainer.removeClass("active");
                        $asideContainer.find('.aside__sidebar').hide();
                    }
                    $mask.hide();
                    $this.removeClass('active')
                    $header.css('filter', 'none');
                    $footer.css('filter', 'none');
                    if (link == 'filter') {
                        $main.find('.page-top, .aside__main, .title-page').css('filter', 'none');
                    } else {
                        $main.find('.page-top, > div.container').css('filter', 'none');
                    }
                    $html.removeClass('locked');
                })
            });
        },

        isAddToBasketHandlerRunned: false,
        addToBasketHandler: function () {

            $(document).on('click', '.btn-tobasket', function() {
                if (Main.isAddToBasketHandlerRunned) {
                    return;
                }
                Main.isAddToBasketHandlerRunned = true;

                var self = this;
                var product_id = $(this).data('product-id');
                var product_name = $(this).data('product-name');
                var product_price = $(this).data('product-price');
                var url_template = BX.message('BUY_URL');

                var reload_page = false;

                if ($(this).hasClass('reload_page_2_basket')) {
                    reload_page = true;
                }

                if (product_id !== '') {
                    var obj = {product_id: product_id};

                    BX.ajax.loadJSON(
                        '/include/ajax_check_offer_inbasket.php',
                        obj,

                        function (res) {
                            var basketResponse = res;

                            if (res.HAS_OFFER === true) {
                                Main.isAddToBasketHandlerRunned = false;
                                overlay.hide();
                                $('#offerInbasketModal').modal('show');
                            } else {
                                var buy_url = url_template.replace('#ID#', product_id) + '&ajax_basket=Y';
                                buy_url = buy_url.replace(/&amp;/g, '&');
                                overlay.show();
                                window.dataLayer = window.dataLayer || [];

                                dataLayer.push({
                                    "event": "addToCart",
                                    "ecommerce": {
                                        "add": {
                                            "products": [
                                                {
                                                    "id": product_id,
                                                    "name": product_name,
                                                    "price": product_price,
                                                    "quantity": 1
                                                }
                                            ]
                                        }
                                    }
                                });

                                BX.ajax.loadJSON(
                                    buy_url,
                                    '',

                                    function (res) {
                                        Main.isAddToBasketHandlerRunned = false;
                                        if (reload_page) {
                                            location.reload();
                                        } else {
                                            BX.onCustomEvent('OnBasketChange');
                                            overlay.hide();
                                            OnModal(self, basketResponse);
                                        }
                                    }
                                );
                            }
                        },
                        function (res) {
                            Main.isAddToBasketHandlerRunned = false;
                            console.log('error');
                        }
                    );
                }
                else{
                    Main.isAddToBasketHandlerRunned = false;
                    $('#chooseSizeModal').modal('show');
                }
                return false;
            });
        },

        //добавление в корзину в листе ожиданий
        addToBasketSubscribePriceHandler: function () {
            $(document).on('click', '.btn-tobasket-insubscribe, .btn-tobasket-infavorites', function() {
                var product_id = $(this).data('product-id');
                var url = $(this).attr('href');
                if(product_id != '') {
                    overlay.show();
                    BX.ajax.loadJSON(
                        url,
                        '',
                        function (res) {
                            BX.onCustomEvent('OnBasketChange');
                            overlay.hide();
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

        // Страница подписок на снижение цены
        subscribePriceHandlers: function () {
            $(document).on('click', '.del-subscribe', function() {
                var element_id = $(this).data('element-id');
                var obj = {
                    TYPE: "DEL_SUBSCRIBE_PRICE",
                    element_id: element_id
                };
                if (element_id != '') {
                    overlay.show();
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
                }

                return false;
            });


            // Грузим модалку аяксом
            $("[data-target='#priceModal']").on('click', function (e) {
                e.preventDefault();

                $('.modal-price').load('/include/ajax_subscribe_price_popup.php?ELEMENT_ID='+$(this).data('item-id'), function () {
                    $('#priceModal').modal('show');
                    Application.Components.Main.setMaskedInputs();
                });
            });

            // Грузим модалку аяксом (Размеры)
            $("[data-target='#sizeModal']").on('click', function (e) {
                e.preventDefault();

                $('.modal-size').load('/include/ajax_no_size_popup.php?ELEMENT_ID='+$(this).data('item-id')+'&COLOR_ID='+$(this).data('color-id'), function () {
                    $('#sizeModal').modal('show');
                    Application.Components.Main.setMaskedInputs();
                });
            });
        },


        productReviewHandlers: function () {
            // Грузим модалку аяксом
            $(".addProductReviewLink").on('click', function () {
                var modal = $('.productReviewModal');
                var backUrl = window.location.href;
                modal.find('.modal-content').html("").load('/include/ajax_product_review_popup.php?ELEMENT_ID='+$(this).data('item-id') + '&ELEMENT_NAME='+$(this).data('item-name') + '&ELEMENT_LINK='+$(this).data('item-link') + '&ELEMENT_IMAGE='+$(this).data('item-src')+'&BACK_URL='+backUrl, function () {
                    $('input[type=radio].star').rating();
                });
            });
        },

        lazy: new Blazy({
            src: 'data-src',
            selector: '.lazy',
            successClass: 'init',
            offset: 100,
        }),

    };

    // TODO: Remove `DopComponents` after fix hard coded dependency in retail rocket
    window.Application.addComponent(['Main', 'DopComponents'], Main);

});

overlay = {
    elementClass:'ajax_overlay',
    showTime:200,
    hideTime:500,
    show:function(){
        this.append();
        $(document).find('.'+this.elementClass).fadeIn(this.showTime);
    },
    hide:function(){
        var that = this;
        $(document).find('.'+this.elementClass).fadeOut(this.hideTime);
        setTimeout(function(){
            $(document).find('.'+that.elementClass).remove();
        }, that.hideTime);
    },
    append:function(){
        $(document).find('.'+this.elementClass).remove();
        $('body').append('<div class="'+this.elementClass+'"></div>');
    }
};

/**
 * прокрутка к элементу target, если необходим клик по элементу то указать второй параметр true
 * использование window.scrollTo('#go_c3') прокрутит до элемента
 * использование window.scrollTo('#go_c3', true) прокрутит до элемента и сделает клик
 * @param target
 * @param click
 */
window.scrollTo = function(target, click){
    if (target != 0) {
        if (click == true)
            $(target).click();
        $('html, body').animate({
            scrollTop: $(target).offset().top - 60
        }, 500);
    }
}

/*$(document).on("ready", function () {

 var wrap2lvl = $(".top-menu").find(".lvl2-wrap");

 $(".top-menu > li").hover(
 function () {
 wrap2lvl.each(function () {
 console.log($(this).closest("li").position().left)
 var left = $(this).closest("li").position().left;
 $(this).css("left", left + "px");
 });
 },
 function () {
 wrap2lvl.each(function () {
 $(this).css("left", -99999 + "px");
 });
 }
 );
 });*/

function OnModal(target, result){

    $('#idSale').removeAttr('initialized');
    $('#idSaleInteres').removeAttr('initialized');

    $.ajax({
        url: '/include/ajax_basket_offer_id.php',

        error: function(jqXHR, textStatus, errorThrown){

        },

        success: function(data){
            $('#idSale').attr('data-product-ids', data);
            $('#idSaleInteres').attr('data-product-ids', data);
            retailrocket.markup.render();
        }
    });

    var windowOverlay = $('.modal-overlay'),
        windowContainer = $('.modal-window');

    var imgSrc = $('.product-slider-small .active img').data('src');

    if (imgSrc == null && target != null) {
        if(target.closest('.item') != null){
            imgSrc = target.closest('.item').querySelector('.image img').src;
        }
    }
    if(!imgSrc){
        imgSrc = $('.product-slider-small [data-src-first] img').attr('src');
    }

    $('#basket-modal-wrap_img').attr('src', imgSrc);

    windowOverlay.fadeIn(300);
    windowContainer.fadeIn(600);



    var size = result.ITEM.RAZMER;
    if (size !== false) {
        $('#basket-modal-wrap_size-title').show();
        $('#basket-modal-wrap_size').text(size);
    } else {
        $('#basket-modal-wrap_size-title').hide();
    }

    var color = result.ITEM.TSVET_MARKETING;
    if (color !== false) {
        $('#basket-modal-wrap_color-title').show();
        $('#basket-modal-wrap_color').text(color);
    } else {
        $('#basket-modal-wrap_color-title').hide();
    }

}

function OnModal2(size, color, imgSrc) {
    $('#idSale').removeAttr('initialized');
    $('#idSaleInteres').removeAttr('initialized');

    var windowOverlay = $('.modal-overlay'),
        windowContainer = $('.modal-window');

    $('#basket-modal-wrap_img').attr('src', imgSrc);

    windowOverlay.fadeIn(300);
    windowContainer.fadeIn(600);

    if (size !== false) {
        $('#basket-modal-wrap_size-title').show();
        $('#basket-modal-wrap_size').text(size);
    } else {
        $('#basket-modal-wrap_size-title').hide();
    }

    if (color !== false) {
        $('#basket-modal-wrap_color-title').show();
        $('#basket-modal-wrap_color').text(color);
    } else {
        $('#basket-modal-wrap_color-title').hide();
    }
}

$(document).on('click', '.window-modal_close', function(){
    var windowOverlay = $('.modal-overlay'),
        windowContainer = $('.modal-window');

    windowOverlay.fadeOut(600);
    windowContainer.fadeOut(300);
});
$(document).on('click', '.close.close-moved-pannel', function(){
    var windowOverlay = $('.modal-overlay'),
        windowContainer = $('.modal-window');

    windowOverlay.fadeOut(600);
    windowContainer.fadeOut(300);


});

$(document).on('click', '.modal-overlay', function(){
    $('.modal-overlay').hide();
});

$(document).on('change', "select[name='map-list']", '', function () {
    window.location.href = $(this).children(":selected").val();
});

$(document).on('ready',  function() {
    if (window.frameCacheVars !== undefined) {
        BX.addCustomEvent("onFrameDataReceived" , function(json) {
            Application.Components.Main.lazy.revalidate();
        });
    } else {
        BX.ready(function() {
            Application.Components.Main.lazy.revalidate();
        });
    }
});
