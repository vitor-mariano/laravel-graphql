<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class AuthType extends BaseType
{
    protected $attributes = [
        'name' => 'Auth',
        'description' => 'Auth type.'
    ];

    public function fields()
    {
        return [
            'token' => [
                'type' => Type::string()
            ]
        ];
    }
}
