<?php
return [

    'default' => 'local',

    'cloud' => 's3',

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ],
        'uploads' => [
            'driver' => 'local',
            'root' => storage_path('uploads'),
        ],

    ],

    'storage' => [
        'driver' => 'local',
        'root'   => storage_path(),
    ],

];