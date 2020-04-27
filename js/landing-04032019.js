var gProducts, gCountryCode;
var gStickyPos = new Array;
var filters, gAnimateInProgress;

$(document).ready(function() {

	$('body').scrollspy({ target: '.spy-active' });

	$('#videoList .video').each(function() {
        var gridDiv = $(this);
        var ytVideoId = gridDiv.data('video');
        var ytVideoList = gridDiv.data('videoList');
        $(this).html('<div class="thumb"><img src="https://img.youtube.com/vi/' + ytVideoId +
        			 '/maxresdefault.jpg" class="img-responsive"></a>');
    });

    $('#seriesTabs .item:eq(0)').tab('show');
	$('#seriesTabs .item:eq(0)').addClass('active');

	new WOW().init();
	// var initModel = $('.product-carousel .item:eq(0)').data('model');
	$('.carousel').slick({
		//dots: true,
		infinite: true,
		slidesToShow: 6,
		slidesToScroll: 1,
		// centerMode: true,
		responsive: [{
			breakpoint: 1024,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 1,
			}
		}, {
			breakpoint: 800,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 2,
			}
		}, {
			breakpoint: 600,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 2, 
			}
		}, {
			breakpoint: 480,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
			}
		}]
	});

	$('.video-carousel').slick({
		//dots: true,
		slidesToShow: 3,
		slidesToScroll: 1,
		responsive: [{
			breakpoint: 1024,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 1,
			}
		}, {
			breakpoint: 800,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
			}
		}, {
			breakpoint: 480,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1, 
			}
		}]
	});

	// sticky navs

	$('.nav-sticky').each(function(idx) {
		var me = $(this);
		var startPos = me.offset();
		gStickyPos[idx] = {
			pos: startPos.top,
			height: me.outerHeight()
		};
		me.data('index', idx);
	});

	$(window).scroll(handleStickies);

	$('#seriesTabs .item').click(function(e) {
		e.preventDefault();
		$('#seriesTabs .item').removeClass('active');
		$(this).addClass('active');
	});

	$('.nav-sticky a[data-toggle="tab"]').on('show.bs.tab', function (e) {
		var closetSticky = gStickyPos[$(this).parents('.nav-sticky').data('index')];
		$(window).off('scroll');
		$(window).scrollTop(closetSticky.pos);
		$('.nav-sticky.navbar-fixed-top').removeClass('navbar-fixed-top');
		$(window).on('scroll', handleStickies);
		recalcPos();
	});

	$(window).resize(function(e) {
		recalcPos();
	});

	playYouTubeModal();

	$('#actions > a').click(function(e) {
		e.preventDefault();
		var btn = $(this);
		btn.toggleClass('active');
		var actives = $('#actions > a.active');
		if (actives.length == 0) {
			$('#productDetail .product-item').removeClass('hide');
			$('#productDetail .tab-pane').each(function() {
				var naDiv = $(this).find('.product-na');
				if ($(this).find('.product-item.hide').length > 0) {
					naDiv.show();
				} else {
					naDiv.hide();
				}
			});
			return;
		}
		$('#productDetail .product-item').addClass('hide');
		actives.each(function(idx) {
			var filter = filters[btn.data('filter')];
			$('#productDetail .product-item').each(function(idx) {
				var me = $(this);
				if (filter.indexOf(me.data('model')) != -1) {
					me.removeClass('hide');
				}
			});
		});
		$('#productDetail .tab-pane').each(function() {
			var naDiv = $(this).find('.product-na');
			if ($(this).find('.product-item.hide').length > 0) {
				naDiv.show();
			} else {
				naDiv.hide();
			}
		});
		$('.nav-sticky').each(function(idx) {
			gStickyPos[idx].pos = $(this).offset().top;
		});
		$(window).scrollTop($('#productDetail').offset().top - $('#seriesTabs').outerHeight());
	});

});

function handleStickies(e) {
	$('.nav-sticky').each(function(idx) {
		var me = $(this);
		var startPos = gStickyPos[idx].pos - gStickyPos[idx].height;
		var endDiv = $($(me.data('target')));
		var endPos = endDiv.offset().top + endDiv.outerHeight();
		var curPos = $(document).scrollTop();
		if (curPos > startPos &&
			curPos < endPos) {
			$(".nav-sticky.navbar-fixed-top").removeClass('navbar-fixed-top');
			recalcPos();
			me.addClass('navbar-fixed-top');
		} else {
			me.removeClass('navbar-fixed-top');
		}
	});
}

function playYouTubeModal() {
    var trigger = $("body").find('[data-target="#videoModal"]');
    trigger.click(function () {
    	var me = $(this);
        var theModal = me.data("target");
        var videoList =  me.data('videoList');
        if (videoList != undefined && videoList != null) {
        	var videoSRC = 'https://www.youtube.com/embed/videoseries?list=' +
        				   me.data('videoList');
        } else {
        	var videoSRC = 'https://www.youtube.com/embed/' +
        				   me.data('video') + '?';
        }
        var videoSRCauto = videoSRC + "&autoplay=1";
        $(theModal + ' iframe').attr('src', videoSRCauto);
        $(theModal + ' button.close').click(function () {
            $(theModal + ' iframe').attr('src', videoSRC);
        });
        $('.modal').click(function () {
            $(theModal + ' iframe').attr('src', videoSRC);
        });
    });
}

function recalcPos() {
	$('.nav-sticky').each(function(idx) {
		gStickyPos[idx].pos = $(this).offset().top;
	});
}
