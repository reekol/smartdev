<?php

namespace OCA\Smartdev\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Smartdev\Db\Smartdev;
use \OCP\IConfig;

class SmartdevService {

	/** @var SmartdevMapper */
	private $profile = [];
   private $uri = '';
   private $tokens = null;

	public function __construct(IConfig $config, $appName) {
		$this->config = $config;
		$this->appName = $appName;
	}
	
	private function cacheCheck($name, $userId){
 			$cache = json_decode($this->getUserValue($name,$userId));
 			if(isset($cache->data) && $cache->data && isset($cache->expires_in) && time() < $cache->expires_in){
 				return $cache->data;
 			}
	}

	private function cacheRebuild($name, $userId, $data, $time){
		$this->setUserValue($name, $userId, json_encode([ 'expires_in' => time() + $time, 'data' => $data ]));
	}

	
	private function post($uri, $body, $json = true, $userId, array $cacheFor = ['name' => 'lastPost', 'time' => 0 ]){
		$headers = [$json ? 'Content-Type: application/json' : 'Content-Type: application/x-www-form-urlencoded'];
		$body = ($json ? json_encode($body) : http_build_query($body));

		$cache = $this->cacheCheck($cacheFor['name'],$userId);
		if($cache) return $cache;

		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		$response = json_decode($result);
		$this->cacheRebuild($cacheFor['name'], $userId, $response, $cacheFor['time']);

		return $response;
	}

	private function getUserValue($key, $userId) {
		return $this->config->getUserValue($userId, $this->appName, $key);
	}

 	private function setUserValue($key, $userId, $value) {
 		$this->config->setUserValue($userId, $this->appName, $key, $value);
 	}
	
	public function setUserCredentials($userId, $email, $password, $country, $zone, $type){
			$this->setUserValue('email',		$userId, $email);
			$this->setUserValue('password',	$userId, $password);
			$this->setUserValue('country',	$userId, $country);
			$this->setUserValue('zone',		$userId, $zone);
			$this->setUserValue('type',		$userId, $type);
			return [
				'email' 		=> $email,
				'country' 	=> $country,
				'zone' 		=> $zone,
				'type' 		=> $type,
			];
	}

	public function getUserCredentials($userId){
		return [
			'email' 		=> $this->getUserValue('email',		$userId),
			'country' 	=> $this->getUserValue('country',	$userId),
			'zone'		=> $this->getUserValue('zone',		$userId),
			'type'		=> $this->getUserValue('type',		$userId),
		];
	}

	public function login($userId){
		$this->profile = [
 			"userName" 		=> $this->getUserValue('email',		$userId),
 			"password" 		=> $this->getUserValue('password',	$userId),
 			"bizType" 		=> $this->getUserValue('type',		$userId),
 			"countryCode" 	=> $this->getUserValue('country',	$userId),
 			"region" 		=> $this->getUserValue('zone',		$userId),
 		];
		$this->profile['from'] = 'tuya';
		$this->uri 	= 'https://px1.tuya'.$this->profile["region"].'.com/homeassistant';
		$response 	= $this->post($this->uri."/auth.do", $this->profile, false, $userId, ['name' => 'loginPost', 'time' => 600 ]);
		if(isset($response->access_token)) $this->setUserValue('access_token',$userId,$response->access_token);
	}

	public function getDevices($userId) {
		return $this->post($this->uri.'/skill', [
					"header"  => ["name" => 'Discovery',"namespace" => 'discovery',"payloadVersion" => 1],
					"payload" => ["accessToken" => $this->getUserValue('access_token',$userId)]
				], true, $userId, ['name' => 'devicestPost', 'time' => 600 ]);
	}

	public function setState($userId,$id,$state) {
		$devicesReq = $this->cacheCheck('devicestPost', $userId);
		if(!$devicesReq) $devicesReq = $this->getDevices($userId);

		$json = $this->post($this->uri.'/skill', [
			"header"  => ["name" => "turnOnOff","namespace" => 'control',"payloadVersion" => 1],
			"payload" => ["devId" => $id, "value" => $state, "accessToken" 	=> $this->getUserValue('access_token',$userId) ]
		], true, $userId);
		
		foreach($devicesReq->payload->devices as $k => $device){
			if($device->id === $id) $devicesReq->payload->devices[$k]->data->state = (bool) $state;
		}
		$this->cacheRebuild('devicestPost', $userId, $devicesReq, 600);
	}
}
