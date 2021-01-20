<?php
namespace OCA\Smartdev\Controller;

use OCA\Smartdev\AppInfo\Application;
use OCP\AppFramework\Http\DataResponse;
use OCA\Smartdev\Service\SmartdevService;
use \OCP\AppFramework\ApiController;
use \OCP\IRequest;

class SmartdevApiController extends ApiController {

	private $service;
	private $userId;

	public function __construct( IRequest $request, SmartdevService $service, $userId) {
		parent::__construct(
				Application::APP_ID,
				$request,
            'PUT, POST, GET, DELETE, PATCH',
            'Authorization, Content-Type, Accept',
            1728000);
		$this->service = $service;
		$this->userId = $userId;
 		$this->service->login($userId);
	}

	/**
	* @CORS
	* @NoCSRFRequired
	*/
	public function list(): DataResponse {
		return new DataResponse($this->service->getDevices($this->userId)->payload->devices);
	}

	/**
	* @CORS
	* @NoCSRFRequired
	*/
	public function setstate($id, $state) {
 		$this->service->setState($this->userId, $id, (int) $state);
	}
}
