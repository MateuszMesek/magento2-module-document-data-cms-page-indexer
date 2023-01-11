<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageIndexer\Model;

use MateuszMesek\DocumentDataCmsPage\Model\Command\GetDocumentDataByPageIdAndStoreId;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\DataResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\DimensionResolverInterface;
use Traversable;

class DataResolver implements DataResolverInterface
{
    public function __construct(
        private readonly DimensionResolverInterface        $storeIdResolver,
        private readonly GetDocumentDataByPageIdAndStoreId $getDocumentDataByPageId
    )
    {
    }

    public function resolve(array $dimensions, Traversable $entityIds): Traversable
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        foreach ($entityIds as $entityId) {
            $data = $this->getDocumentDataByPageId->execute((int)$entityId, $storeId);

            yield $entityId => $data;
        }
    }
}
