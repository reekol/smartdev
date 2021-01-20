<?php

return [
	'resources' => [
		'smartdev' => ['url' => '/smartdev'],
		'smartdev_api' => ['url' => '/api/1.0/smartdev']
	],
	'routes' => [
		[
			'name' => 'page#index',
			'url' => '/',
			'verb' => 'GET'
		],[
			'name' => 'smartdev#userPut',
			'url'  => '/user',
			'verb' => 'PUT'
		],[
			'name' => 'smartdev#userGet',
			'url'  => '/user',
			'verb' => 'GET'
		],[
			'name' => 'smartdev_api#list',
			'url' => '/api/1.0/',
			'verb' => 'GET',
			'requirements' => ['path' => '.+']
		],[
			'name' => 'smartdev_api#setstate',
			'url' => '/api/1.0/setstate',
			'verb' => 'GET',
			'requirements' => ['path' => '.+']
		]
	]
];
