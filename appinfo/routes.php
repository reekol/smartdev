<?php

return [
	'resources' => [
		'smartdev' => ['url' => '/smartdev'],
		'smartdev_api' => ['url' => '/api/0.1/smartdev']
	],
	'routes' => [
		[
			'name' => 'smartdev#userPut',
			'url'  => '/user',
			'verb' => 'PUT'
		],[
			'name' => 'smartdev#userGet',
			'url'  => '/user',
			'verb' => 'GET'
		],[
			'name' => 'page#index',
			'url' => '/',
			'verb' => 'GET'
		],[
			'name' => 'smartdev_api#preflighted_cors',
			'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS',
			'requirements' => ['path' => '.+']
		]
	]
];
