<?php
	require_once('remote/init.php');

	// Get Request
	$request = new Request(array('restful' => true));

	// curl -X GET http://webdev.com/projects/gazprom/restapi/users -d "some=var" -d "other=var2"
	// curl -H "Content-Type: application/json" -X POST -d '{"id": 1}' http://webdev.com/projects/gazprom/restapi/users
	// echo "<P>request: " . $request->to_string();

	// Get Controller
	require_once('remote/app/controllers/' . $request->controller . '.php');
	$controller_name = ucfirst($request->controller);

	try {
		$controller = new $controller_name;
		// Dispatch request
		echo $controller->dispatch($request);
	} catch (Exception $e) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
		$res = new Response();
		$res->success = false;
		$res->message = $e->getMessage();
		echo $res->to_json();
}

