<?php
/**
* @class Request
*/
class Request {
	public $restful, $method, $controller, $action, $id, $params;

	public function __construct($params) {
		$this->restful = (isset($params["restful"])) ? $params["restful"] : false;
		$this->method = $_SERVER["REQUEST_METHOD"];
		$this->parseRequest();
	}

	public function isRestful() {
		return $this->restful;
	}

	public function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	protected function parseRequest() {
		if($this->method == 'GET'){
			$this->params = $_GET;
		}else{
			$data = file_get_contents('php://input');
			if($this->isJson($data)){
				$this->params = json_decode($data);
			}else{
				parse_str($data, $this->params);
			}
		}

		// Quickndirty PATH_INFO parser
		if(!empty($_SERVER['REQUEST_URI']) && !empty($_SERVER['SCRIPT_NAME'])){
			$path_info = parse_url(substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME'])))['path'];
			$cai = '/^\/([a-z]+\w)\/([a-z]+\w)\/([0-9]+)$/';  // /controller/action/id
			$ca =  '/^\/([a-z]+\w)\/([a-z]+)$/';              // /controller/action
			$ci =  '/^\/([a-z]+\w)\/([0-9]+)$/';              // /controller/id
			$c =   '/^\/([a-z]+\w)$/';                        // /controller
			$i =   '/^\/([0-9]+)$/';                          // /id

			if(preg_match($cai, $path_info, $matches)) {
				$this->controller = $matches[1];
				$this->action = $matches[2];
				$this->id = $matches[3];
			}elseif(preg_match($ca, $path_info, $matches)){
				$this->controller = $matches[1];
				$this->action = $matches[2];
			}elseif(preg_match($ci, $path_info, $matches)){
				$this->controller = $matches[1];
				$this->id = $matches[2];
			}elseif(preg_match($c, $path_info, $matches)){
				$this->controller = $matches[1];
			}elseif(preg_match($i, $path_info, $matches)){
				$this->id = $matches[1];
			}

		}
	}
}

