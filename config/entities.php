<?php
return [
    'base' => [
        'meta' => [
            'virtual' => true,
            'parent' => false,
        ],
        'attributes' => [
            [
                'name' => 'name',
                'type' => 'string',
                'unique' => true,
                'required' => true,
                'max' => 50,
            ],
            [
                'name' => 'description',
                'type' => 'string',
                'max' => 255,
            ],
        ],
    ],

    // 'friends' => [
    //     'meta' => [
    //         'virtual' => false,
    //         'parent' => false,
    //     ],
    //     'attributes' => [
    //         [
    //             'name' => 'user_id',
    //             'type' => 'integer',
    //             'unique' => false,
    //             'required' => true,
    //         ],
    //         [
    //             'name' => 'friend_id',
    //             'type' => 'integer',
    //             'unique' => false,
    //             'required' => true,
    //         ],
    //         [
    //             'name' => 'subscription',
    //             'type' => 'integer',
    //             'unique' => false,
    //             'required' => true,
    //         ],
    //     ]
    // ],

    'posts' => [
        'meta' => [
            'virtual' => false,
            'parent' => false,
        ],
        'attributes' => [
            [
                'name' => 'post_user_id',
                'type' => 'integer',
                'unique' => false,
                'required' => true,
            ],
            [
                'name' => 'post_type',
                'type' => 'string',
                'unique' => false,
                'required' => true,
            ],
            [
                'name' => 'post_content',
                'type' => 'string',
                'unique' => false,
            ],
            [
                'name' => 'post_attachments',
                'type' => 'jsonb',
            ],
            [
                'name' => 'post_poll',
                'type' => 'integer',
            ],
            [
                'name' => 'post_poll_title',
                'type' => 'string',
                'max' => 255,
            ],
            [
                'name' => 'post_poll_all_voted',
                'type' => 'integer',
            ],
            [
                'name' => 'post_comment_count',
                'type' => 'integer',
            ],
            [
                'name' => 'post_like_users',
                'type' => 'jsonb',
            ],
            [
                'name' => 'post_like_count',
                'type' => 'integer',
            ],
            [
                'name' => 'post_attachments',
                'type' => 'jsonb',
            ],

            [
                'name' => 'post_community',
                'type' => 'integer',
                'max' => 255,
            ],
        ],
    ],


 
    'files' => [
        'meta' => [
            'parent' => false,
            'active' => false,
            'class' => \app\modules\v1\models\FileResource::class
        ],
    ],

   




];