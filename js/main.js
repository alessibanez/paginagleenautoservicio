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
    // offset negativo: dispara las animaciones antes (cuando el elemento aún está
    // ~200px bajo el borde inferior), para que el contenido aparezca sin esperar.
    new WOW({
        offset: -200,
        mobile: true,
        live: true
    }).init();


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


    // Service list vertical carousel (show 4, loop infinitely in both directions)
    var $serviceViewport = $('.service-nav-viewport');
    if ($serviceViewport.length) {
        var $list = $serviceViewport.find('.nav-pills');
        var $items = $serviceViewport.find('.nav-link');
        var $upBtn = $('.service-scroll-up');
        var $downBtn = $('.service-scroll-down');
        var visible = 4;
        var animating = false;

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
        }

        // Runs `done` once the list's transform transition finishes
        function onListTransitionEnd(done) {
            $list.one('transitionend', function (e) {
                if (e.target !== $list[0]) return;          // ignore child transitions
                done();
            });
        }

        // Advance: slide up one item, then move the first item to the end
        function goDown() {
            if (animating || $items.length <= visible) return;
            animating = true;
            var step = itemStep();
            $list.css({ transition: 'transform .35s ease', transform: 'translateY(' + (-step) + 'px)' });
            onListTransitionEnd(function () {
                $list.css('transition', 'none').css('transform', 'translateY(0)');
                $items.eq(0).appendTo($list);              // first goes to the bottom
                $items = $serviceViewport.find('.nav-link');
                $list[0].offsetHeight;                     // force reflow before re-enabling transitions
                animating = false;
            });
        }

        // Go back: move the last item to the top, then slide it into view
        function goUp() {
            if (animating || $items.length <= visible) return;
            animating = true;
            var step = itemStep();
            $items.eq($items.length - 1).prependTo($list); // last goes to the top
            $items = $serviceViewport.find('.nav-link');
            $list.css({ transition: 'none', transform: 'translateY(' + (-step) + 'px)' });
            $list[0].offsetHeight;                         // force reflow so the next transition runs
            $list.css({ transition: 'transform .35s ease', transform: 'translateY(0)' });
            onListTransitionEnd(function () {
                animating = false;
            });
        }

        $upBtn.on('click', goUp);
        $downBtn.on('click', goDown);
        $(window).on('resize', fitViewport);
        fitViewport();
    }

})(jQuery);

