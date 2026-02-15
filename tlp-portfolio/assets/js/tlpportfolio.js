(function ($, window) {

    window.pfpFixLazyLoadToAll = function () {
        $('.tlp-portfolio-container').each(function () {
            // jetpack Lazy load
            $(this).find('img.jetpack-lazy-image:not(.jetpack-lazy-image--handled)').each(function () {
                $(this).addClass('jetpack-lazy-image--handled').removeAttr('srcset').removeAttr('data-lazy-src').attr('data-lazy-loaded', 1);
            });

            //
            $(this).find('img.lazyload').each(function () {
                var src = $(this).attr('data-src') || '';
                if (src) {
                    $(this).attr('src', src).removeClass('lazyload').addClass('lazyloaded');
                }
            });

            $(this).find("img[data-lazy-src]:not(.lazyloaded)").each(function () {
                $imgUrl = $(this).data("lazy-src");
                $(this).attr('src', $imgUrl).addClass('lazyloaded');
            });
        });
    };

    window.pfpFixLazyLoad = function (container) {
        if (container === undefined)
            return;

        // jetpack Lazy load
        container.find('img.jetpack-lazy-image:not(.jetpack-lazy-image--handled)').each(function () {
            $(this).addClass('jetpack-lazy-image--handled').removeAttr('srcset').removeAttr('data-lazy-src').attr('data-lazy-loaded', 1);
        });

        //
        container.find('img.lazyload').each(function () {
            var src = $(this).attr('data-src') || '';
            if (src) {
                $(this).attr('src', src).removeClass('lazyload').addClass('lazyloaded');
            }
        });

        container.find("img[data-lazy-src]:not(.lazyloaded)").each(function () {
            var imgUrl = $(this).data("lazy-src");
            $(this).attr('src', imgUrl).addClass('lazyloaded');
        });
    };

    // window.pfpOverlayIconResize = function () {
        // $('.tlp-item').each(function () {
        //     var holder_height = $(this).height();
        //     var a_height = $(this).find('.tlp-overlay .link-icon').height();
        //     var h = (holder_height - a_height) / 2;
        //     $(this).find('.link-icon').css('margin-top', h + 'px');
        // });
    // };

    window.initTlpPortfolio = function () {
        $(".tlp-portfolio-container").each(function () {
            var container = $(this),
                isIsotope = container.hasClass("is-isotope"),
                isCarousel = container.find('is-carousel');
            pfpFixLazyLoad(container);
            setTimeout(function () {
                container.imagesLoaded().progress(function (instance, image) {
                    container.trigger('pfp_image_loading');
                }).done(function (instance) {
                    container.trigger('pfp_item_before_load');
                    if (isIsotope) {
                        var isoHolder = container.find('.tlp-portfolio-isotope');
                        if (isoHolder.length) {
                            isoHolder.isotope({
                                itemSelector: '.tlp-isotope-item',
                            });
                            container.trigger('pfp_item_after_load');
                            setTimeout(function () {
                                isoHolder.isotope();
                            }, 10);
                            var $isotopeButtonGroup = container.find('.tlp-portfolio-isotope-button');
                            $isotopeButtonGroup.on('click', 'button', function (e) {
                                e.preventDefault();
                                var filterValue = $(this).attr('data-filter');
                                isoHolder.isotope({filter: filterValue});
                                $(this).parent().find('.selected').removeClass('selected');
                                $(this).addClass('selected');
                            });
                        }
                    }
                    setTimeout(function () {
                        $(document).trigger("pfp_loaded");
                    }, 10);
                });
            }, 10);
        });
    };

    window.initPfpMagicPopup = function () {
        if ($.fn.magnificPopup) {
            $('.tlp-portfolio-container').each(function () {
                $(this).magnificPopup({
                    delegate: '.tlp-zoom',
                    type: 'image',
                    preload: [1, 3],
                    gallery: {
                        enabled: true
                    }
                });
            });
        }
    };

    window.initRtppCaroselPortfolio =  function(){
        $('.is-carousel').each(function () {
            var container = $(this);
            // id = $.trim(container.attr('id')),
            var caro = container.find('.pfp-carousel');
            if (caro.length) {
                var items = caro.data('items'),
                    loop = caro.data('loop'),
                    nav = caro.data('nav'),
                    dots = caro.data('dots'),
                    autoplay = caro.data('autoplay'),
                    autoPlayHoverPause = caro.data('autoplay-hover-pause'),
                    autoPlayTimeOut = caro.data('autoplay-timeout'),
                    autoHeight = caro.data('autoheight'),
                    lazyLoad = caro.data('lazyload'),
                    rtl = caro.data('rtl'),
                    desktopcolumn = caro.data('desktopcolumn'),
                    tabcolumn = caro.data('tabcolumn'),
                    mobilecolumn = caro.data('mobilecolumn'),
                    smartSpeed = caro.data('smart-speed');
                caro.owlCarousel({
                    items: items ? items : desktopcolumn,
                    loop: loop ? true : false,
                    nav: nav ? true : false,
                    dots: dots ? true : false,
                    navText: ["<i class=\'demo-icon icon-left-open\'></i>", "<i class=\'demo-icon icon-right-open\'></i>"],
                    autoplay: autoplay ? true : false,
                    autoplayHoverPause: autoPlayHoverPause ? true : false,
                    autoplayTimeout: autoPlayTimeOut ? autoPlayTimeOut : 5000,
                    smartSpeed: smartSpeed ? smartSpeed : 250,
                    autoHeight: autoHeight ? true : false,
                    lazyLoad: lazyLoad ? true : false,
                    rtl: rtl ? true : false,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: mobilecolumn ? mobilecolumn : 1
                        },
                        600: {
                            items: tabcolumn ? tabcolumn : 2
                        },
                        1000: {
                            items: items ? items : 3
                        }
                    }
                });
                caro.find('.owl-prev').attr('aria-label', 'Previous');
                caro.find('.owl-next').attr('aria-label', 'Next');
                caro.find('.owl-dot').each(function(index) {
                    $(this).attr('aria-label', index + 1);
                });
                caro.parents('.rt-row').removeClass('pfp-pre-loader');

            }
            
        });
        
        // console.log('Ready');
    }

    /**
     * Initialize AJAX-based portfolio search filter.
     *
     * Sends search queries to the server and replaces
     * portfolio content with filtered results.
     */
    window.initPfpFilter = function () {
        $('.rt-search-filter-wrap').each(function () {
            var $wrap = $(this),
                $input = $wrap.find('.rt-search-input'),
                $loading = $wrap.find('.rt-loading'),
                $container = $wrap.next('.tlp-portfolio-container'),
                containerID = $.trim($container.attr('id')),
                scID = containerID ? containerID.replace('tlp-portfolio-container-', '') : 0,
                isIsotope = $container.hasClass('is-isotope'),
                isCarousel = $container.hasClass('is-carousel'),
                debounceTimer = null,
                currentXHR = null,
                originalContent = $container.html();

            if (!scID || isCarousel) {
                return;
            }

            $input.on('input', function () {
                var searchVal = $(this).val().trim();

                clearTimeout(debounceTimer);

                // Abort any running AJAX request
                if (currentXHR && currentXHR.readyState !== 4) {
                    currentXHR.abort();
                }

                $container.stop(true, true).css('opacity', '1');

                if (!searchVal) {
                    $container.html(originalContent);
                    pfpFixLazyLoadToAll();
                    initPfpMagicPopup();
                    $loading.removeClass('is-loading');
                    return;
                }

                debounceTimer = setTimeout(function () {
                    currentXHR = pfpDoAjax($container, scID, searchVal, 1, $loading, isIsotope, false);
                }, 400);
            });
        });
    };

    /**
     * Perform AJAX search request for portfolio items.
     *
     * @param {jQuery}  $container  The portfolio container element.
     * @param {number}  scID        The shortcode post ID.
     * @param {string}  search      The search term.
     * @param {number}  paged       The current page number.
     * @param {jQuery}  $loading    The loading indicator element.
     * @param {boolean} isIsotope   Whether the layout is isotope.
     * @param {boolean} isCarousel  Whether the layout is carousel.
     */
    function pfpDoAjax($container, scID, search, paged, $loading, isIsotope, isCarousel) {
        $loading.addClass('is-loading');
        $container.stop(true, true).css('opacity', '0.5');

        return $.ajax({
            url: tlp_portfolio_public_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'pfp_ajax_filter',
                sc_id: scID,
                search: search,
                paged: paged,
                nonce: tlp_portfolio_public_obj.nonce
            },
            success: function (response) {
                if (!response.success) {
                    $container.css('opacity', '1');
                    return;
                }

                var data = response.data,
                    $row = $container.find('.rt-row').first();

                if (isIsotope) {
                    pfpReplaceIsotopeContent($container, $row, data);
                } else {
                    pfpReplaceGridContent($container, $row, data);
                }

                $container.css('opacity', '1');
                pfpFixLazyLoadToAll();
                initPfpMagicPopup();
            },
            error: function (xhr, status) {
                if (status !== 'abort') {
                    $container.css('opacity', '1');
                }
            },
            complete: function (xhr, status) {
                if (status !== 'abort') {
                    $loading.removeClass('is-loading');
                }
            }
        });
    }
    /**
     * Replace isotope layout content with AJAX search results.
     *
     * Destroys and reinitializes isotope with new items and filter buttons.
     * Shows "not found" message when no results are returned.
     *
     * @param {jQuery} $container The portfolio container element.
     * @param {jQuery} $row       The row element inside the container.
     * @param {object} data       The AJAX response data.
     */
    function pfpReplaceIsotopeContent($container, $row, data) {
        var $isoHolder = $container.find('.tlp-portfolio-isotope'),
            $buttonGroup = $container.find('.tlp-portfolio-isotope-button');

        if ($isoHolder.length && $isoHolder.data('isotope')) {
            $isoHolder.isotope('destroy');
        }

        $buttonGroup.remove();
        $row.find('.pfp-no-results').remove();

        if (data.html && data.html.trim().length > 0) {
            if ($isoHolder.length) {
                $isoHolder.html(data.html);
            } else {
                $row.find('.tlp-isotope-item').remove();
                $row.append(data.html);
            }

            if (data.filter_button) {
                $row.prepend(data.filter_button);
            }

            if ($isoHolder.length) {
                $isoHolder.imagesLoaded(function () {
                    $isoHolder.isotope({
                        itemSelector: '.tlp-isotope-item'
                    });

                    var $newButtonGroup = $container.find('.tlp-portfolio-isotope-button');

                    $newButtonGroup.on('click', 'button', function (e) {
                        e.preventDefault();
                        var filterValue = $(this).attr('data-filter');
                        $isoHolder.isotope({ filter: filterValue });
                        $(this).parent().find('.selected').removeClass('selected');
                        $(this).addClass('selected');
                    });
                });
            }
        } else {
            // No results found
            if ($isoHolder.length) {
                $isoHolder.html('');
            }

            $row.find('.tlp-isotope-item').remove();
            $row.append(
                '<div class="pfp-no-results" style="width:100%;text-align:center;padding:30px 15px;">' +
                '<p>No portfolio found</p>' +
                '</div>'
            );
        }
    }


    /**
     * Replace grid layout content with AJAX search results.
     *
     * Clears all existing items, messages, and pagination
     * before inserting the new filtered content.
     *
     * @param {jQuery} $container The portfolio container element.
     * @param {jQuery} $row       The row element inside the container.
     * @param {object} data       The AJAX response data.
     */
    function pfpReplaceGridContent($container, $row, data) {
        $row.children().remove();
        $container.children().not('.rt-row').not('[class*="row"]').remove();

        if (data.html && data.html.trim().length > 0) {
            $row.html(data.html);

            if (data.pagination) {
                $container.append(data.pagination);
                pfpBindPaginationFilter($container);
            }
        } else {
            $row.html(
                '<div class="pfp-no-results" style="width:100%;text-align:center;padding:30px 15px;">' +
                '<p>' + 'No portfolio found' + '</p>' +
                '</div>'
            );
        }
    }

    /**
     * Bind click events on AJAX pagination links.
     *
     * Intercepts pagination clicks and triggers AJAX search
     * with the appropriate page number.
     *
     * @param {jQuery} $container The portfolio container element.
     */
    function pfpBindPaginationFilter($container) {
        $container.find('.pfp-pagination a').on('click', function (e) {
            e.preventDefault();

            var $wrap = $container.prev('.rt-search-filter-wrap'),
                $input = $wrap.find('.rt-search-input'),
                $loading = $wrap.find('.rt-loading'),
                searchVal = $input.val() ? $input.val().trim() : '',
                containerID = $.trim($container.attr('id')),
                scID = containerID ? containerID.replace('tlp-portfolio-container-', '') : 0,
                isIsotope = $container.hasClass('is-isotope'),
                isCarousel = $container.hasClass('is-carousel'),
                href = $(this).attr('href'),
                paged = 1;

            var match = href.match(/paged?=(\d+)/);

            if (match) {
                paged = parseInt(match[1], 10);
            } else {
                var pageMatch = href.match(/\/page\/(\d+)/);

                if (pageMatch) {
                    paged = parseInt(pageMatch[1], 10);
                }
            }

            pfpDoAjax($container, scID, searchVal, paged, $loading, isIsotope, isCarousel);

            $('html, body').animate({
                scrollTop: $container.offset().top - 50
            }, 300);
        });
    }

    $(document).on('pfp_loaded pfp_item_after_load', function () {
        initPfpMagicPopup();
        // pfpOverlayIconResize();
    });
    $(function () {
        initPfpMagicPopup();
        initTlpPortfolio();
        initPfpFilter();
    });
    $(window).on('resize', function () {
        $(".tlp-portfolio-container").trigger("pfp_loaded");
    });

    $(window).on('load', function () {
        initRtppCaroselPortfolio();
    });

     // Elementor Frontend Load
    $( window ).on( 'elementor/frontend/init', function() {
        if (elementorFrontend.isEditMode()) {
            elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function(){
                initRtppCaroselPortfolio();
                initPfpFilter();
            } );
        }
    } );




})(jQuery, window);
