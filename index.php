<?php
//error_reporting(0);
error_reporting(E_ALL);
session_start();

require_once ("classes/pdo.php");
require_once ("classes/library.php");
require_once ("classes/model.php");
require_once ("classes/view.php");
require_once ("classes/controller.php");
require_once ("classes/site.php");
require_once ("classes/imageacceptor.php");
require_once ("classes/adslib.php");
require_once ("classes/userauth.php");
require_once ("classes/prelib.php");
require_once ("classes/lot.php");

$site = new c_site();
$site->execute();
