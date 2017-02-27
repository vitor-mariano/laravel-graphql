<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class CommentType extends BaseType
{
    protected $attributes = [
        'name' => 'Comment',
        'description' => 'Comment type.'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int())
            ],
            'user' => [
                'type' => Type::nonNull(GraphQL::type('User'))
            ],
            'post' => [
                'type' => Type::nonNull(GraphQL::type('Post'))
            ],
            'body' => [
                'type' => Type::nonNull(Type::string())
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::string())
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::string())
            ]
        ];
    }
}
