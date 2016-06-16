/**
 * SmartBlog Theme Utilities 
 */
var Bunyad_Theme = (function($) {
	"use strict";
	
	var has_touch = false,
		responsive_menu = false,
		search_active = false;
	
	// module
	return {
		
		init: function() 
		{
			
			/**
			 * Comment form fields placeholders
			 */
			
			$('.comment-form .inline-field input').on('loaded keyup', function() {
				
				var label = $(this).parent().find('label'),
				    length = $(this).val(),
				    id = 'label-hide';
				
				if (length && !label.hasClass(id)) {
					label.addClass(id);
				}
				else if (!length) {
					label.removeClass(id);
				}
				
			}).trigger('loaded'); // trigger at load
	
			
			/**
			 * Top search handler
			 */
			$('.search-action .search').on('click', function() {
				
				var form = $('.top-bar .search-overlay, .main-head .search-overlay');
				search_active = true;
				
				// disable search if active
				if (form.hasClass('active')) {
					search_active = false;
				}
				
				form.toggleClass('active');
				form.siblings().toggleClass('active');
				
				return false;
			});
			
			$(document).on('click', function(e) {
				if (search_active && !$(e.target).parents('.search-overlay').length) {
					$('.search-action .search').click();
				}
			});
			
			// Reflow bug fix for webkit
			var nav = $('.navigation .menu ul').css('-webkit-box-sizing', 'border-box');
			requestAnimationFrame(function() {
				nav.css('-webkit-box-sizing', '');
			});
			
			
			// Fit videos to container
			$('.featured-vid, .post-content').fitVids();
			
			// Clickable links that shouldn't scroll to # anchor
			$('.top-actions .icon-hamburger, .post-counters .share > a').on('click', function(e) {
				e.preventDefault();
			});
			
			// Add overlays to footer instagram
			if ($('.mid-footer .instagram-pics').length) {
				var link = $('.mid-footer .clear a').clone();
				link.addClass('overlay').html('<i class="icon-instagram"></i>' + link.text());
				
				$('.instagram-pics a').each(function() {
					$(this).parent().append(link.prop('outerHTML'));
				});
			}
			
			// Fix hierarchy in default WP widget
			$('.widget_categories').find('.children').parents('ul').addClass('hierarchy');
						
			// Social icons tooltip
			$('.lower-footer .social-icons a, .share-links a').data('toggle', 'tooltip').tooltip({placement: 'top'});
			$('.social-icons a, .post-share-icons a').tooltip({placement: 'bottom'});
			
			// Back to top handler
			$('.back-to-top').on('click', function() {
				
				// Using both html, body until scrollingElement has more support
				$('html, body').animate({scrollTop: 0}, 800);
				return false;
			});
			
			// Setup all sliders
			this.sliders();
			
			// Justified gallerys init
			this.justified_galleries();
			
			// Setup mobile navigation
			this.responsive_nav();
			
			// Setup sticky top-bar
			this.sticky_bar();
						
			// Lightbox for images
			this.lightbox();
			
			// Likes system
			this.likes();
			
			// Prev/Next for posts
			this.post_nav();
			
			// add support for placeholder in IE9
			$('input, textarea').placeholder();
		},

		responsive_nav: function()
		{
			// detect touch capability dynamically
			$(window).on('touchstart', function() {
				has_touch = true;
				$('body').addClass('touch');
			});
			
			this.init_responsive_nav();
			this.touch_nav();
			var that = this;

			$(window).on('resize orientationchange', function() {
				that.init_responsive_nav();
			});		
		},
		
		/**
		 * Setup the responsive nav events and markup
		 */
		init_responsive_nav: function() {
			
			if ($(window).width() > 940 || responsive_menu) {
				return;
			}
			
			// Set responsive initialized
			responsive_menu = true;
			
			// Create off-canvas container
			var menu_contain = $('.mobile-menu-container');
			
			// No items for mobile menu? Grab from others
			if (!menu_contain.find('.mobile-menu').children().length) {
				// Merge existing menus
				var mobile_menu = menu_contain.find('.mobile-menu'),
				    top_menu = $('.top-nav .menu'), 
				    main_menu = $('.navigation .menu');
				
				if (top_menu.length) {
					mobile_menu.append(top_menu.children().clone());
				}
				
				if (main_menu.length) {
					mobile_menu.append(main_menu.children().clone());
				}
				
				menu_contain.appendTo('body');	
			}
			
			$('body').addClass('nav-off-canvas');
			
			// Setup mobile menu click handlers
			$('.mobile-menu li > a').each(function() {
				
				if ($(this).parent().children('ul').length) {
					$('<a href="#" class="chevron"><i class="icon icon-down-dir"></i></a>').appendTo($(this));
				}
			});
			
			$('.mobile-menu li .chevron').click(function() {
					$(this).closest('li').find('ul').first().slideToggle().parent().toggleClass('active item-active');
					return false;
			});
			
			// Menu open handler
			$('.top-actions .icon-hamburger').on('click', function() {
				$('.mobile-menu').addClass('active');
            	$('body').toggleClass('off-canvas-active');
            	$('html').toggleClass('hide-scroll');
			});
			
			
			// Off-canvas close
			$('.off-canvas .close').click(function() {
				$('body').toggleClass('off-canvas-active');
			});
		},

		/**
		 * Setup touch for touch devices
		 */
		touch_nav: function() {
			
			var targets = $('.menu:not(.mobile-menu) a'),
				open_class = 'item-active',
				child_tag = 'ul, .mega-menu';
			
			targets.each(function() {
				
				var $this = $(this),
					$parent = $this.parent('li'),
					$siblings = $parent.siblings().find('a');
				
				$this.click(function(e) {
					
					if (!has_touch) {
						return;
					}
					
					var $this = $(this);
					e.stopPropagation();
					
					$siblings.parent('li').removeClass(open_class);
					
					// has a child? open the menu on tap
					if (!$this.parent().hasClass(open_class) && $this.next(child_tag).length > 0 && !$this.parents('.mega-menu.links').length) {
						e.preventDefault();
						$this.parent().addClass(open_class);
					}
				});
			});
			
			// close all menus
			$(document).click(function(e) {
				if (!$(e.target).is('.menu') && !$(e.target).parents('.menu').length) {
					targets.parent('li').removeClass(open_class);
				}
			});
		},
		
		/**
		 * Setup unique justified galleries
		 */
		justified_galleries: function()
		{
			// Not using justified gallery?
			var type = $('.the-post').data('gallery');
			if (!$.fn.justifiedGallery ||  (type && type != 'justified')) {
				return;
			}
			
			/**
			 * Justified post galleries
			 */
			var gallery = '.post-content .gallery';
			
			if ($(gallery).length) {
				
				$(gallery).each(function() {
					
					var match, columns = 1;
					
					match = $(this).prop('class').match(/columns-(\d*)/);
					if (match) {
						columns = match[1];
					} 
					
					$(this).find('a').each(function() { 
						$(this).parents('.gallery-item').before(this).remove(); 
					});
					
					// Adjust desired height based on column count
					var heights = {
						1: 650,
						2: 450,
						3: 350,
						4: 300,
						5: 250,
						6: 200,
						7: 150,
						8: 150,
						9: 150
					};
					
					$('.post-content .gallery').justifiedGallery({
						selector: ' > a',
						rowHeight: heights[columns],
						maxRowHeight: '125%',
						margins: 6,
						imagesAnimationDuration: 0,
						lastRow: 'justify'
					});
				});
			}
		},
		
		/**
		 * Setup prev/next post navigation
		 */
		post_nav: function()
		{
			if (!$('body').hasClass('single-post')) {
				return;
			}
				
			var post_nav = $('.post-nav-overlay');
			
			if (!post_nav.length) {
				return;
			}					
			
			/**
			 * Pre-compute for performance
			 * 
			 * Show when post-content is in viewport and hide when footer 
			 * becomes visible in view.
			 */ 
			var ele     = $('.the-post .post-content'), 
			    offset  = ele.offset(),
			    show_at = offset.top,
			    hide_at = $('.main-footer').offset().top,
			    win_height = $(window).height(), 
			    scroll;
			
			// Show at certain scroll
			$(window).on('scroll', function() {
				
				if ($(window).width() < 940) {
					return;
				}
				
				// add viewport height to scrollTop
				scroll = $(window).scrollTop() + win_height;
				
				if (scroll > show_at && scroll < hide_at) {
					
					if (!post_nav.hasClass('active')) {
						post_nav.addClass('active');
					}
				}
				else {
					post_nav.removeClass('active');
				}
			});
			
		},

		/**
		 * Setup sticky bar if enabled
		 */
		sticky_bar: function()
		{
			var nav = $('.top-bar-content'),
			    smart = false,
			    is_sticky = false,
			    prev_scroll = 0,
			    cur_scroll;
			    
			// Target sticky based on header layout
			if (nav.length) {
			
				// default 
				var pos_ele  = $('.main-head .title'),
				    nav_top  = (pos_ele.length ? pos_ele.offset().top : nav.offset().top),
				    hide_at  = nav.length ? nav.offset().top : 0;
			}
			else {
				
				// alternate
				var nav = $('.navigation'),
				    nav_top  = nav.offset().top,
				    hide_at  = nav.offset().top;
				
			}
			
			// not enabled?
			if (!nav.data('sticky-bar')) {
				return;
			}
			
			if (nav.find('.sticky-logo').length) {
				nav.addClass('has-logo');
			}
			
			// disable the sticky nav
			var remove_sticky = function() {
				
				// check before dom modification 
				if (is_sticky) {
					nav.removeClass('sticky-bar'); 
				}
			}
			
			// make the nav sticky
			var sticky = function() {

				if (!nav.data('sticky-bar') || $(window).width() < 800) {
					return;
				}
				
				cur_scroll = $(window).scrollTop();
				is_sticky  = nav.hasClass('sticky-bar');
				
				// make it sticky when viewport is scrolled beyond the navigation
				if ($(window).scrollTop() > nav_top) {
					
					// for smart sticky, test for scroll change
					if (smart && (!prev_scroll || cur_scroll > prev_scroll)) {
						remove_sticky();
					}
					else {
						
						if (!nav.hasClass('sticky-bar')) {
							nav.addClass('sticky-bar no-transition');
						
							setTimeout(function() { 
								nav.removeClass('no-transition'); 
							}, 100);
						}
					}
					
					prev_scroll = cur_scroll;
					
				} else {
					
					// hide at a certain point
					if ($(window).scrollTop() <= hide_at) {
						remove_sticky();
					}
				}
			};

			sticky();

			$(window).on('scroll resize orientationchange', function() {
				sticky();
			});
			
		},
		
		/**
		 * Setup sliders and carousels
		 */
		sliders: function()
		{
			
			/**
			 * Main featured post slider
			 */
			var slider = $('.main-slider .slides');
			
			slider.on('init', function(e, slick) {
				
				// Fix for subpixel rounding errors on webkit
				slider.find('.overlay').each(function() {
					
					var width = Math.round($(this).outerWidth() / 2),
					    height = Math.round($(this).outerHeight() / 2);
					
					if (width < 50 || height < 50) {
						return;
					}
					
					$(this).css('-webkit-transform', 'translate(-' + width + 'px, -' + height + 'px)');
				});
				
			})
			
			slider.slick({
				  slidesToShow: 1,
				  slidesToScroll: 1,
				  fade: slider.data('animation') == 'fade' ? true : false,
				  cssEase: slider.parent().hasClass('alt-slider') ? 'ease-in' : 'linear',
				  autoplay: slider.data('autoplay') ? true : false,
				  autoplaySpeed: slider.data('speed'),
				  arrows: true,
				  prevArrow: '<a href="#" class="prev-arrow"><i class="icon icon-arrow-left"></i></a>',
				  nextArrow: '<a href="#" class="next-arrow"><i class="icon icon-arrow-right"></i></a>',
				  touchThreshold: 50,
				  dots: true,
				  appendDots: slider.parent().find('.dot-nav')
				});
			
			
			var slider_nav = $('.slider-nav'), 
			    has_slider_nav;

			
			// Add a carousel to the slider nav if more than 3 posts
			if (slider_nav.children().length > 3) {
				
				has_slider_nav = true;
				
				slider_nav.slick({
					slidesToShow: 3,
					slidesToScroll: 3,
					arrows: false,
					//focusOnSelect: false,
					
					// Disable on 940 breakpoint
					responsive: [{
						breakpoint: 940,
						settings: 'unslick'
					}]
				});
				
				$(window).on('resize orientationchange', (function() {
					if ($(window).width() < 940) {
						has_slider_nav = false;
					}
				})());
				
			}
			
			slider_nav.find('.post').on('click', function() {
				slider.slick('slickGoTo', $(this).data('slick-index') || $(this).index());
			});
			
			// First slide active
			slider_nav.find('.post:first-child, .slick-current').addClass('active');
			
			
			// Sync slider with navigation
			slider.on('beforeChange', function(e, slick, prev, current) {
				
				// slide nav carousel if it exists
				if (has_slider_nav) {
					
					slider_nav.slick('slickGoTo', current);
					
					slider_nav.find('.post')
						.removeClass('active')
						.end().find('[data-slick-index=' + current + ']').addClass('active');
				}
				else {
					slider_nav.find('.post')
						.removeClass('active')
						.eq(current).addClass('active');
				}
				
			});
			
			
			/**
			 * Featured gallery slider
			 */
			$('.gallery-slider').slick({
				infinite: true,
				slidesToShow: 1,
				prevArrow: '<a href="#" class="prev-arrow"><i class="icon icon-arrow-left"></i></a>',
				nextArrow: '<a href="#" class="next-arrow"><i class="icon icon-arrow-right"></i></a>',
				slidesToScroll: 1
			});


			/**
			 * Related posts carousel
			 */
			$('.related-posts .posts').slick({
				infinite: true,
				slidesToShow: 3,
				prevArrow: '<span class="prev-post"><i class="icon-angle-left"></i></span>',
				nextArrow: '<span class="next-post"><i class="icon-angle-right"></i></span>',
				appendArrows: $('.related-posts .navigate'),
				slidesToScroll: 1,
				
				// Adjusted for breakpoints
				responsive: [{
					breakpoint: 767,
					settings: {slidesToShow: 2}
				},
				{
					breakpoint: 415,
					settings: {slidesToShow: 1}
				}]
			});
			
			/**
			 * Homepage carousel
			 */
			$('.posts-carousel .posts').slick({
				infinite: true,
				slidesToShow: 4,
				prevArrow: '<a class="prev-post"><i class="icon icon-left-open-big"></i></a>',
				nextArrow: '<a class="next-post"><i class="icon icon-right-open-big"></i></a>',
				appendArrows: $('.posts-carousel .navigate'),
				slidesToScroll: 4,
				
				responsive: [{
					breakpoint: 940,
					settings: {slidesToShow: 3, slidesToScroll: 3}
				},
				{
					breakpoint: 767,
					settings: {slidesToShow: 2, slidesToScroll: 2}
				},
				{
					breakpoint: 415,
					settings: {slidesToShow: 1, slidesToScroll: 1}
				}]
			});
			
			
			// Disable anchor jump on arrows
			$('.slick-arrow').on('click', function(e) {
				e.preventDefault();
			});

		},
		
		/**
		 * Setup Likes system
		 */
		likes: function()
		{
			if (typeof Sphere_Plugin == 'undefined') {
				return;
			}
			
			$('.count-heart').on('click', function() {
				
				var like = $(this);
				
				if (like.hasClass('voted')) {
					return false;
				}
				
				// Register the vote!
				$.post(Sphere_Plugin.ajaxurl, {action: 'sphere_likes', id: $(this).data('id')}, function(data) {
					
					if (data === Object(data)) {
						like.addClass('voted animate').find('.number').html(data.count);
					}
					
				}, 'json');
				
				return false;
				
			});
		},
		
		/**
		 * Setup Lightbox
		 */
		lightbox: function() 
		{
			// disabled on mobile screens
			if (!$.fn.magnificPopup || $(window).width() < 768) {
				return;
			}

			// filter to handle valid images only
			var filter_images = function() {
				
				if (!$(this).attr('href')) {
					return false;
				}
				
				return $(this).attr('href').match(/\.(jpe?g|png|bmp|gif)$/); 
			};	
			
			var mfp_init = {
				type: 'image',
				tLoading: '',
				mainClass: 'mfp-fade mfp-img-mobile',
				removalDelay: 300,
				callbacks: {
					afterClose: function() {
						if (this._lastFocusedEl) {
							$(this._lastFocusedEl).addClass('blur');
						}
					}
				}
			};
			
			/**
			 * Handle Galleries in post
			 */
			
			var gal_selectors = '.gallery-slider, .post-content .gallery, .post-content .tiled-gallery';
			
			// filter to only tie valid images
			$(gal_selectors).find('a').has('img').filter(filter_images).addClass('lightbox-gallery-img');
			
			// attach the lightbox as gallery
			$(gal_selectors).magnificPopup($.extend({}, mfp_init, {
				delegate: '.lightbox-gallery-img',
				gallery: {enabled: true},
				image: {
					titleSrc: function(item) {
						var image = item.el.find('img'), caption = item.el.find('.caption').html();
						return (caption || image.attr('title') || ' ');
					}
				}
			}));
			
			// Non-gallery images in posts
			var selector = $('.post-content, .main .featured').find('a:not(.lightbox-gallery-img)').has('img');
			
			selector.add('.post-content, .main .featured, .lightbox-img')
				.filter(filter_images)
				.magnificPopup(mfp_init);
		}
		
	}; // end return
	
})(jQuery);

