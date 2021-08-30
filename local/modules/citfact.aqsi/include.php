<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses("citfact.aqsi", 
    [
        "\Citfact\Aqsi\Rest" => "lib/rest.php",
        "\Citfact\Aqsi\Orders" => "lib/orders.php",
        "\Citfact\Aqsi\Orders\Create" => "lib/orders.create.php",
        "\Citfact\Aqsi\Clients" => "lib/clients.php",
        "\Citfact\Aqsi\Clients\Create" => "lib/clients.create.php",
        "\Citfact\Aqsi\Clients\Read" => "lib/clients.read.php"
    ]
);
