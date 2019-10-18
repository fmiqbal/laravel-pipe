<?php

return [
    'modules' => [
        'auth' => false,
    ],

    'auth' => [
        'table_name'       => 'users',
        'primary_key'      => 'id',
        'human_identifier' => 'email',
        'policies'         => [
            /**
             * Available values :
             * any => user can view object created by anyone
             * self => user can only view object created by itself
             */
            'credentials' => [
                'view_other'   => false,
                'update_other' => false,
                'delete_other' => false,
            ],
        ],
    ],
];
