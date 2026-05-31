(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Navbar always visible (floating from the start)
    
    
    // Dropdown on mouse hover
    const $dropdown = $(".dropdown");
    const $dropdownToggle = $(".dropdown-toggle");
    const $dropdownMenu = $(".dropdown-menu");
    const showClass = "show";
    
    $(window).on("load resize", function() {
        if (this.matchMedia("(min-width: 992px)").matches) {
            $dropdown.hover(
            function() {
                const $this = $(this);
                $this.addClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "true");
                $this.find($dropdownMenu).addClass(showClass);
            },
            function() {
                const $this = $(this);
                $this.removeClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "false");
                $this.find($dropdownMenu).removeClass(showClass);
            }
            );
        } else {
            $dropdown.off("mouseenter mouseleave");
        }
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 10,
        time: 2000
    });


    // Date and time picker
    $('.date').datetimepicker({
        format: 'L'
    });
    $('.time').datetimepicker({
        format: 'LT'
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: true,
        margin: 25,
        dots: true,
        loop: true,
        nav : false,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });


    // Service list vertical scroll (show 4, navigate the rest)
    var $serviceViewport = $('.service-nav-viewport');
    if ($serviceViewport.length) {
        var $items = $serviceViewport.find('.nav-link');
        var $upBtn = $('.service-scroll-up');
        var $downBtn = $('.service-scroll-down');
        var visible = 4;

        // Height of one item including its bottom margin
        function itemStep() {
            return $items.eq(0).outerHeight(true);
        }

        // Fit the viewport to exactly `visible` items (ignoring the last margin)
        function fitViewport() {
            if (!$items.length) return;
            var h = 0;
            var count = Math.min(visible, $items.length);
            for (var i = 0; i < count; i++) {
                h += $items.eq(i).outerHeight(true);
            }
            h -= parseFloat($items.eq(count - 1).css('marginBottom')) || 0;
            $serviceViewport.css('height', h);
            updateButtons();
        }

        // Enable/disable arrows at the ends of the list
        function updateButtons() {
            var el = $serviceViewport[0];
            var maxScroll = el.scrollHeight - el.clientHeight - 1;
            $upBtn.prop('disabled', el.scrollTop <= 0);
            $downBtn.prop('disabled', el.scrollTop >= maxScroll);
        }

        $upBtn.on('click', function () {
            $serviceViewport[0].scrollBy({ top: -itemStep(), behavior: 'smooth' });
        });
        $downBtn.on('click', function () {
            $serviceViewport[0].scrollBy({ top: itemStep(), behavior: 'smooth' });
        });
        $serviceViewport.on('scroll', updateButtons);
        $(window).on('resize', fitViewport);
        fitViewport();
    }

})(jQuery);

