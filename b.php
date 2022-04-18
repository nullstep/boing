<?php

//  ▀█████████▄    ▄██████▄    ▄█   ███▄▄▄▄▄       ▄██████▄   
//    ███    ███  ███    ███  ███   ███▀▀▀▀██▄    ███    ███  
//    ███    ███  ███    ███  ███▌  ███    ███    ███    █▀   
//   ▄███▄▄▄██▀   ███    ███  ███▌  ███    ███   ▄███         
//  ▀▀███▀▀▀██▄   ███    ███  ███▌  ███    ███  ▀▀███ ████▄   
//    ███    ██▄  ███    ███  ███   ███    ███    ███    ███  
//    ███    ███  ███    ███  ███   ███    ███    ███    ███  
//  ▄█████████▀    ▀██████▀   █▀     ▀█    █▀     ████████▀    

// set these

define('_HOST', '');
define('_PORT', 9200);
define('_USER', '');
define('_PASS', '');

define('_LOGGING', false);

class B {
	private static $curl;
	private static $fp;

	public static function do($method, $path, $query = '') {
		$headers = [
			'Content-Type: application/json',
			'Authorization: Basic ' . base64_encode(_USER . ':' . _PASS)
		];

		$url = _HOST . '/' . $path;
		self::$curl = curl_init();

		curl_setopt(self::$curl, CURLOPT_URL, $url);
		curl_setopt(self::$curl, CURLOPT_PORT, _PORT);
		curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt(self::$curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt(self::$curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $query);
		curl_setopt(self::$curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt(self::$curl, CURLOPT_VERBOSE, 1);

		if (_LOGGING) {
			self::$fp = fopen(dirname(__FILE__) . '/log.txt', 'w');
			curl_setopt(self::$curl, CURLOPT_STDERR, self::$fp);			
		}

		$result = curl_exec(self::$curl);
		curl_close(self::$curl);

		if (_LOGGING) {
			fclose(self::$fp);
		}

		return $result;
	}

	public static function out($response) {
		header('Content-Type: application/json');
		die(json_encode($response, true));
	}

	public static function auth() {
		return self::do(
			'get',
			'_security/_authenticate'
		);
	}

	public static function index_create($index) {
		return self::do(
			'put',
			$index
		);
	}

	public static function index_delete($index) {
		return self::do(
			'delete',
			$index
		);
	}

	public static function find($index, $query) {
		return self::do(
			'get',
			$index . '/_search',
			json_encode($query)
		);
	}

	public static function get($index, $id) {
		return self::do(
			'get',
			$index . '/_doc/' . $id
		);
	}

	public static function put($index, $doc) {
		return self::do(
			'post',
			$index . '/_doc',
			json_encode($doc)
		);
	}

	public static function delete($index, $id) {
		return self::do(
			'delete',
			$index . '/_doc/' . $id
		);
	}
}

//     ▄█    █▄        ▄████████   ▄█           ▄███████▄  
//    ███    ███      ███    ███  ███          ███    ███  
//    ███    ███      ███    █▀   ███          ███    ███  
//   ▄███▄▄▄▄███▄▄   ▄███▄▄▄      ███          ███    ███  
//  ▀▀███▀▀▀▀███▀   ▀▀███▀▀▀      ███        ▀█████████▀   
//    ███    ███      ███    █▄   ███          ███         
//    ███    ███      ███    ███  ███▌    ▄    ███         
//    ███    █▀       ██████████  █████▄▄██   ▄████▀        

/*

---
create index
---

$response = B::index_create(
	'index_name'
);

---
delete index
---

$response = B::index_delete(
	'index_name'
);

---
put document
---

$response = B::put(
	'index_name',
	[
		'title' => 'nozzles',
		'author' => 'john smith',
		'text' => 'the book content about nozzles'
	]
);

---
find document
---

$response = B::find(
	'index_name',
	[
		'query' => [
			'match' => [
				'title' => 'nozzles'
			]
		]
	]
);

---
get document
---

$response = B::get(
	'index_name',
	'document_id'
);

---
delete document
---

$response = B::delete(
	'index_name',
	'document_id'
);

*/

// EOF