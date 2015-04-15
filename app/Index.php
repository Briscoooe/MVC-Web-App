<?php
/* creates a session or resumes the current one based on a session identifier passed via a GET or POST request, or passed via a cookie. The $_SESSION superglobal array can contain variable accessible accross the entire session */
session_start ();

$action = "";
if (! empty ( $_REQUEST ['action'] ))
	$action = $_REQUEST ['action'];

include "models/Model.php";
include "controllers/Controller.php";
include "views/View.php";

$model = new Model ();
$controller = new Controller ( $model, $action, $_REQUEST );
$view = new View ( $controller, $model );
$view->output ();
?>