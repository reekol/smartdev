<?php

namespace OCA\Smartdev\Controller;

use OCA\Smartdev\AppInfo\Application;
use OCA\Smartdev\Service\SmartdevService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class SmartdevApiController extends ApiController {

	private $service;
	private $userId;

	public function __construct(IRequest $request, SmartdevService $service, $userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	public function index(): DataResponse {
		return new DataResponse($this->service->getDevices($this->userId)->payload->devices);
	}

	public function userPut($email, $password, $country, $zone, $type): array {
		return $this->service->setUserCredentials($this->userId, $email, $password, $country, $zone, $type);
	}
	
	public function userGet($email, $password, $country, $zone, $type): array {
		return $this->service->getUserCredentials($this->userId);
	}

	public function update(string $id, string $name, $data) {
 		$this->service->setState($this->userId, $id, (int) $data['state']);
	}
}
