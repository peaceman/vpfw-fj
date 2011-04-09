<?php
$ipAddresses = array(
    $_SERVER['REMOTE_ADDR'],
    'ff80::1',
    '2001:45f3:dfec::5',
);

foreach ($ipAddresses as $ip) {
    echo $ip . ' equals ' . inet_pton($ip) . '<br />';
}