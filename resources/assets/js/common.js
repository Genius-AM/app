(function($){
    $('div.podbor').on('click', 'span', function () {
        var inp = $('input' , this.parentNode),
            val = +inp.val()||0;
        inp.val(val += this.className == 'minus' ? val > 0 ? -1: 0: 1);
    });
}(jQuery));

$(function() {
	$('select').niceSelect();

	$(".menu-serivce  > ul > li span").click(function() {
			$(this).parents(".menu-serivce  > ul > li").find("ul").slideToggle();
		});

	$(".popup-form").animated("bounceInDown", "fadeInDown");

	$('.tel-input').inputmask('+7(999)999-99-99');
	$('.time-input').inputmask('99 : 99');
		
	$(".accordeon dd").hide().prev().click(function() {
		$(this).parents(".accordeon").find("dd").not(this).slideUp().prev().removeClass("active");
		$(this).next().not(":visible").slideDown().prev().addClass("active");
	});

	$(".tab_item").not(":first").hide();
	$(".wrapper .tab").click(function() {
		$(".wrapper .tab").removeClass("active").eq($(this).index()).addClass("active");
		$(".tab_item").hide().eq($(this).index()).fadeIn()
	}).eq(0).addClass("active");


	$('.carousel').slick({
	  dots: true,
	  infinite: false,
	  speed: 1000,
	  infinite: true,
	  slidesToShow: 1,
	  slidesToScroll: 1,
	  prevArrow: '<button type="button" class="btn-slider prev"></button>',
	  nextArrow: '<button type="button" class="btn-slider next"></button>',
	  //asNavFor: '.slider-nav'
	  responsive: [
	    {
	      breakpoint: 1200,
	      settings: {
	        slidesToShow: 1,
	        slidesToScroll: 1,
	        infinite: true,
	        dots: true
	      }
	    },
	    {
	      breakpoint: 992,
	      settings: {
	        slidesToShow: 1,
	        slidesToScroll: 1
	      }
	    },
	    {
	      breakpoint: 768,
	      settings: {
	        slidesToShow: 1,
	        slidesToScroll: 1
	      }
	    },
	    {
	      breakpoint: 576,
	      settings: {
	        slidesToShow: 1,
	        slidesToScroll: 1
	      }
	    },
	    {
	      breakpoint: 0,
	      settings: {
	        slidesToShow: 1,
	        slidesToScroll: 1
	      }
	    }

	    // You can unslick at a given breakpoint now by adding:
	    // settings: "unslick"
	    // instead of a settings object
	  ]
	});
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
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: '<a href="%url%">Изображение #%curr%</a> не загружено.',
			titleSrc: function(item) {
				return '';
			}
		}
	});

	$(".main_mnu_button").click(function() {
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
			beforeOpen: function() {
				if($(window).width() < 700) {
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
		if($("html").hasClass("chrome")) {
			$.smoothScroll();
		}
	} catch(err) {

	};

	$("img, a").on("dragstart", function(event) { event.preventDefault(); });
	
});

