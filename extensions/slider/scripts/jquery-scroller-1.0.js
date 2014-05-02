/**
 * jQuery Scroller plugin v1.0
 * http://www.sample.iastate.edu/examples/scroller/
 *
 * Copyright 2012 Iowa State University and ITS Web Development Services
 */
(function($)
{
	/**
	 * Initilize the scroller on a slideshow container element
	 *
	 * @param object opts
	 *                - autoplay: boolean whether to start playing the slideshow (default: false)
	 *                - duration: integer seconds between slide transitions (default: 5)
	 *                - transition: the type of transition between slides [fade, scroll, swipe] (default: fade)
	 *                - init: function called before the slideshow is initialized
	 *                - preTransition: function called before every slide transition
	 * @return jQuery the slideshow container element
	 */
	$.fn.scroller = function(opts)
	{
		if (!('localStorage' in window))
		{
			window.localStorage = {
				getItem: function(){},
				setItem: function(){}
			}
		}
		opts = opts || {};
		return this.each(function()
		{
			// Initial setup
			var scroller = $(this),
				paneContainer = $(this).find('.slide-container > ul'),
				panes = paneContainer.eq(0).children('li'),
				navsAll = $(this).find('.nav-container > ul').children('li').children('span'),
				navs = navsAll.filter(function()
				{
					var pr = $(this).parent();
					return !(pr.hasClass('prev') || pr.hasClass('next') ||  pr.hasClass('slideshow'));
				}),
				slideWidth = scroller.width(),
				slideHeight = scroller.height(),
				lock = 0,
				running = opts.autoplay || 'true',
				transition = opts.transition || 'fade',
				idx = 0;
			if (transition == 'scroll')
			{
				var tw = slideWidth * panes.length;
				paneContainer.css({
					position: 'relative',
					width: tw
				});
				panes.css({
					display: 'block',
					float: 'left',
					height: slideHeight,
					position: 'relative',
					width: slideWidth
				});
			}
			else
			{
				paneContainer.css({
					position: '',
					width: ''
				});
				panes.css({
					display: '',
					float: '',
					height: '',
					position: '',
					width: ''
				});
			}
			if (panes.filter('.active').length == 0)
			{
				panes.add(navs).removeClass('active');
				panes.eq(idx).add(navs.eq(idx)).addClass('active');
			}
			else 
			{
				idx = $.inArray(panes.filter('.active').get(0), panes);
				if (transition == 'scroll')
				{
					paneContainer.css({
						left: -1 * slideWidth * idx
					});
				}
			}
			if (transition == 'swipe')
			{
				var slicePane = scroller.find('.swipe-container');
				if (slicePane.length == 0)
				{
					slicePane = $('<div class="swipe-container">').appendTo(scroller).hide();
					var left = 0,
						slices = opts.slices || 16,
						w = Math.floor(slideWidth / slices),
						wl = slideWidth - w * (slices - 1);
					for (var i = slices; i > 0; i--)
					{
						$('<div class="swipe-slide">').css({
							width: i == 1 ? wl : w, left: left,
							'background-position': '-'+ left +'px 0'
						}).appendTo(slicePane);
						left += i == 1 ? wl : w;
					}
				}
			}
			opts.init && opts.init(panes.eq(idx), panes, scroller);
			// Reset previous
			clearInterval(scroller.data('timer.scroller'));
			scroller.unbind('.scroller');
			navsAll.unbind('.scroller');
			// Start anew
			scroller.bind('move.scroller', function(e, i, dir)
			{
				if (i == idx)
					return false;
				var old = idx;
				idx = i;
				opts.preTransition && opts.preTransition(panes.eq(old), panes.eq(idx), panes, scroller);
				if (transition == 'swipe' && !($.browser.msie && $.browser.version < '9.0') && panes.eq(idx).find('img').length == 1)
				{
					dir = dir || (idx > old ? 'left' : 'right');
					lock = slices + 1;
					slicePane.show().children().css({
						'background-image': 'url("'+ panes.eq(idx).find('img').attr('src') +'")',
						opacity: 0
					}).each(function(j)
					{
						var self = $(this);
						setTimeout(function()
						{
							self.animate({opacity: 1}, function()
							{
								if (--lock != 1)
									return;
								panes.eq(old).removeClass('active').css('display', '');
								panes.eq(idx).addClass('active');
								slicePane.fadeOut(function()
								{
									lock--;
								});
							});
						}, (dir == 'left' ? slices - j : j) * 500/slices);
					});
				}
				else if (transition == 'scroll')
				{
					lock = 1;
					paneContainer.animate({
						left: -1 * slideWidth * idx
					}, 600, function()
					{
						lock--;
					});
					panes.eq(idx).addClass('active');
					panes.eq(old).removeClass('active');
				}
				else
				{
					lock = 1;
					panes.eq(old).css('z-index', 1);
					panes.eq(idx).css('z-index', 5).fadeIn(function()
					{
						lock--;
						panes.eq(old).removeClass('active').css('display', '');
					}).addClass('active');
				}
				navs.removeClass('active').eq(idx).addClass('active');
			});
			scroller.bind('prev.move.scroller', function()
			{
				var i = (idx - 1 + panes.length) % panes.length;
				scroller.trigger('move.scroller', [i, 'right']);
			});
			scroller.bind('next.move.scroller', function()
			{
				var i = (idx + 1) % panes.length;
				scroller.trigger('move.scroller', [i, 'left']);
			});
			scroller.bind('pause.slideshow.scroller', function()
			{
				clearInterval(scroller.data('timer.scroller'));
			});
			scroller.bind('play.slideshow.scroller', function()
			{
				clearInterval(scroller.data('timer.scroller'));
				scroller.data('timer.scroller', setInterval(function()
				{
					scroller.trigger('next.move.scroller');
//change number after duration for slide duration before transition
				}, (opts.duration || 5) * 1000));
			});
			scroller.bind('hover.scroller', function()
			{
				scroller.trigger('pause.slideshow.scroller');
			}, function()
			{
				if (running)
					scroller.trigger('play.slideshow.scroller');
			});
//Pause autoplay when mouse hovers over slides
			scroller.bind('mouseenter.scroller', function()
			{
				scroller.trigger('pause.slideshow.scroller');
			});
			scroller.bind('mouseleave.scroller', function()
			{
				if (running)
					scroller.trigger('play.slideshow.scroller');
			});
			navsAll.bind('click.scroller', function()
			{
				if (lock != 0)
					return;
				var pr = $(this).parent();
				if (pr.hasClass('slideshow'))
				{
					scroller.trigger((running ? 'pause' : 'play') +'.slideshow.scroller');
					running = !running;
					if (running)
					{
						setTimeout(function()
						{
							scroller.trigger('next.move.scroller');
						}, 150);
					}
					window.localStorage.setItem('slideshow', running +'');
					$(this)[(running ? 'add' : 'remove') +'Class']('playing')[(running ? 'remove' : 'add') +'Class']('paused');
				}
				else
				{
					if (pr.hasClass('prev'))
						scroller.trigger('prev.move.scroller');
					else if (pr.hasClass('next'))
						scroller.trigger('next.move.scroller');
					else
					{
						var i = $.inArray(this, navs);
						scroller.trigger('move.scroller', [i]);
					}
				}
				return false;
			});
			if (running)
			{
				scroller.trigger('play.slideshow.scroller');
				navsAll.parent('.slideshow').children('span').removeClass('paused').addClass('playing');
			}
		});
	};
}(jQuery));