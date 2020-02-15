<?php

    $SourceDirectory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'src';
    include_once($SourceDirectory . DIRECTORY_SEPARATOR . 'TelegramClientManager' . DIRECTORY_SEPARATOR . 'TelegramClientManager.php');

    $TelegramClientManager = new \TelegramClientManager\TelegramClientManager();