// load when ready
jQuery(function($) {
	Bunyad_Theme.init();
});


/**
 * Required plugins and 3rd Party Libraries
 */

/**
 * Bootstrap: tooltip.js v3.2.0
 * http://getbootstrap.com/javascript/#tooltip
 * Licensed under MIT 
 */
+function(t){"use strict";function e(e){return this.each(function(){var o=t(this),n=o.data("bs.tooltip"),s="object"==typeof e&&e;(n||"destroy"!=e)&&(n||o.data("bs.tooltip",n=new i(this,s)),"string"==typeof e&&n[e]())})}var i=function(t,e){this.type=this.options=this.enabled=this.timeout=this.hoverState=this.$element=null,this.init("tooltip",t,e)};i.VERSION="3.2.0",i.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0}},i.prototype.init=function(e,i,o){this.enabled=!0,this.type=e,this.$element=t(i),this.options=this.getOptions(o),this.$viewport=this.options.viewport&&t(this.options.viewport.selector||this.options.viewport);for(var n=this.options.trigger.split(" "),s=n.length;s--;){var r=n[s];if("click"==r)this.$element.on("click."+this.type,this.options.selector,t.proxy(this.toggle,this));else if("manual"!=r){var a="hover"==r?"mouseenter":"focusin",p="hover"==r?"mouseleave":"focusout";this.$element.on(a+"."+this.type,this.options.selector,t.proxy(this.enter,this)),this.$element.on(p+"."+this.type,this.options.selector,t.proxy(this.leave,this))}}this.options.selector?this._options=t.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},i.prototype.getDefaults=function(){return i.DEFAULTS},i.prototype.getOptions=function(e){return e=t.extend({},this.getDefaults(),this.$element.data(),e),e.delay&&"number"==typeof e.delay&&(e.delay={show:e.delay,hide:e.delay}),e},i.prototype.getDelegateOptions=function(){var e={},i=this.getDefaults();return this._options&&t.each(this._options,function(t,o){i[t]!=o&&(e[t]=o)}),e},i.prototype.enter=function(e){var i=e instanceof this.constructor?e:t(e.currentTarget).data("bs."+this.type);return i||(i=new this.constructor(e.currentTarget,this.getDelegateOptions()),t(e.currentTarget).data("bs."+this.type,i)),clearTimeout(i.timeout),i.hoverState="in",i.options.delay&&i.options.delay.show?void(i.timeout=setTimeout(function(){"in"==i.hoverState&&i.show()},i.options.delay.show)):i.show()},i.prototype.leave=function(e){var i=e instanceof this.constructor?e:t(e.currentTarget).data("bs."+this.type);return i||(i=new this.constructor(e.currentTarget,this.getDelegateOptions()),t(e.currentTarget).data("bs."+this.type,i)),clearTimeout(i.timeout),i.hoverState="out",i.options.delay&&i.options.delay.hide?void(i.timeout=setTimeout(function(){"out"==i.hoverState&&i.hide()},i.options.delay.hide)):i.hide()},i.prototype.show=function(){var e=t.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(e);var i=t.contains(document.documentElement,this.$element[0]);if(e.isDefaultPrevented()||!i)return;var o=this,n=this.tip(),s=this.getUID(this.type);this.setContent(),n.attr("id",s),this.$element.attr("aria-describedby",s),this.options.animation&&n.addClass("fade");var r="function"==typeof this.options.placement?this.options.placement.call(this,n[0],this.$element[0]):this.options.placement,a=/\s?auto?\s?/i,p=a.test(r);p&&(r=r.replace(a,"")||"top"),n.detach().css({top:0,left:0,display:"block"}).addClass(r).data("bs."+this.type,this),this.options.container?n.appendTo(this.options.container):n.insertAfter(this.$element);var l=this.getPosition(),h=n[0].offsetWidth,f=n[0].offsetHeight;if(p){var u=r,d=this.$element.parent(),c=this.getPosition(d);r="bottom"==r&&l.top+l.height+f-c.scroll>c.height?"top":"top"==r&&l.top-c.scroll-f<0?"bottom":"right"==r&&l.right+h>c.width?"left":"left"==r&&l.left-h<c.left?"right":r,n.removeClass(u).addClass(r)}var g=this.getCalculatedOffset(r,l,h,f);this.applyPlacement(g,r);var y=function(){o.$element.trigger("shown.bs."+o.type),o.hoverState=null};t.support.transition&&this.$tip.hasClass("fade")?n.one("bsTransitionEnd",y).emulateTransitionEnd(150):y()}},i.prototype.applyPlacement=function(e,i){var o=this.tip(),n=o[0].offsetWidth,s=o[0].offsetHeight,r=parseInt(o.css("margin-top"),10),a=parseInt(o.css("margin-left"),10);isNaN(r)&&(r=0),isNaN(a)&&(a=0),e.top=e.top+r,e.left=e.left+a,t.offset.setOffset(o[0],t.extend({using:function(t){o.css({top:Math.round(t.top),left:Math.round(t.left)})}},e),0),o.addClass("in");var p=o[0].offsetWidth,l=o[0].offsetHeight;"top"==i&&l!=s&&(e.top=e.top+s-l);var h=this.getViewportAdjustedDelta(i,e,p,l);h.left?e.left+=h.left:e.top+=h.top;var f=h.left?2*h.left-n+p:2*h.top-s+l,u=h.left?"left":"top",d=h.left?"offsetWidth":"offsetHeight";o.offset(e),this.replaceArrow(f,o[0][d],u)},i.prototype.replaceArrow=function(t,e,i){this.arrow().css(i,t?50*(1-t/e)+"%":"")},i.prototype.setContent=function(){var t=this.tip(),e=this.getTitle();t.find(".tooltip-inner")[this.options.html?"html":"text"](e),t.removeClass("fade in top bottom left right")},i.prototype.hide=function(){function e(){"in"!=i.hoverState&&o.detach(),i.$element.trigger("hidden.bs."+i.type)}var i=this,o=this.tip(),n=t.Event("hide.bs."+this.type);return this.$element.removeAttr("aria-describedby"),this.$element.trigger(n),n.isDefaultPrevented()?void 0:(o.removeClass("in"),t.support.transition&&this.$tip.hasClass("fade")?o.one("bsTransitionEnd",e).emulateTransitionEnd(150):e(),this.hoverState=null,this)},i.prototype.fixTitle=function(){var t=this.$element;(t.attr("title")||"string"!=typeof t.attr("data-original-title"))&&t.attr("data-original-title",t.attr("title")||"").attr("title","")},i.prototype.hasContent=function(){return this.getTitle()},i.prototype.getPosition=function(e){e=e||this.$element;var i=e[0],o="BODY"==i.tagName;return t.extend({},"function"==typeof i.getBoundingClientRect?i.getBoundingClientRect():null,{scroll:o?document.documentElement.scrollTop||document.body.scrollTop:e.scrollTop(),width:o?t(window).width():e.outerWidth(),height:o?t(window).height():e.outerHeight()},o?{top:0,left:0}:e.offset())},i.prototype.getCalculatedOffset=function(t,e,i,o){return"bottom"==t?{top:e.top+e.height,left:e.left+e.width/2-i/2}:"top"==t?{top:e.top-o,left:e.left+e.width/2-i/2}:"left"==t?{top:e.top+e.height/2-o/2,left:e.left-i}:{top:e.top+e.height/2-o/2,left:e.left+e.width}},i.prototype.getViewportAdjustedDelta=function(t,e,i,o){var n={top:0,left:0};if(!this.$viewport)return n;var s=this.options.viewport&&this.options.viewport.padding||0,r=this.getPosition(this.$viewport);if(/right|left/.test(t)){var a=e.top-s-r.scroll,p=e.top+s-r.scroll+o;a<r.top?n.top=r.top-a:p>r.top+r.height&&(n.top=r.top+r.height-p)}else{var l=e.left-s,h=e.left+s+i;l<r.left?n.left=r.left-l:h>r.width&&(n.left=r.left+r.width-h)}return n},i.prototype.getTitle=function(){var t,e=this.$element,i=this.options;return t=e.attr("data-original-title")||("function"==typeof i.title?i.title.call(e[0]):i.title)},i.prototype.getUID=function(t){do t+=~~(1e6*Math.random());while(document.getElementById(t));return t},i.prototype.tip=function(){return this.$tip=this.$tip||t(this.options.template)},i.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},i.prototype.validate=function(){this.$element[0].parentNode||(this.hide(),this.$element=null,this.options=null)},i.prototype.enable=function(){this.enabled=!0},i.prototype.disable=function(){this.enabled=!1},i.prototype.toggleEnabled=function(){this.enabled=!this.enabled},i.prototype.toggle=function(e){var i=this;e&&(i=t(e.currentTarget).data("bs."+this.type),i||(i=new this.constructor(e.currentTarget,this.getDelegateOptions()),t(e.currentTarget).data("bs."+this.type,i))),i.tip().hasClass("in")?i.leave(i):i.enter(i)},i.prototype.destroy=function(){clearTimeout(this.timeout),this.hide().$element.off("."+this.type).removeData("bs."+this.type)};var o=t.fn.tooltip;t.fn.tooltip=e,t.fn.tooltip.Constructor=i,t.fn.tooltip.noConflict=function(){return t.fn.tooltip=o,this}}(jQuery),+function(t){"use strict";function e(){var t=document.createElement("bootstrap"),e={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var i in e)if(void 0!==t.style[i])return{end:e[i]};return!1}t.fn.emulateTransitionEnd=function(e){var i=!1,o=this;t(this).one("bsTransitionEnd",function(){i=!0});var n=function(){i||t(o).trigger(t.support.transition.end)};return setTimeout(n,e),this},t(function(){t.support.transition=e(),t.support.transition&&(t.event.special.bsTransitionEnd={bindType:t.support.transition.end,delegateType:t.support.transition.end,handle:function(e){return t(e.target).is(this)?e.handleObj.handler.apply(this,arguments):void 0}})})}(jQuery);

