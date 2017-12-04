// IL Humanities Envisioning Justice - Firebelly 2017
/*jshint latedef:false*/

// Good Design for Good Reason for Good Namespace
var EJ = (function($) {

  var screen_width = 0,
      breakpoint_small = false,
      breakpoint_medium = false,
      breakpoint_large = false,
      breakpoint_array = [480,1000,1200],
      $document,
      $sidebar,
      $tod,
      no_header_text,
      map,
      mapFeatureLayer,
      mapGeoJSON = [],
      mapIconRed,
      mapIconBlue,
      mapTop,
      loadingTimer,
      page_at;

  function _init() {
    // touch-friendly fast clicks
    FastClick.attach(document.body);

    // Cache some common DOM queries
    $document = $(document);
    $('body').addClass('loaded');
    $sidebar = $('aside.main');
    $tod = $('section.thought-of-the-day');
    no_header_text = $('header.no-header-text').length;

    // Set screen size vars
    _resize();

    // Fit them vids!
    $('main').fitVids();

    // Homepage (pre _initMasonry)
    if ($('.home.page').length) {
      page_at = 'homepage';
      // Homepage has a funky load-more in events that is part of masonry until clicked
      if (breakpoint_medium) {
        $('.event-cal .events-buttons').clone().addClass('masonry-me').appendTo('.event-cal .events');
      }
    }

    // Disclaimer mobile link that reveals hidden disclaimer block
    $('<li class="hide-for-medium-up"><a href="#">Disclaimer</a></li>').prependTo('#menu-footer-links').on('click', function(e) {
      e.preventDefault();
      $('.disclaimer').velocity('slideDown');
    });

    // Add .img-link class to sidebar image links to target with CSS
    $('.sidebar-content a,.user-content a,.event-details a').has('img').addClass('img-link');

    // Init behavior for various sections
    _injectSvgs();
    _initPageActions();
    _initThoughtSubmit();
    _initNav();
    _initSearch();
    _initFormActions();
    _initMap();
    _initMasonry();
    _initLoadMore();
    _initBigClicky();
    _initFilters();
    _initSlashFields();
    _initHoverPairs();

    // Esc handlers
    $(document).keyup(function(e) {
      if (e.keyCode === 27) {
        // Check if search is open
        if ($('.site-header .search-form').is('.-active')) {
          _hideSearch();
        } else if ($('.site-nav').is('.-active')) {
          _hideMobileNav();
        }
      }
    });

    // Add span to accordion titles to style +/- icons
    $('.accordion-title').prepend('<span class="open-status"></span>');

    // Events landing page
    if ($('.post-type-archive-event').length) {
      if (breakpoint_medium) {
        // Set initial mapTop position
        mapTop = $('#map').offset().top;
        // Onscroll toggle sticky class on large map
        $(window).on('scroll', _scroll);
      }
    }

    // Run resize again in case anything needs adjusting
    _resize();

    // Smoothscroll links
    $('a.smoothscroll').click(function(e) {
      e.preventDefault();
      var href = $(this).attr('href');
      _scrollBody($(href));
    });

    // Scroll down to hash afer page load
    $(window).load(function() {
      if (window.location.hash) {
        _scrollBody($(window.location.hash));
      }
    });

  } // end _init()

  function _injectSvgs() {
    // Load the svgs-defs
    boomsvgloader.load('/app/themes/envisioningjustice/assets/svgs/build/svgs-defs.svg');
  }

  function _initFilters() {
    $('form.filters').on('submit', function(e) {
      if ($('form.filters select[name=prox_miles]').length) {
        if ($('form.filters select[name=prox_miles]').val() !== '' && $('form.filters input[name=prox_zip]').val() === '') {
          e.preventDefault();
          alert('Please enter a zip code.');
          $('form.filters input[name=prox_zip]')[0].focus();
        } else if ($('form.filters select[name=prox_miles]').val() === '' && $('form.filters input[name=prox_zip]').val() !== '') {
          $('form.filters select[name=prox_miles]').val(1);
        }
      }
    });
  }

  function _initBigClicky() {
    $(document).on('click', '.article-list article, .focus-list-large article, .bigclicky .flex-item', function(e) {
      if (!$(e.target).is('a')) {
        e.preventDefault();
        var link = $(this).find('h1:first a,h2:first a');
        var href = link.attr('href');
        if (href) {
          if (e.metaKey || link.attr('target')) {
            window.open(href);
          } else {
            location.href = href;
          }
        }
      }
    });
  }

  function _scrollBody(element, duration, delay) {
    if ($('#wpadminbar').length) {
      wpOffset = $('#wpadminbar').height();
    } else {
      wpOffset = 0;
    }
    element.velocity("scroll", {
      duration: duration,
      delay: delay,
      offset: -wpOffset
    }, "easeOutSine");
  }

  function _initPageActions() {
    // Frontpage
    if ($('body.home').length) {
      $('.page-header').append('<button class="button button-circular scroll-to-content"><svg class="icon icon-arrow" aria-hidden="true" role="presentation"><use xlink:href="#icon-arrow"/></svg></button>');
      $('.scroll-to-content').on('click', function() {
        _scrollBody($('#page-content'));
      });
    }
  }

  function _initSearch() {
    $('.search-close').on('click', function(e) {
      e.preventDefault();
      _hideSearch();
    });

    $('.search-toggle').on('click', function(e) {
      $(this).removeClass('hover-trigger');
      $('.site-nav').addClass('search-open');
      $('.search-form').addClass('-active');
      $('.search-form input').focus();
      $('.site-header .container').addClass('-hover');
    }).mouseenter(function() {
      if (!$('.site-nav').is('.search-open')) {
        $('[data-hover="header-slash"]').addClass('-hover');
      }
    }).mouseleave(function() {
      if (!$('.site-nav').is('.search-open')) {
        $('[data-hover="header-slash"]').removeClass('-hover');
      }
    });
  }

  function _hideSearch() {
    $('.site-header .container').removeClass('-hover');
    $('.search-toggle').addClass('hover-trigger');
    $('.site-nav').removeClass('search-open');
    $('.search-form').removeClass('-active');
  }

  function _initFormActions() {
    function checkInputVal($input) {
      if($input.val()) {
        $input.parents('.input-wrap').addClass('filled');
      } else {
        $input.parents('.input-wrap').removeClass('filled');
      }
    }

    $('form input, form textarea').on('focus', function() {
      $(this).parents('.input-wrap').addClass('-focus');
    }).on('blur', function() {
      checkInputVal($(this));
      $(this).parents('.input-wrap').removeClass('-focus');
    }).on('keypress', function(e) {
      $(this).parents('.input-wrap').addClass('filled');
    });
  }

  function _initMap() {
    // Only init Mapbox if > breakpoint_medium, or on a body.single page (small sidebar maps)
    if ($('#map').length && (breakpoint_medium || $('body.single').length)) {
      L.mapbox.accessToken = 'pk.eyJ1IjoiZmlyZWJlbGx5ZGVzaWduIiwiYSI6IlZMd0JwWWcifQ.k9GG6CFOLrVk7kW75z6ZZA';
      map = L.mapbox.map('map', 'firebellydesign.0238ce0b', { zoomControl: false, attributionControl: false }).setView([41.843, -88.075], 11);

      mapFeatureLayer = L.mapbox.featureLayer().addTo(map);

      mapIconRed = L.icon({
        iconUrl: "/app/themes/envisioningjustice/dist/images/mapbox/marker-red.png",
        iconSize: [25, 42],
        iconAnchor: [12, 40],
        popupAnchor: [0, -40],
        className: "marker-red"
      });
      mapIconBlue = L.icon({
        iconUrl: "/app/themes/envisioningjustice/dist/images/mapbox/marker-blue.png",
        iconSize: [25, 42],
        iconAnchor: [12, 40],
        popupAnchor: [0, -40],
        className: "marker-blue"
      });

      // Set custom icons
      mapFeatureLayer.on('layeradd', function(e) {
        var marker = e.layer,
          feature = marker.feature;
        marker.setIcon(feature.properties.icon);
      });

      // Larger map behavior
      if ($('#map').hasClass('large')) {
        // Disable zoom/scroll
        map.dragging.disable();
        map.touchZoom.disable();
        map.doubleClickZoom.disable();
        map.scrollWheelZoom.disable();

        // Prevent the listeners from disabling default
        // actions (http://bingbots.com/questions/1428306/mapbox-scroll-page-on-touch)
        L.DomEvent.preventDefault = function(e) {return;};

        // Click to open event
        mapFeatureLayer.on('click', function(e) {
          e.layer.closePopup();
          var event_url = e.layer.feature.properties.event_url;
          location.href = event_url;
        });

        // Hover events to highlight listings
        mapFeatureLayer.on('mouseover', function(e) {
          // e.layer.openPopup();
          var event_id = e.layer.feature.properties.event_id;
          var article = $('.events article[data-id='+event_id+']');
          if (article.length) {
            article.addClass('hover');
          }
          _highlightMapPoint(event_id);
        });
        mapFeatureLayer.on('mouseout', function(e) {
          e.layer.closePopup();
          var event_id = e.layer.feature.properties.event_id;
          var article = $('.events article[data-id='+event_id+']');
          if (article.length) {
            article.removeClass('hover');
          }
          _unHighlightMapPoints();
        });
      } else {
        // Smaller maps need no tooltip
        mapFeatureLayer.on('click', function(e) {
          e.layer.closePopup();
        });
      }

      _getMapPoints();
    }
  }

  function _getMapPoints() {
    var $mapPoints = $('.map-point:not(.mapped)');
    if ($mapPoints.length) {
      // Any map-points on page? add to map
      $mapPoints.each(function() {
        var event_id = $(this).data('id');
        var $point = $(this).addClass('mapped').hover(function() {
          _highlightMapPoint(event_id);
        }, _unHighlightMapPoints);
        if ($point.data('lng')) {
          mapGeoJSON.push({
              type: 'Feature',
              geometry: {
                  type: 'Point',
                  coordinates: [ $point.data('lng'), $point.data('lat') ]
              },
              properties: {
                  title: $point.data('title'),
                  event_id: $point.data('id'),
                  event_url: $point.data('url'),
                  description: $point.data('desc'),
                  icon: mapIconRed
              }
          });
        }
      });
      // Add the array of point objects
      mapFeatureLayer.setGeoJSON(mapGeoJSON);

      if ($('#map').hasClass('large')) {
        // Larger map centers on IL
        map.setView([39.9, -90.5], 7);
      } else {
        // Smaller map zooms in on single point
        map.setView([$mapPoints.first().data('lat'), $mapPoints.first().data('lng')], 13);
      }
    }
  }

  function _highlightMapPoint(event_id) {
    mapFeatureLayer.eachLayer(function(marker) {
      if (marker.feature.properties.event_id === event_id) {
        marker.setIcon(mapIconRed);
        marker.setZIndexOffset(1000);
      } else {
        marker.setIcon(mapIconBlue);
        marker.setZIndexOffset(0);
      }
    });
    // mapFeatureLayer.setGeoJSON(mapGeoJSON);
  }
  function _unHighlightMapPoints() {
    mapFeatureLayer.eachLayer(function(marker) {
      marker.setIcon(mapIconRed);
      marker.setZIndexOffset(0);
    });
    // mapFeatureLayer.setGeoJSON(mapGeoJSON);
  }

  // Handles main nav
  function _initNav() {
    // SEO-useless nav toggler
    $('<button class="menu-toggle"><span class="text">Menu</span> <span class="menu-bar"></span></button>')
      .prependTo('.site-header .container > .-inner');

    $document.on('click', '.menu-toggle:not(.menu-open)', function(e) {
      _showMobileNav();
    });
    $document.on('click', '.menu-toggle.menu-open', function(e) {
      _hideMobileNav();
    });

    // Trigger slanted line hover
    $('.site-nav a').mouseenter(function() {
      $('.hover-item[data-hover="header-slash"]').addClass('-hover');
    }).mouseleave(function() {
      var pairName = $(this).attr('data-hover');
      $('.hover-item[data-hover="header-slash"]').removeClass('-hover');
    });

    // Show the after element on nav links after the first time the nav
    $('nav.site-nav').one('mouseenter', function() {
      $('nav.site-nav').addClass('-animate');
    });
  }

  function _showMobileNav() {
    $document.scrollTop(0);
    $('body, .menu-toggle').addClass('menu-open');
    $('.menu-toggle .text').html('Close');
    $('.site-nav').addClass('-active');
  }

  function _hideMobileNav() {
    $('body, .menu-toggle').removeClass('menu-open');
    $('.menu-toggle .text').html('Menu');
    $('.site-nav').removeClass('-active');
  }

  function _initMasonry(){
    if (breakpoint_medium) {
      $('.masonry').masonry({
        itemSelector: 'article,.masonry-me',
        transitionDuration: '.35s'
      });
    }
  }

  function _initLoadMore() {
    $document.on('click', '.load-more a', function(e) {
      e.preventDefault();
      var $load_more = $(this).closest('.load-more');
      var post_type = $load_more.attr('data-post-type') ? $load_more.attr('data-post-type') : 'news';
      var page = parseInt($load_more.attr('data-page-at'));
      var per_page = parseInt($load_more.attr('data-per-page'));
      var past_events = (post_type==='event') ? parseInt($load_more.attr('data-past-events')) : 0;
      var prox_zip = (post_type==='event') ? parseInt($load_more.attr('data-prox-zip')) : '';
      var prox_miles = (post_type==='event') ? parseInt($load_more.attr('data-prox-miles')) : '';
      var focus_area = $load_more.attr('data-focus-area');
      var program = $load_more.attr('data-program');
      var exhibitions = $load_more.attr('data-exhibitions');
      var more_container = $load_more.parents('section,main').find('.load-more-container');
      loadingTimer = setTimeout(function() { more_container.addClass('loading'); }, 500);

      // Homepage has a funky load-more div in events that is part of masonry until clicked
      if (breakpoint_medium && $('.home.page').length && $(e.target).parents('.events-buttons').length) {
        var lm = $('.event-cal').addClass('loaded-more').find('.events .events-buttons');
        // Remove load-more from masonry and relayout
        $('.events').masonry('remove', lm);
        $('.events').masonry();
      }

      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          data: {
              action: 'load_more_posts',
              post_type: post_type,
              page: page+1,
              per_page: per_page,
              past_events: past_events,
              focus_area: focus_area,
              exhibitions: exhibitions,
              program: program,
              prox_zip: prox_zip,
              prox_miles: prox_miles
          },
          success: function(data) {
            var $data = $(data);
            if (loadingTimer) { clearTimeout(loadingTimer); }
            more_container.append($data).removeClass('loading');
            if (breakpoint_medium) {
              more_container.masonry('appended', $data, true);
            }
            $load_more.attr('data-page-at', page+1);
            if (post_type==='event') {
              _getMapPoints();
            }

            // Hide load more if last page
            if ($load_more.attr('data-total-pages') <= page + 1) {
                $load_more.addClass('hide');
            }
          }
      });
    });
  }

  function _initThoughtSubmit() {
    $document.on('click', '.submit-thought a', function(e) {
      e.preventDefault();
      if ($('.thought-of-the-day').hasClass('submitting-thought')) {
        _cancelThoughtSubmit();
      } else {
        $('.thought-of-the-day').velocity({opacity: 0, left: -50}, { easing: 'easeInSine', duration: 150,
          complete: function(e) {
            $('.thought-of-the-day').css('left',50).addClass('submitting-thought').velocity({opacity: 1, left: 0}, {  easing: 'easeOutSine', duration: 150 });
          }
        });
      }
    });
    $('.thought-of-the-day .close-button').on('click', _cancelThoughtSubmit);

    // Handle ajax submit of new thought
    $document.on('submit', 'form.new-thought-form', function(e) {
      e.preventDefault();
      var $form = $(this);
      var data = $form.addClass('working').serialize();
      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          data: data,
          success: function(response) {
            $form.removeClass('working');
            if (response.success) {
              $('.thought-of-the-day').velocity({opacity: 0, left: -50}, { easing: 'easeInSine', duration: 150,
                complete: function(e) {
                  $('.new-thought-form')[0].reset();
                  $('.thought-of-the-day .response').text(response.data.message);
                  $('.thought-of-the-day').css('left',50).removeClass('submitting-thought').addClass('thought-submitted').velocity({opacity: 1, left: 0}, {  easing: 'easeOutSine', duration: 150 });
                }
              });

            } else {
              alert('There was an error: '+response.data.message);
            }
          }
      });
    });
  }

  function _cancelThoughtSubmit() {
    if ($('.thought-of-the-day').is('.submitting-thought,.thought-submitted')) {
      $('.thought-of-the-day').velocity({opacity: 0, left: 50}, { easing: 'easeInSine', duration: 150,
        complete: function(e) {
          $('.thought-of-the-day').css('left',-50).removeClass('submitting-thought thought-submitted').velocity({opacity: 1, left: 0}, {  easing: 'easeOutSine', duration: 150 });
        }
      });
    }
  }

  function _initSlashFields() {
    // Establish slashes, their size, and max width of field
    var $slash = $('#slash');
    var slashW = 36;
    var slashH = 36;
    var maxW = 1240;

    // Draw the slashes
    $('.slashfield').each(function(e) {
      var rows = $(this).data('rows');
      var slashAmount = rows*(maxW / slashW);
      var fieldW = $(this).width();
      
      $(this).height(slashH*rows);
      
      for(var i = 0;i < slashAmount; i++) {
        $(this).append('<svg class="slash" aria-hidden="true" role="presentation"><use xlink:href="#slash"/></svg>');
      }
    });
    // Get the drawn slashes and their size
    var slashes = $('.slash');
    var slashesW = slashes.outerWidth();
    var slashesH = slashes.outerHeight();

    // Track Mouse Movement
    // Event throttling
    var lastMove = 0;
    var eventThrottle = 10;
    $(window).on('mousemove', function(e) {
      e.preventDefault();
      var now = Date.now();
      if (now > lastMove + eventThrottle) {
        lastMove = now;
        var mousePos = {
          x: e.pageX,
          y: e.pageY
        };

        slashes.each(function(e) {
          var slash = $(this);

          var slashPos = {
            x: slash.offset().left + slashesW / 2,
            y: slash.offset().top + slashesH / 2
          };

          var angle = (Math.atan2(mousePos.y - slashPos.y, mousePos.x - slashPos.x) * 180 / Math.PI) - 90;
          if(angle >= 360) {
            angle -= 360;
          }
          // Change transformation
          slash.css('transform', 'rotate('+angle+'deg)');
        });
      }
    });
  }

  function _initHoverPairs() {
    $('.hover-trigger').mouseenter(function() {
      var pairName = $(this).attr('data-hover');
      $('.hover-item[data-hover="'+pairName+'"]').addClass('-hover');
    }).mouseleave(function() {
      var pairName = $(this).attr('data-hover');
      $('.hover-item[data-hover="'+pairName+'"]').removeClass('-hover');
    });
  }

  // Track ajax pages in Analytics
  function _trackPage() {
    if (typeof ga !== 'undefined') { ga('send', 'pageview', document.location.href); }
  }

  // Track events in Analytics
  function _trackEvent(category, action) {
    if (typeof ga !== 'undefined') { ga('send', 'event', category, action); }
  }

  // Called in quick succession as window is resized
  function _resize() {
    screenWidth = document.documentElement.clientWidth;
    breakpoint_small = (screenWidth > breakpoint_array[0]);
    breakpoint_medium = (screenWidth > breakpoint_array[1]);
    breakpoint_large = (screenWidth > breakpoint_array[2]);

    // Adjust position of Thought of the Day for smaller quotes
    if (!no_header_text && $sidebar.length) {
      if (breakpoint_medium) {
        var sidebar_height = $sidebar.height();
        var adjustment = 270;
        // Shorter TOD giving sidebars guff
        if ($tod.length && $tod.height() <= 406) {
          adjustment = $tod.height() - 136;
        }
        $sidebar.css('margin-top', -adjustment);
      } else {
        $sidebar.css('margin-top', '');
      }
    } else if (page_at === 'homepage' && $tod.length) {
      // Homepage TOD adjustment
      if (breakpoint_medium) {
        var header_height = $('header.page-header').height();
        var tod_height = $tod.height();
        var top = header_height - 270;
        if (tod_height <= 346) {
          top = top + 406 - tod_height;
        }
        $('.thought-of-the-day-wrapper').css('top', top);
      } else {
        $('.thought-of-the-day-wrapper').css('top', '');
      }
    }
  }

  // Called on scroll
  function _scroll(dir) {
    var wintop = $(window).scrollTop();
    $('#map').toggleClass('sticky', (wintop > mapTop));
  }

  // Public functions
  return {
    init: _init,
    resize: _resize,
    scrollBody: function(section, duration, delay) {
      _scrollBody(section, duration, delay);
    },
    setMapView: function(lat, lng, zoom) {
      map.setView([lat, lng], zoom);
    }
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(EJ.init);

// Zig-zag the mothership
jQuery(window).resize(EJ.resize);
