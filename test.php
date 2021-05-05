<?php

require __DIR__ . '/vendor/autoload.php';

$message = '123';

$cli_message = new \cliMessage\CliMessage();
$cli_message->setMessage($message);
$cli_message->setPerLineQuantity(6);
$cli_message->run();