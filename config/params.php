<?php

return [
    'adminEmail' => 'admin@example.com',
    'pagination_offset' => 0,
    'pagination_limit'  => 10,
    'static_routes' => ['v1/messages', 'v1/files', 'v1/friends'],
    'entities_config' => realpath(__DIR__ . '/../config/entities.php'),
];
