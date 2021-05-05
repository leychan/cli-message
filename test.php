<?php

require __DIR__ . '/vendor/autoload.php';

$message = '';

$cli_message = new \cliMessage\CliMessage();
$cli_message->setMessage($message);
$cli_message->setPerLineFontQuantity(6);
$cli_message->run();