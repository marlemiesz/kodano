<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\ProductCategoriesLinkDto;

/**
 * Provider for the product-categories linking operation.
 * Since this is only a POST operation, this provider is not actually used
 * but is required by the ApiResource configuration.
 */
class ProductCategoriesLinkProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // This provider handles only POST operations and will not be called for retrieving data
        return new ProductCategoriesLinkDto();
    }
} 