<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class PostType extends BaseType
{
    protected $attributes = [
        'name' => 'Post',
        'description' => 'Post type.'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int())
            ],
            'body' => [
                'type' => Type::nonNull(Type::string())
            ],
            'user' => [
                'type' => GraphQL::type('User')
            ],
            'comments' => [
                'type' => Type::listOf(GraphQL::type('Comment'))
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
