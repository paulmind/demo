<?php
/**
 * @class ApplicationController
 */
class ApplicationController {
	public $request, $id, $params;
	protected $c='undefined';

	public function __construct(){
		if(class_exists($this->c)){
			$this->model = new $this->c;
		}else{
			throw new Exception("Class '{$this->c}' not found");
		}
	}

	/**
	 * dispatch
	 * Dispatch request to appropriate controller-action by convention according to the HTTP method.
	 */
	public function dispatch($request) {
			$this->request = $request;
			$this->id = $request->id;
			$this->params = $request->params;

			if($request->isRestful()){
				return $this->dispatchRestful();
			}
			if($request->action){
				return $this->{$request->action}();
			}
	}

	protected function dispatchRestful() {
		switch ($this->request->method) {
			case 'GET':
				return $this->view();
				break;
			case 'POST':
				return $this->create();
				break;
			case 'PUT':
				return $this->update();
				break;
			case 'DELETE':
				return $this->destroy();
				break;
		}
	}

	public function view() {
		$res = new Response();
		$res->success = true;
		$res->message = "Loaded data ".print_r($this->params, true);
		$res->data = $this->model->all($this->params);
		return $res->to_json();
	}

	public function create() {
		$res = new Response();
		$prepare = $this->model->create($this->params);
		if(isset($prepare['failure'])){
			$res->success = false;
			$res->message = "Failed to create record";
			$res->data = $prepare['msg'];
		}else{
			$res->success = true;
			$res->message = "record created";
			$res->data = $prepare;
		}
		return $res->to_json();
	}

	public function update() {
		$res = new Response();
		$prepare = $this->model->update($this->id, $this->params);
		if(isset($prepare['failure'])){
			$res->success = false;
			$res->message = "Failed to update record";
			$res->data = $prepare['msg'];
		}else{
			$res->success = true;
			$res->message = "record updated";
			$res->data = $prepare;
		}
		return $res->to_json();
	}

	public function destroy() {
		$res = new Response();
		$prepare = $this->model->destroy($this->id);
		if(isset($prepare['failure'])){
			$res->success = false;
			$res->message = "Failed to delete record";
			$res->data = $prepare['msg'];
		}else{
			$res->success = true;
			$res->message = "record deleted";
			$res->data = $prepare;
		}
		return $res->to_json();
	}

}
