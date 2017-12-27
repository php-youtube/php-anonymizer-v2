<?php

$_BODY = str_replace([
	// URL
	'href="//',
	'src="//',
	'https://',
	'yandex.ru',
	'yandex.ua',
	'yandex.net',
	'yandex.com',
	'yastatic.net',
	'avatars.mds',
	// Мусор
	'http://ssl.mc.yandex.ru'.$_URL['domain'].'/metrika/watch_visor.js',
	'http://ssl.mc.yandex.ru'.$_URL['domain'].'/watch/152220',
	'<div class="advert">',
	'<div class="promo-header__content promo-header__content_type_common">',
],
[
	// URL
	'href="http://ssl.',
	'src="http://ssl.',
	'http://ssl.',
	'yandex.ru'.$_URL['domain'],
	'yandex.ua'.$_URL['domain'],
	'yandex.net'.$_URL['domain'],
	'yandex.com'.$_URL['domain'],
	'yastatic.net'.$_URL['domain'],
	'tcp.avatars.mds',
	/* Мусор */
	'#',
	'#',
	'<div class="advert" style="display:none">',
	'<div class="promo-header__content promo-header__content_type_common" style="display:none">',
],
$_BODY);