<script type="text/javascript">
require(['converse'], function (converse) {
    converse.initialize({
        bosh_service_url: '<?php echo $openfire_bosh_service_url; ?>',
        keepalive: true,
        message_carbons: true,
        play_sounds: true,
        roster_groups: false,
        show_controlbox_by_default: false,
        xhr_user_search: false,
        allow_registration: false,
        jid: '<?php echo $_SESSION['user']['infos']['username'].$VIDdomain; ?>',
        password: '<?php echo md5($_SESSION['user']['infos']['password']); ?>',
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
