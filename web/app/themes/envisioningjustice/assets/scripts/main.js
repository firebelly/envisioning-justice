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
      pointsLayer,
      pointArea,
      mapPointsData,
      mapGeoJSON = [],
      mapIconRed,
      mapIconBlue,
      mapTop,
      mapboxGlSupported,
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
    mapboxGlSupported = mapboxgl.supported();

    // Set screen size vars
    _resize();

    // Fit them vids!
    $('main').fitVids();

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
    _initStorySubmit();
    _initNav();
    _initSearch();
    _initFormActions();
    _initSlickSliders();
    _initMasonry();
    _initLoadMore();
    _initBigClicky();
    _initAccordions();
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

    // Maps
    if($('#map').length){
      // Mapbox GL will only work in ie11+
      // For ie9 and ie10, we will need to use the old school raster-tile mapbox

      // Get the correct CSS
      var mapboxCss = mapboxGlSupported ? 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css' : 'https://api.mapbox.com/mapbox.js/v3.2.0/mapbox.css';
      $('head').append('<link href="'+mapboxCss+'" rel="stylesheet" />');

      // Get the correct JS, init maps on load
      var mapboxJs = mapboxGlSupported ? 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.js' : 'https://api.mapbox.com/mapbox.js/v3.2.0/mapbox.js';
      $.getScript(mapboxJs, function() {
        if (breakpoint_medium) {
          _initMap(11, [-87.6568088,41.8909229]);
        }
      });
    }

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
    $(document).on('click', '.big-clicky', function(e) {
      if (!$(e.target).is('a')) {
        e.preventDefault();
        var link = $(this).find('a');
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
        $input.parents('.input-wrap, .BBFormFieldContainer').addClass('filled');
      } else {
        $input.parents('.input-wrap, .BBFormFieldContainer').removeClass('filled');
      }
    }

    $document.on('focus', 'form input, form textarea', function() {
      console.log('test');
      $(this).parents('.input-wrap, .BBFormFieldContainer').addClass('-focus');
    }).on('blur', 'form input, form textarea', function() {
      checkInputVal($(this));
      $(this).parents('.input-wrap, .BBFormFieldContainer').removeClass('-focus');
    }).on('keypress', 'form input, form textarea', function(e) {
      $(this).parents('.input-wrap, .BBFormFieldContainer').addClass('filled');
    });

    // Clear Filter Button
    $('#filter-clear').on('click', function(e) {
      e.preventDefault();
      // The form
      var $form = $(this).closest('form');
      $form.find('input[type="radio"]').prop('checked', false);
      $form.find('input[type="radio"]:first').prop('checked', true);
      $form.find('select option').prop('selected', false);

      $(this).addClass('hide');
    });

    // Add Clear Filter Button
    $('form.filters').on('change', function(e) {
      $(this).find('#filter-clear').removeClass('hide');
    });
  }

  function _initSlickSliders() {
    $('.slider').slick({
      slide: '.slide-item',
      autoplay: true,
      arrows: true,
      prevArrow: '<button class="button button-circular previous-item button-prev nav-button"><svg class="icon icon-arrow" aria-hidden="true" role="presentation"><use xlink:href="#icon-arrow"/></svg></button>',
      nextArrow: '<button class="button button-circular next-item button-next nav-button"><svg class="icon icon-arrow" aria-hidden="true" role="presentation"><use xlink:href="#icon-arrow"/></svg></button>',
      dots: false,
      autoplaySpeed: 6000,
      speed: 300,
      lazyLoad: 'ondemand'
    });

    // Put slick arrows in a wrapper for positioning
    $('.slider').each(function() {
      $(this).append('<div class="slick-arrows-container"></div>');
      $(this).find('.slick-arrow').appendTo($(this).find('.slick-arrows-container'));
    });
  }

  function _initAccordions() {
    // Attach button
    $('.accordion').each(function() {
      if (!$(this).is('.open')) {
        $(this).find('.accordion-content').hide();
        $(this).find('.accordion-toggle, .accordion-title').append('<button class="button button-circular"><svg class="icon icon-plus" aria-hidden="true" role="presentation"><use xlink:href="#icon-plus"/></svg></button>');
      } else {
        $(this).find('.accordion-toggle, .accordion-title').append('<button class="button button-circular"><svg class="icon icon-minus" aria-hidden="true" role="presentation"><use xlink:href="#icon-minus"/></svg></button>');
      }
    });

    $('.accordion-toggle').on('click', function(e) {
      e.preventDefault();

      var $accordion = $(this).closest('.accordion');
      if ($accordion.is('.open')) {
        _closeAccordion($accordion);
      } else {
        _openAccordion($accordion);
      }
    });

    // Wordpress accordions
    $('.accordion-title').on('click', function(e) {

      $(this).find('.icon').remove();

      if ($(this).is('.open')) {
        $(this).find('button').append('<svg class="icon icon-plus" aria-hidden="true" role="presentation"><use xlink:href="#icon-plus"/></svg>');
      } else if (!$(this).is('.open')) {
        $(this).find('button').append('<svg class="icon icon-minus" aria-hidden="true" role="presentation"><use xlink:href="#icon-minus"/></svg>');
      }

      $('.accordion-title').not($(this)).find('.icon').remove();
      $('.accordion-title').not($(this)).find('button').append('<svg class="icon icon-plus" aria-hidden="true" role="presentation"><use xlink:href="#icon-plus"/></svg>');
    });

    $('#resource-submit-expand').on('click', function(e) {
      e.preventDefault();

      if ($('.resource-submission-text').is('.open')) {
        $('.resource-submission-text').removeClass('open');
        $('.resource-submission-text').slideUp(250);
      } else {
        $('.resource-submission-text').addClass('open');
        $('.resource-submission-text').slideDown(250);
      }
    });
  }

  function _openAccordion($accordion) {
    $accordion.addClass('open');
    $accordion.find('.accordion-toggle .icon, .accordion-title .icon').remove();
    $accordion.find('.accordion-toggle button, .accordion-title button').append('<svg class="icon icon-minus" aria-hidden="true" role="presentation"><use xlink:href="#icon-minus"/></svg>');

    var $content = $accordion.find('.accordion-content');
    $content.slideDown(250);
  }

  function _closeAccordion($accordion) {
    $accordion.removeClass('open');
    $accordion.find('.accordion-toggle .icon, .accordion-title .icon').remove();
    $accordion.find('.accordion-toggle button, .accordion-title button').append('<svg class="icon icon-plus" aria-hidden="true" role="presentation"><use xlink:href="#icon-plus"/></svg>');

    var $content = $accordion.find('.accordion-content');
    $content.slideUp(250);
  }

  function _initMap(startZoom, startCenter) {

    var $mapPoints = $('.map-point');
    var color = $('#map').attr('data-color');
    mapPointsData = {
      'type': 'FeatureCollection',
      'features': []
    };

    // FOR MAPBOX GL (ie11+, and everything else)
    if (mapboxGlSupported) {

      if (typeof mapboxgl === 'undefined') { return; }

      mapboxgl.accessToken = 'pk.eyJ1IjoidHNxdWFyZWQxMDE3IiwiYSI6ImNpdG5hdXJnYTAzcmsyb24waW42MTlsY24ifQ.8hBEeKstyMlXhbK8mSxJoA';
      map = new mapboxgl.Map({
        container: 'map',
        scrollZoom: false,
        zoom: startZoom,
        center: startCenter,
        style: 'mapbox://styles/tsquared1017/cj8c5fqt57w1q2slaz7ca5t7a'
      });

      var nav = new mapboxgl.NavigationControl();
      map.addControl(nav, 'top-right');

      // Inject svg icons
      $('#map .mapboxgl-ctrl-zoom-in').html('<svg class="icon icon-plus" aria-hidden="true" role="presentation"><use xlink:href="#icon-plus"/></svg>');
      $('#map .mapboxgl-ctrl-zoom-out').html('<svg class="icon icon-minus" aria-hidden="true" role="presentation"><use xlink:href="#icon-minus"/></svg>');

      // Just init single map?
      if ($mapPoints.length === 0) { return; }

      // Cull map points from DOM
      $mapPoints.each(function(){
        var $this = $(this);
        $this.addClass('mapped');
        if ($this.attr('data-lat') !== '') {
          mapPointsData.features.push({
            'type': 'Feature',
            'geometry': {
              'type': 'Point',
              'coordinates': [parseFloat($this.attr('data-lng')), parseFloat($this.attr('data-lat'))]
            },
            'properties': {
              'title': $this.attr('data-title'),
              'url': $this.attr('data-url'),
              'enabled': !$this.hasClass('disabled')
            }
          });
        }
      });

      // No map points with lat/lng found?
      if (mapPointsData.features.length === 0) { return; }

      // Center map
      if (mapPointsData.features.length > 1) {
        var bounds = new mapboxgl.LngLatBounds();
        mapPointsData.features.forEach(function(feature) {
          bounds.extend(feature.geometry.coordinates);
        });
        map.fitBounds(bounds, {padding: 100});
      } else {
        map.setCenter(mapPointsData.features[0].geometry.coordinates);
      }

      map.on('load', function () {
        map.addSource('points', {
          'type': 'geojson',
          'data': mapPointsData
        });

        // Add points as a layer
        map.addLayer({
          'id': 'points',
          'type': 'symbol',
          'source': 'points',
          'layout': {
            'icon-image': 'map-pin-'+color,
            'icon-allow-overlap': true
          }
        });

        // Add points as a hover layer with "map-pin-hover" icon
        map.addLayer({
          'id': 'points-hover',
          'type': 'symbol',
          'source': 'points',
          'layout': {
            'icon-image': 'map-pin-black',
            'icon-allow-overlap': true
          },
          'filter': ['==', 'url', '']
        });

        // When clicking on map, check if clicking on a pin, and open URL if so
        map.on('click', function(e) {
          var features = map.queryRenderedFeatures(e.point, { layers: ['points', 'points-hover'] });
          if (features.length && features[0].properties.enabled) {
            location.href = features[0].properties.url;
          }
        });

        // Map hover state handling
        map.on('mousemove', function(e) {
          var features = map.queryRenderedFeatures(e.point, { layers: ['points', 'points-hover'] });

          if (features.length && features[0].properties.enabled) {
            // Cursor pointer = clickable
            map.getCanvas().style.cursor = 'pointer';

            // Hover state for pin: show pins in points-hover that match feature URL
            map.setFilter('points-hover', ['!=', 'url', features[0].properties.url]);

            // Match map-point link for point and add "-hover" class
            $mapPoints.find('a[href="' + features[0].properties.url +'"]').closest('.map-point').addClass('-hover');
          } else {
            // Clear out hover states for pins and features
            map.getCanvas().style.cursor = '';
            $mapPoints.removeClass('-hover');
            map.setFilter('points-hover', ['==', 'url', '']);
          }
        });

        // Highlight related pins on map when hovering over communities
        $('body').on('mouseenter', '.map-point', function() {
          var url = $(this).find('a').attr('href');
          map.setFilter('points-hover', ['!=', 'url', url]);
        }).on('mouseleave', '.map-point', function() {
          map.setFilter('points-hover', ['==', 'url', '']);
        });
      });

    } else {
      // FOR OLD SCHOOL MAPBOX JS (ie9,10)
      if (typeof L === 'undefined') { return; }
      L.mapbox.accessToken = 'pk.eyJ1IjoidHNxdWFyZWQxMDE3IiwiYSI6ImNpdG5hdXJnYTAzcmsyb24waW42MTlsY24ifQ.8hBEeKstyMlXhbK8mSxJoA';

      // Convert given zoom and centers to something readable by raster mapbox js
      var zoom = Math.ceil(startZoom)+1;
      var center = [startCenter[1],startCenter[0]];

      // Init map
      map = new L.mapbox.Map('map', null, { zoomControl: false }).setView(center, zoom);

      var rasterTileLayer = L.mapbox.styleLayer('mapbox://styles/tsquared1017/cj8c5fqt57w1q2slaz7ca5t7a').addTo(map);

      // Add loaded class when the raster tile layer is up and runnin
      rasterTileLayer.on('load', function(e) {
        $('#map').addClass('loaded');
      });

      // disable drag and zoom handlers
      map.scrollWheelZoom.disable();

      // Add mapbox nav controls (styling overrided in _maps.scss)
      new L.Control.Zoom({ position: 'topright' }).addTo(map);

      if ($mapPoints.length === 0) { return; }

      pointsLayer = L.mapbox.featureLayer(null, {id: 'points', 'type': 'symbol'}).addTo(map);

      // Give layers proper icons
      pointsLayer.on('layeradd', function(e) {
        var marker = e.layer,
            feature = marker.feature;
        marker.setIcon(L.icon(feature.properties.icon));
        marker.unbindPopup();
      });

      // Add click event
      pointsLayer.on('click', function(e) {
        if(e.layer.feature.properties.enabled) {
          location.href = e.layer.feature.properties.url;
        }
      });

      // Add all map points
      _addMapPoints();

    }

    // After render and titles loaded hide the
    // overlaying ::before element on the map
    // to reveale the map to avoid the flashing of
    // the light green base layer
    map.on('render', _isMapLoaded);
    function _isMapLoaded() {
      if (!$('#map').is('.loaded')) {
        if (map.loaded()) {
          $('#map').addClass('loaded');
        }
      } else {
        // if it's already loaded then stop the call
        _mapOff();
      }
    }
    function _mapOff() {
      map.off('render', _isMapLoaded);
    }

  }

  function _addMapPoints() {
    var $mapPoints = $('.map-point:not(.mapped)');
    if ($mapPoints.length) {

      if (mapboxGlSupported) {

        // Cull map points from DOM
        $mapPoints.each(function(){
          var $this = $(this);
          $this.addClass('mapped');
          if ($this.attr('data-lat') !== '') {
            mapPointsData.features.push({
              'type': 'Feature',
              'geometry': {
                'type': 'Point',
                'coordinates': [parseFloat($this.attr('data-lng')), parseFloat($this.attr('data-lat'))]
              },
              'properties': {
                'title': $this.attr('data-title'),
                'url': $this.attr('data-url'),
                'enabled': !$this.hasClass('disabled')
              }
            });
          }
        });

        // Add points to source
        map.getSource('points').setData(mapPointsData);

        // Center
        var bounds = new mapboxgl.LngLatBounds();
        mapPointsData.features.forEach(function(feature) {
          bounds.extend(feature.geometry.coordinates);
        });
        map.fitBounds(bounds, {padding: 150});

        // Resize map
        map.resize();

      } else {

        // Cull map points from DOM
        $mapPoints.each(function(){
          var $this = $(this).addClass('mapped');
          if ($this.attr('data-lat') !== '') {
            mapPointsData.features.push({
              'type': 'Feature',
              'geometry': {
                'type': 'Point',
                'coordinates': [parseFloat($this.attr('data-lng')), parseFloat($this.attr('data-lat'))]
              },
              'properties': {
                'title': $this.attr('data-title'),
                'url': $this.attr('data-url'),
                'enabled': !$this.hasClass('disabled'),
                'icon' : {
                  'iconUrl': '/app/themes/envisioningjustice/dist/images/map-pin-black.svg',
                  'iconSize': [25, 50],
                  'iconAnchor': [12, 25],
                },
              },
            });
          }
        });

        // Add geoJson source
        pointsLayer.setGeoJSON(mapPointsData.features);

        // Center with new map points added
        map.setView(pointsLayer.getBounds().getCenter());

      }
    }
  }

  // Handles main nav
  function _initNav() {
    // SEO-useless nav toggler
    $('<button class="menu-toggle"><span class="text">Menu</span> <span class="menu-bar"></span></button>')
      .prependTo('.site-header .container > .-inner');

    $document.on('click', '.menu-toggle', function(e) {
      if ($('.site-nav').is('.-active')) {
        _hideMobileNav();
      } else {
        _showMobileNav();
      }
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
      var category = $load_more.attr('data-category') ? $load_more.attr('data-category') : '';
      var page = parseInt($load_more.attr('data-page-at'));
      var past_events = parseInt($load_more.attr('data-past-events'));
      var per_page = parseInt($load_more.attr('data-per-page'));
      var prox_zip = (post_type==='event') ? parseInt($load_more.attr('data-prox-zip')) : '';
      var prox_miles = (post_type==='event') ? parseInt($load_more.attr('data-prox-miles')) : '';
      var more_container = $load_more.parents('.section').find('.load-more-container');
      loadingTimer = setTimeout(function() { more_container.addClass('loading'); }, 500);

      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          data: {
              action: 'load_more_posts',
              post_type: post_type,
              category: category,
              past_events: past_events,
              page: page+1,
              per_page: per_page,
              prox_zip: prox_zip,
              prox_miles: prox_miles
          },
          success: function(data) {
            var $data = $(data);

            if (loadingTimer) { clearTimeout(loadingTimer); }
            more_container.append($data).removeClass('loading');

            $load_more.attr('data-page-at', page+1);
            if (post_type==='event' || post_type==='resource') {
              _addMapPoints();
            }

            if (more_container.is('.masonry')) {
              if (breakpoint_medium) {
                more_container.masonry('appended', $data, true);
              }
            }

            // Hide load more if last page
            if ($load_more.attr('data-total-pages') <= page + 1) {
              $load_more.addClass('hide');
            }
          }
      });
    });
  }

  function _initStorySubmit() {
    // Handle ajax submit of new Story
    $document.on('click', 'form.new-story-form button[type=submit]', function(e) {
      var $form = $(this).closest('form');
      $form.validate({
        messages: {
          story_name: 'Please leave us your name or a title for the story',
          story_email: 'We will need a valid email to contact you at',
          story_content: 'This field is required. If you are attaching your story as an uploaded text document, please state so in this field'
        },
        errorPlacement: function(error, element) {
          console.log(element);
          error.insertAfter(element.parent('.input-wrap'));
        },
        submitHandler: function(form) {
          // only AJAXify if browser supports FormData (necessary for file uploads via AJAX, <IE10 = no go)
          if( window.FormData !== undefined ) {
            var formData = new FormData(form);
            // Show working state, disable submit button
            $form.addClass('working').find('button[type=submit]').prop('disabled', true).html('<span>Working</span>');
            $.ajax({
              url: wp_ajax_url,
              method: 'post',
              data: formData,
              dataType: 'json',
              mimeType: 'multipart/form-data',
              processData: false,
              contentType: false,
              cache: false,
              success: function(response) {
                if (response.success) {
                  form.reset();
                  $form.find('.files-attached').html('');
                  $form.addClass('hide');
                  $('.story-submitted-wrapper').removeClass('hide');
                  _scrollBody($form.closest('.section'));
                } else {
                  $('.story-submitted-wrapper').removeClass('hide');
                  $('.story-submitted-wrapper .response').html(response.data.message);
                }
              },
              error: function(response) {
                var message;
                if (!respond.data && response.responseText.match(/exceeds the limit/)) {
                  message = 'There was an error: files are larger than the accepted limit (100mb).';
                } else {
                  message = response.data ? response.data.message : 'There was an error uploading. Please try again.';
                }
                $('.story-submitted-wrapper').removeClass('hide');
                $('.story-submitted-wrapper .response').html(message);
              }
            }).always(function() {
              // Re-enable form
              $form.removeClass('working').find('button[type=submit]').prop('disabled', false).html('<span>Submit</span>');
            });
          } else {
            form.submit();
          }
        }
      });
    });

    // Handle file input interaction
    $document.on('change', '.new-story-form input[type=file]', function(e) {
      var files = $(this).prop('files');
      var $files_label = $('label[for='+$(this).attr('id')+']');
      var $files_attached = $(this).closest('form').find('.files-attached');
      if (files.length) {
        $files_label.html('('+files.length+') File(s) Attached');
        for (var i=0;i<files.length;i++) {
          $files_attached.append('<p>'+files[i].name+'</p>');
        }
        $(this).attr('data-content', 'Click to replace file(s)');
      } else {
        $(this).attr('data-content', 'Click to add file(s)');
        $files_label.html('Story Images and/or Files (optional)');
        $files_attached.html('');
      }
    });
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
