<?php

return [
    'stats_start_day' => env('GAME_STATS_START_DAY', '20260611'),
    'stats_base_url' => env('GAME_STATS_BASE_URL', 'http://81.69.9.148:8888/call'),
    'request_timeout' => env('GAME_STATS_REQUEST_TIMEOUT', 15),
    'chunk_days' => env('GAME_STATS_CHUNK_DAYS', 7),
];
