/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(2);


/***/ }),
/* 1 */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

(function ($) {
	$('div.podbor').on('click', 'span', function () {
		var inp = $('input', this.parentNode),
		    val = +inp.val() || 0;
		inp.val(val += this.className == 'minus' ? val > 0 ? -1 : 0 : 1);
	});
})(jQuery);

$(function () {
	var _$$slick;

	$('select').niceSelect();

	$(".menu-serivce  > ul > li span").click(function () {
		$(this).parents(".menu-serivce  > ul > li").find("ul").slideToggle();
	});

	$(".popup-form").animated("bounceInDown", "fadeInDown");

	$('.tel-input').inputmask('+7(999)999-99-99');
	$('.time-input').inputmask('99 : 99');

	$(".accordeon dd").hide().prev().click(function () {
		$(this).parents(".accordeon").find("dd").not(this).slideUp().prev().removeClass("active");
		$(this).next().not(":visible").slideDown().prev().addClass("active");
	});

	$(".tab_item").not(":first").hide();
	$(".wrapper .tab").click(function () {
		$(".wrapper .tab").removeClass("active").eq($(this).index()).addClass("active");
		$(".tab_item").hide().eq($(this).index()).fadeIn();
	}).eq(0).addClass("active");

	$('.carousel').slick((_$$slick = {
		dots: true,
		infinite: false,
		speed: 1000
	}, _defineProperty(_$$slick, 'infinite', true), _defineProperty(_$$slick, 'slidesToShow', 1), _defineProperty(_$$slick, 'slidesToScroll', 1), _defineProperty(_$$slick, 'prevArrow', '<button type="button" class="btn-slider prev"></button>'), _defineProperty(_$$slick, 'nextArrow', '<button type="button" class="btn-slider next"></button>'), _defineProperty(_$$slick, 'responsive', [{
		breakpoint: 1200,
		settings: {
			slidesToShow: 1,
			slidesToScroll: 1,
			infinite: true,
			dots: true
		}
	}, {
		breakpoint: 992,
		settings: {
			slidesToShow: 1,
			slidesToScroll: 1
		}
	}, {
		breakpoint: 768,
		settings: {
			slidesToShow: 1,
			slidesToScroll: 1
		}
	}, {
		breakpoint: 576,
		settings: {
			slidesToShow: 1,
			slidesToScroll: 1
		}
	}, {
		breakpoint: 0,
		settings: {
			slidesToShow: 1,
			slidesToScroll: 1
		}

		// You can unslick at a given breakpoint now by adding:
		// settings: "unslick"
		// instead of a settings object
	}]), _$$slick));
	//$('.slider-nav').slick({
	//  slidesToShow: 4,
	//  slidesToScroll: 1,
	//  asNavFor: '.slider-for',
	//  focusOnSelect: true
	//});


	$('.popup-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		tLoading: 'Загрузка изображения #%curr%...',
		mainClass: 'mfp-fade mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: '<a href="%url%">Изображение #%curr%</a> не загружено.',
			titleSrc: function titleSrc(item) {
				return '';
			}
		}
	});

	$(".main_mnu_button").click(function () {
		$("nav > ul").slideToggle();
	});

	$('.popup-with-form').magnificPopup({
		type: 'inline',
		preloader: false,
		focus: '#name',
		mainClass: 'mfp-fade',
		// When elemened is focused, some mobile browsers in some cases zoom in
		// It looks not nice, so we disable it:
		callbacks: {
			beforeOpen: function beforeOpen() {
				if ($(window).width() < 700) {
					this.st.focus = false;
				} else {
					this.st.focus = '#name';
				}
			}
		}
	});

	$('.popup').magnificPopup({
		type: 'image',
		closeOnContentClick: true,
		closeBtnInside: false,
		fixedContentPos: true,
		mainClass: 'mfp-fade mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
		image: {
			verticalFit: true
		},
		zoom: {
			enabled: true,
			duration: 300 // don't foget to change the duration also in CSS
		}
	});

	//Кнопка "Наверх"
	//Документация:
	//http://api.jquery.com/scrolltop/
	//http://api.jquery.com/animate/
	$("#top").click(function () {
		$("body, html").animate({
			scrollTop: 0
		}, 800);
		return false;
	});

	//E-mail Ajax Send
	//Documentation & Example: https://github.com/agragregra/uniMail
	/**$("form").submit(function() { //Change
 	var th = $(this);
 	$.ajax({
 		type: "POST",
 		url: "assets/templates/mail.php", //Change
 		data: th.serialize()
 	}).done(function() {
 		$(".done-w").fadeIn();
 		setTimeout(function() {
 			// Done Functions
 			$(".done-w").fadeOut();
 			$.magnificPopup.close();
 			th.trigger("reset");
 		}, 3000);
 	});
 	return false;
 });**/

	//Chrome Smooth Scroll
	try {
		$.browserSelector();
		if ($("html").hasClass("chrome")) {
			$.smoothScroll();
		}
	} catch (err) {};

	$("img, a").on("dragstart", function (event) {
		event.preventDefault();
	});
});

/***/ }),
/* 2 */
/***/ (function(module, exports) {

throw new Error("Module build failed: ModuleBuildError: Module build failed: Error: spawn /WTS/gelen/node_modules/gifsicle/vendor/gifsicle ENOENT\n    at Process.ChildProcess._handle.onexit (internal/child_process.js:232:19)\n    at onErrorNT (internal/child_process.js:407:16)\n    at process._tickCallback (internal/process/next_tick.js:63:19)\n    at runLoaders (/WTS/gelen/node_modules/webpack/lib/NormalModule.js:195:19)\n    at /WTS/gelen/node_modules/loader-runner/lib/LoaderRunner.js:364:11\n    at /WTS/gelen/node_modules/loader-runner/lib/LoaderRunner.js:230:18\n    at context.callback (/WTS/gelen/node_modules/loader-runner/lib/LoaderRunner.js:111:13)\n    at /WTS/gelen/node_modules/img-loader/index.js:45:31");

/***/ })
/******/ ]);