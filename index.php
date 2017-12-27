<?php

// Функция отладки
function debug_show($data) {
	echo '<pre style="background: #272822; color: #FFF; padding: 10px; font-size: 15px; word-wrap: break-word;">';
	var_dump($data);
	echo '</pre>';
	exit;
}

function parse_cookie($cookie) {
	list($data, $expire, $path) = explode('; ', $cookie);
	list($name, $value) = explode('=', $data);
	return [
		'name' => $name,
		'value' => $value,
	];
}

$rn = "\r\n";

$_URL['domain'] = '.site.com'; // Ваш домен (точку в начале не удалять)
$part = explode('.', str_replace($_URL['domain'], '', $_SERVER['HTTP_HOST']));
$scheme = $part[0];
unset($part[0]);
$reverse = array_reverse($part);

// Порты
$port = [
	'tcp' => 80,
	'ssl' => 443,
];

// Параметры
$_URL = [
	'domain' => $_URL['domain'],
	'scheme' => $scheme,
	'port' => $port[$scheme],
	'host' => implode('.', $part),
	'query' => $_SERVER['REQUEST_URI'],
	'web' => $reverse[1].'.'.$reverse[0],
];

$fp = fsockopen($_URL['scheme'].'://'.$_URL['host'], $_URL['port']);

if (!$fp) {
   exit('Сервер не отвечает');
}

$out = $_SERVER['REQUEST_METHOD'].' '.$_URL['query'].' '.$_SERVER['SERVER_PROTOCOL'].$rn;
$out .= 'Host: '.$_URL['host'].$rn;
//$out .= 'User-Agent: '.$_SERVER['HTTP_USER_AGENT'].$rn;
$out .= 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8'.$rn;
$out .= 'Accept: '.$_SERVER['HTTP_ACCEPT'].$rn;

// Установка куки (если есть)
if ($_COOKIE) {
	$out .= sprintf('Cookie: %s', http_build_query($_COOKIE, null, '; ')).$rn;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Обработка HTML формы с файлами
	if ($_FILES) {
		$boundary = '----'.md5(time());
		foreach ($_POST as $key => $value) {
			$post .= '--{$boundary}'.$rn;
			$post .= 'Content-Disposition: form-data; name='.$key.$rn.$rn;
			$post .= urldecode($value).$rn;
		}
		foreach ($_FILES as $key => $file_info) {
			$post .= '--{$boundary}'.$rn;
			$post .= 'Content-Disposition: form-data; name="'.$key.'"; filename="{'.$file_info['name'].'}'.$rn;
			$post .= 'Content-Type: '.(empty($file_info['type']) ? 'application/octet-stream' : $file_info['type']).$rn.$rn;
			if (is_readable($file_info['tmp_name'])) {
				$handle = fopen($file_info['tmp_name'], 'rb');
				$post .= fread($handle, filesize($file_info['tmp_name']));
				fclose($handle);
			}
			$post .= $rn;
		}
		$post .= '--{$boundary}--'.$rn;
		$out .= 'Content-Type: multipart/form-data; boundary={$boundary}'.$rn;
	}
	else {
		// Обработка обычной HTML формы
		$post = http_build_query($_POST);
		$out .= 'Content-Type: application/x-www-form-urlencoded'.$rn;
	}
	$out .= 'Content-Length: '.strlen($post).$rn;
	$out .= 'Connection: Close'.$rn.$rn;
	$out .= $post;
}
else {
	$out .= 'Connection: Close'.$rn.$rn;
}


fwrite($fp, $out);

while (!feof($fp)) {
	$response .= fgets($fp, 128);
}

fclose($fp);
list($_HEADER, $_BODY) = explode($rn.$rn, $response);

// Преобразование заголовков в массив
$exp = explode("\n", str_replace("\r", '', $_HEADER));
unset($exp[0]);
foreach ($exp as $val) {
	list($name, $data) = explode(': ', $val);
	if ($result[$name]) {
		if (!is_array($result[$name])) {
    		$cur = $result[$name];
    		$result[$name] = [];
    		$result[$name][] = $cur;
    	}
    	$result[$name][] = $data;
    }
    else {
    	$result[$name] = $data;
    }
}
$_HEADER = $result;

unset($_HEADER['Content-Security-Policy'], $_HEADER['Content-Security-Policy-Report-Only']);
// Установка заголовков
foreach ($_HEADER as $key => $val) {
	// Обработка куки
	if (strtolower($key) == 'set-cookie' and $_HEADER[$key]) {
		if (is_array($_HEADER[$key])) {
			foreach ($_HEADER[$key] as $cookie) {
				$cookie = parse_cookie($cookie);
				setcookie($cookie['name'], $cookie['value'], strtotime('+365 days'), '/', $_URL['web'].$_URL['domain']);
			}
		}
		else {
			$cookie = parse_cookie($_HEADER[$key]);
			setcookie($cookie['name'], $cookie['value'], strtotime('+365 days'), '/', $_URL['web'].$_URL['domain']);
		}
	}
	// Обработка редиректа
	elseif (strtolower($key) == 'location') {
		if (substr($val, 0, 2) == '//') {
			$val = 'http:'.$val;
		}
		$url = parse_url($val);
		if (!$url['scheme']) {
			$location = $val;
		}
		else {
			if ($url['query']) {
				$url['query'] = '?'.$url['query'];
			}
			else {
				$url['query'] = '';
			}
			$location = 'http://'.str_replace(['https', 'http'], ['ssl', 'tcp'], $url['scheme']).'.'.$url['host'].$_URL['domain'].$url['path'].$url['query'];
		}
		$location = str_replace('1.vk.com', 'ssl.m.vk.com', $location);
		header($key.': '.$location);
	}
	else {
		header($key.': '.$val);
	}
}

// Правка HTML кода
$fix = 'fix/'.$_URL['web'].'.php';
if (file_exists($fix)) {
	require $fix;
}

echo $_BODY;