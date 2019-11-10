<?php
require_once 'Classes/RequestHandler/RequestHandler.php';

$startProcessingMicrotime = microtime(true);

$requestHandler = RequestHandler::getInstanse($_POST['requesttype'], $_POST['daterange_txt'], $_SERVER['REMOTE_ADDR'], $startProcessingMicrotime);

$requestHandler->validateInputData();
$requestHandler->calculateDifference();
$requestHandler->saveToDB();
$requestHandler->generateResponse('Difference between end and start date = ' . $requestHandler->getDiff() . ' day(s).', 'notification');

exit();
