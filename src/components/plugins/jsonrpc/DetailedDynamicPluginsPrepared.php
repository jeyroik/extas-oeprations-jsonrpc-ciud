<?php
namespace extas\components\plugins\jsonrpc;

use extas\components\operations\jsonrpc\specs\SpecsProperty;
use extas\components\plugins\Plugin;
use extas\interfaces\operations\IJsonRpcOperation;
use extas\interfaces\operations\jsonrpc\ISpecs;
use extas\interfaces\stages\IStageDynamicPluginsPrepared;

/**
 * Class DetailedDynamicPluginsPrepared
 *
 * @package extas\components\plugins\jsonrpc
 * @author jeyroik <jeyroik@gmail.com>
 */
class DetailedDynamicPluginsPrepared extends Plugin implements IStageDynamicPluginsPrepared
{
    /**
     * @param IJsonRpcOperation $operation
     * @return IJsonRpcOperation
     */
    public function __invoke(IJsonRpcOperation $operation): IJsonRpcOperation
    {
        if ($operation->getMethod() == 'index') {
            $operation->setSpecsFromObject($this->addedIndexProperties($operation->getSpecsAsObject()));
        }

        return $operation;
    }

    /**
     * @param ISpecs $specs
     * @return ISpecs
     */
    protected function addedIndexProperties(ISpecs $specs): ISpecs
    {
        $response = $specs->getResponse();
        $props = $response->getProperties();

        $sort = new SpecsProperty();
        $sort
            ->setType('array')
            ->setName('sort')
            ->setProperties($props['items'] ?? []);

        $filter = new SpecsProperty();
        $filter
            ->setType('object')
            ->setName('filter')
            ->setProperties($props['items'] ?? []);

        $request = $specs->getRequest();

        $request->hasProperty('limit') || $request->addProperty(new SpecsProperty([
                SpecsProperty::FIELD__NAME => 'limit',
                SpecsProperty::FIELD__TYPE => 'int'
            ]));
        $request->hasProperty('offset') || $request->addProperty(new SpecsProperty([
                SpecsProperty::FIELD__NAME => 'offset',
                SpecsProperty::FIELD__TYPE => 'int'
            ]));
        $request->hasProperty('sort') || $request->addProperty($sort);
        $request->hasProperty('filter') || $request->addProperty($filter);

        $specs->setRequest($request);

        return $specs;
    }
}