/**
 *  FitVids 1.1 - https://github.com/davatron5000/FitVids.js
 */
(function($){$.fn.fitVids=function(options){var settings={customSelector:null,ignore:null};if(!document.getElementById("fit-vids-style")){var head=document.head||document.getElementsByTagName("head")[0];var css=".fluid-width-video-wrapper{width:100%;position:relative;padding:0;}.fluid-width-video-wrapper iframe,.fluid-width-video-wrapper object,.fluid-width-video-wrapper embed {position:absolute;top:0;left:0;width:100%;height:100%;}";var div=document.createElement("div");div.innerHTML='<p>x</p><style id="fit-vids-style">'+css+"</style>";head.appendChild(div.childNodes[1])}if(options)$.extend(settings,options);return this.each(function(){var selectors=["iframe[src*='player.vimeo.com']","iframe[src*='youtube.com']","iframe[src*='youtube-nocookie.com']","iframe[src*='kickstarter.com'][src*='video.html']","object","embed"];if(settings.customSelector)selectors.push(settings.customSelector);var ignoreList=".fitvidsignore";if(settings.ignore)ignoreList=ignoreList+", "+settings.ignore;var $allVideos=$(this).find(selectors.join(","));$allVideos=$allVideos.not("object object");$allVideos=$allVideos.not(ignoreList);$allVideos.each(function(){var $this=$(this);if($this.parents(ignoreList).length>0)return;if(this.tagName.toLowerCase()==="embed"&&$this.parent("object").length||$this.parent(".fluid-width-video-wrapper").length)return;if(!$this.css("height")&&!$this.css("width")&&(isNaN($this.attr("height"))||isNaN($this.attr("width")))){$this.attr("height",9);$this.attr("width",16)}var height=this.tagName.toLowerCase()==="object"||$this.attr("height")&&!isNaN(parseInt($this.attr("height"),10))?parseInt($this.attr("height"),10):$this.height(),width=!isNaN(parseInt($this.attr("width"),10))?parseInt($this.attr("width"),10):$this.width(),aspectRatio=height/width;if(!$this.attr("id")){var videoID="fitvid"+Math.floor(Math.random()*999999);$this.attr("id",videoID)}$this.wrap('<div class="fluid-width-video-wrapper"></div>').parent(".fluid-width-video-wrapper").css("padding-top",aspectRatio*100+"%");$this.removeAttr("height").removeAttr("width")})})}})(window.jQuery||window.Zepto);


