<?php
/**
 * Created by PhpStorm.
 * User: timondecathlon
 * Date: 31.08.20
 * Time: 1:02
 */

require_once('../errors.php');

exec("go run /var/www/www-root/data/www/cards.bitkit.pro/daemons/bitcards_notificator.go &");