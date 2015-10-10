<script type="text/javascript">
require(['converse'], function (converse) {
    (function () {
        /* XXX: This function initializes jquery.easing for the https://conversejs.org
        * website. This code is only useful in the context of the converse.js
        * website and converse.js itself is NOT dependent on it.
        */
        var $ = converse.env.jQuery;
        $.extend( $.easing, {
            easeInOutExpo: function (x, t, b, c, d) {
                if (t==0) return b;
                if (t==d) return b+c;
                if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
                return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
            },
        });

        $(window).scroll(function() {
            if ($(".navbar").offset().top > 50) {
                $(".navbar-fixed-top").addClass("top-nav-collapse");
            } else {
                $(".navbar-fixed-top").removeClass("top-nav-collapse");
            }
        });
        //jQuery for page scrolling feature - requires jQuery Easing plugin
        $('.page-scroll a').bind('click', function(event) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top
            }, 700, 'easeInOutExpo');
            event.preventDefault();
        });
    })();
    converse.initialize({
        bosh_service_url: 'https://octeau.fr:7443/http-bind/', // Please use this connection manager only for testing purposes
        keepalive: true,
        message_carbons: true,
        play_sounds: true,
        roster_groups: false,
        show_controlbox_by_default: false,
        xhr_user_search: false,
        allow_registration: false,
        jid: '<?php echo $user->getUsername(); ?>@octeau.fr',
        password: '<?php echo md5($user->getPasswordHash()); ?>',
        authentication: 'login',
        auto_login: true,
        auto_reconnect: true,
        hide_muc_server: true,
        message_archiving: true,
        cache_otr_key: true,
        auto_subscribe: true,
        auto_away: 30,
        allow_contact_requests: false,
        allow_contact_removal: false,
        hide_offline_users: true
    });
});
</script>
