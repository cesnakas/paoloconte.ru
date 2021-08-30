<?
header("Content-Type: application/x-javascript");
$hash = "12345678";
$config = array("appmap" =>
	array("main" => "/paoloconte_app/",
		"left" => "/paoloconte_app/menu.php",
		"settings" => "/paoloconte_app/settings.php",
		"hash" => substr($hash, rand(1, strlen($hash)))
	)
);
echo json_encode($config);