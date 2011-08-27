<?php

	// include the core startup file
	require './core/startup.php';

	// load the loader
	ezphp::ez_set('load', new load());
	// load the router
	ezphp::ez_set('router', new router());
	// load the model
	ezphp::ez_set('model', new model());
	// load the view
	ezphp::ez_set('view', new view());

	// dispatch the controller
	ezphp::ez_get('router')->dispatch();
?>

