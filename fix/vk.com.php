<?php

$_BODY = str_replace([	
	// Фикс URL
	'href="//',
	'src="//',
	'https://',
	'https:\/\/',
	'vk.com',
	'userapi.com',
	// Скрыть блок рекламы
	'id="ads_left"',
	// Фикс JS регулярок на определение домена
	'match(/[a-zA-Z]+\.[a-zA-Z]+\.?$/)',
	'match(/[a-zA-Z]*\.[a-zA-Z]*$/)',
	// Улучшение мобильной версии
	's_cf.css',
	's_yzg.css',
	'class="messagesActions"',
	'class="wi_actions_btn"',
	'class="ai_menu_wrap"',
	'class="reply_owner_image _reply_owner_selected_image"',
	// Фикс бесконечной перезагрузки в мобильной версии
	',location.replace(location.toString())',
	'domain=.vk.com',
],
[
	// Фикс URL
	'href="http://ssl.',
	'src="http://ssl.',
	'http://ssl.',
	'http:\/\/ssl.',
	'vk.com'.$_URL['domain'],
	'userapi.com'.$_URL['domain'],
	// Скрыть блок рекламы
	'id="ads_left" style="display:none"',
	// Фикс JS регулярок на определение домена
	'match(/[a-zA-Z]+\.[a-zA-Z]+\.[a-zA-Z]+[a-zA-Z-]+\.[a-zA-Z]+\.?$/)',
	'match(/[a-zA-Z]+\.[a-zA-Z]+\.[a-zA-Z]+[a-zA-Z-]+\.[a-zA-Z]*$/)',
	// Улучшение мобильной версии
	's_cfmxw.css',
	's_yzgt.css',
	'class="messagesActions" style="display:none"',
	'class="wi_actions_btn" style="display:none"',
	'class="ai_menu_wrap" style="display:none"',
	'class="reply_owner_image _reply_owner_selected_image" style="display:none"',
	// Фикс бесконечной перезагрузки в мобильной версии
	'',
	'domain=.x.x',
],
$_BODY);