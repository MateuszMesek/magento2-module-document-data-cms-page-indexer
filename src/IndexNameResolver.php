<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageIndexer;

use MateuszMesek\DocumentDataIndexIndexerApi\DimensionResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\IndexNameResolverInterface;

class IndexNameResolver implements IndexNameResolverInterface
{
    private DimensionResolverInterface $storeIdResolver;

    public function __construct(
        DimensionResolverInterface $storeIdResolver
    )
    {
        $this->storeIdResolver = $storeIdResolver;
    }

    public function resolve(array $dimensions): string
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        return "cms_page_$storeId";
    }
}
