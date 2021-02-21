<?php

    require("ppm");
    ppm_import("net.intellivoid.telegram_client_manager");

    $TelegramClientManager = new \TelegramClientManager\TelegramClientManager();
    $TelegramClientManager->applyAccountIdPatch();