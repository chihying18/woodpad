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
        			 '/hqdefault.jpg" class="img-responsive"></a>');
    });

    /* $('#seriesTabs .item:eq(0)').tab('show');
	$('#seriesTabs .item:eq(0)').addClass('active'); */

	new WOW().init();
	// var initModel = $('.product-carousel .item:eq(0)').data('model');
	$('.carousel').slick({
		//dots: true,
		infinite: true,
		slidesToShow: 4,
		slidesToScroll: 1,
		centerMode: true,
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

	/* $('.video-carousel').slick({
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
	}); */

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

	/* $('#seriesTabs .item').click(function(e) {
		e.preventDefault();
		$('#seriesTabs .item').removeClass('active');
		$(this).addClass('active');
	}); */

	/* $('.nav-sticky a[data-toggle="tab"]').on('show.bs.tab', function (e) {
		var closetSticky = gStickyPos[$(this).parents('.nav-sticky').data('index')];
		$(window).off('scroll');
		$(window).scrollTop(closetSticky.pos);
		$('.nav-sticky.navbar-fixed-top').removeClass('navbar-fixed-top');
		$(window).on('scroll', handleStickies);
		recalcPos();
	}); */

	$('.nav-item').click(function(e) {
		// e.preventDefault();
		$('#tabUpgrades > li.active').each(function() {
			var me = $(this);
			me.removeClass('active');
			$(me.children('.nav-link').attr('href')).collapse('hide');
		});
		$('#tabUpgradeContents > .active').removeClass('active');
		var target = $(this).find('.nav-link:eq(0)').attr('href');
		$('.nav-item > .nav-link[href="' + target + '"]').each(function() {
			var child = $(this);
			child.parent().addClass('active');
		});
	});

	$('.pane-upgrade .up-collapse').click(function(e) {
		$(this).closest('.pane-upgrade').collapse('hide');
	});

	$('a[data-action="filter"]').click(function(e) {
		var filter = $(this).data('filter');
		$('#funcPills .nav-pills li[data-filter="' + filter + '"]').click();
	});

	$(window).resize(function(e) {
		recalcPos();
	});

	playYouTubeModal();

	$('#funcPills .nav-pills li').click(function(e) {
		e.preventDefault();
		var btn = $(this);
		if (btn.hasClass('disabled')) {
			e.stopPropagation();
			return;
		}
		btn.toggleClass('active');
		var actives = $('#funcPills .nav-pills li.active');
		if (actives.length == 0) {
			$('#productDetail .product-item').removeClass('hide');
			$('#funcPills .nav-pills li').removeClass('disabled');
			return;
		}
		$('#productDetail .product-item').addClass('hide');
		// update product list
		var onModels = new Array();
		actives.each(function(idx) {
			var filter = filters[$(this).data('filter')];
			if (idx == 0) {
				$('#productDetail .product-item').each(function() {
					var me = $(this);
					var theModel = me.data('model');
					if (filter.indexOf(theModel) != -1) {
						me.removeClass('hide');
						onModels.push(theModel);
					}
				});
			} else {
				$('#productDetail .product-item:visible').each(function() {
					var me = $(this);
					var theModel = me.data('model');
					if (filter.indexOf(theModel) == -1) {
						me.addClass('hide');
						var pos = onModels.indexOf(theModel);
						onModels.splice(pos, 1);
					}
				});
			}
		});
		// enable/disable filter options based on new product list
		$('#funcPills .nav-pills li').each(function() {
			var filter = filters[$(this).data('filter')];
			// var common = filter.filter(value => -1 !== onModels.indexOf(value));
			var common = filter.filter(function(item, idx, array) {
				if (onModels.indexOf(item) != -1)
					return item;
			});
			if (common.length == 0) {
				$(this).addClass('disabled');
			} else {
				$(this).removeClass('disabled');
			}
		});

		recalcPos();
		// $(window).scrollTop($('#productDetail').offset().top - $('#seriesTabs').outerHeight());
	});

	$('.link-buy').click(function(e) {
		if (typeof gtag === 'function') {
			gtag('event', 'shop-visit', {
				'event_label':  'shop - ' + $(this).data('shop'),
				'event_category': 'engagement - ' + $(this).data('sku')
			});
		}
	});

	$('[data-toggle="tooltip"]').tooltip();

	$('a.page-scroll').bind('click', function(event) {
        var anchor = $(this);
        var scrollTop = $(anchor.attr('href')).offset().top;
        var offset = anchor.data('offset');
        if (offset != undefined)
        	scrollTop -= offset;
        $('html, body').stop().animate({
            scrollTop: scrollTop
        }, 1500, 'easeOutExpo');
        event.preventDefault();
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


