<?php
namespace tests\jsonrpc;

use extas\components\operations\JsonRpcOperation;
use extas\components\plugins\jsonrpc\DetailedDynamicPluginsPrepared;
use extas\interfaces\operations\IJsonRpcOperation;
use extas\interfaces\operations\jsonrpc\ISpecs;
use extas\interfaces\operations\jsonrpc\specs\ISpecsProperty;
use extas\interfaces\samples\parameters\ISampleParameter;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

class DetailedPluginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testPlugin()
    {
        $plugin = new DetailedDynamicPluginsPrepared();
        $operation = $plugin(new JsonRpcOperation([
            JsonRpcOperation::FIELD__PARAMETERS => [
                JsonRpcOperation::PARAM__METHOD => [
                    ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__METHOD,
                    ISampleParameter::FIELD__VALUE => 'index'
                ]
            ],
            JsonRpcOperation::FIELD__SPECS => [
                ISpecs::FIELD__REQUEST => [
                    ISpecsProperty::FIELD__TYPE => 'object',
                    ISpecsProperty::FIELD__PROPERTIES => []
                ],
                ISpecs::FIELD__RESPONSE => [
                    ISpecsProperty::FIELD__TYPE => 'object',
                    ISpecsProperty::FIELD__PROPERTIES => [
                        'items' => [
                            'test' => [
                                ISpecsProperty::FIELD__TYPE => 'string'
                            ]
                        ],
                        'total' => [
                            'type' => 'int'
                        ]
                    ]
                ]
            ]
        ]));

        $this->assertEquals(
            [
                IJsonRpcOperation::FIELD__PARAMETERS => [
                    JsonRpcOperation::PARAM__METHOD => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__METHOD,
                        ISampleParameter::FIELD__VALUE => 'index'
                    ]
                ],
                IJsonRpcOperation::FIELD__SPECS => [
                    ISpecs::FIELD__REQUEST => [
                        ISpecsProperty::FIELD__TYPE => 'object',
                        ISpecsProperty::FIELD__PROPERTIES => [
                            'limit' => [
                                'type' => 'int'
                            ],
                            'offset' => [
                                'type' => 'int'
                            ],
                            'sort' => [
                                'type' => 'array',
                                'properties' => [
                                    'test' => [
                                        'type' => 'string'
                                    ]
                                ]
                            ],
                            'filter' => [
                                'type' => 'object',
                                'properties' => [
                                    'test' => [
                                        'type' => 'string'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    ISpecs::FIELD__RESPONSE => [
                        ISpecsProperty::FIELD__TYPE => 'object',
                        ISpecsProperty::FIELD__PROPERTIES => [
                            'items' => [
                                'test' => [
                                    ISpecsProperty::FIELD__TYPE => 'string'
                                ]
                            ],
                            'total' => [
                                'type' => 'int'
                            ]
                        ]
                    ]
                ]
            ],
            $operation->__toArray(),
            'Incorrect operation: ' . print_r($operation->__toArray(), true)
        );
    }
}
