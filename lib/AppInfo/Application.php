<?php

namespace OCA\Smartdev\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'smartdev';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