/*! http://mths.be/placeholder v2.0.7 by @mathias */
(function(q,f,d){function r(b){var a={},c=/^jQuery\d+$/;d.each(b.attributes,function(b,d){d.specified&&!c.test(d.name)&&(a[d.name]=d.value)});return a}function g(b,a){var c=d(this);if(this.value==c.attr("placeholder")&&c.hasClass("placeholder"))if(c.data("placeholder-password")){c=c.hide().next().show().attr("id",c.removeAttr("id").data("placeholder-id"));if(!0===b)return c[0].value=a;c.focus()}else this.value="",c.removeClass("placeholder"),this==m()&&this.select()}function k(){var b,a=d(this),c=this.id;if(""==this.value){if("password"==this.type){if(!a.data("placeholder-textinput")){try{b=a.clone().attr({type:"text"})}catch(e){b=d("<input>").attr(d.extend(r(this),{type:"text"}))}b.removeAttr("name").data({"placeholder-password":a,"placeholder-id":c}).bind("focus.placeholder",g);a.data({"placeholder-textinput":b,"placeholder-id":c}).before(b)}a=a.removeAttr("id").hide().prev().attr("id",c).show()}a.addClass("placeholder");a[0].value=a.attr("placeholder")}else a.removeClass("placeholder")}function m(){try{return f.activeElement}catch(b){}}var h="placeholder"in f.createElement("input"),l="placeholder"in f.createElement("textarea"),e=d.fn,n=d.valHooks,p=d.propHooks;h&&l?(e=e.placeholder=function(){return this},e.input=e.textarea=!0):(e=e.placeholder=function(){this.filter((h?"textarea":":input")+"[placeholder]").not(".placeholder").bind({"focus.placeholder":g,"blur.placeholder":k}).data("placeholder-enabled",!0).trigger("blur.placeholder");return this},e.input=h,e.textarea=l,e={get:function(b){var a=d(b),c=a.data("placeholder-password");return c?c[0].value:a.data("placeholder-enabled")&&a.hasClass("placeholder")?"":b.value},set:function(b,a){var c=d(b),e=c.data("placeholder-password");if(e)return e[0].value=a;if(!c.data("placeholder-enabled"))return b.value=a;""==a?(b.value=a,b!=m()&&k.call(b)):c.hasClass("placeholder")?g.call(b,!0,a)||(b.value=a):b.value=a;return c}},h||(n.input=e,p.value=e),l||(n.textarea=e,p.value=e),d(function(){d(f).delegate("form","submit.placeholder",function(){var b=d(".placeholder",this).each(g);setTimeout(function(){b.each(k)},10)})}),d(q).bind("beforeunload.placeholder",function(){d(".placeholder").each(function(){this.value=""})}))})(this,document,jQuery);
