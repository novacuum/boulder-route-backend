<?php
declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;

final class JwtDecorator implements OpenApiFactoryInterface {
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new \ArrayObject([
            'type'       => 'object',
            'properties' => [
                'token' => [
                    'type'     => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type'     => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['Credentials'] = new \ArrayObject([
            'type'       => 'object',
            'properties' => [
                'username'    => [
                    'type'    => 'string',
                    'example' => 'johndoe@example.com',
                ],
                'password' => [
                    'type'    => 'string',
                    'example' => 'apassword',
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'JWT Token',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Token'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content'     => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get JWT token to login.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/login_check', $pathItem);

        $schemas['LogoutResponse'] = new \ArrayObject([
            'type'       => 'object',
            'properties' => [
                'Message' => [
                    'type'     => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'JWT Token',
            post: new Model\Operation(
                operationId: 'logout',
                tags: ['Logout'],
                responses: [
                    '200' => [
                        'description' => 'Logout',
                        'content'     => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/LogoutResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Logout.',
                requestBody: new Model\RequestBody(
                    description: 'Logout'
                ),
            ),
        );

        $openApi->getPaths()->addPath('/api/v2/logout', $pathItem);

        return $openApi;
    }
}