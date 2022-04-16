<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageIndexer;

use MateuszMesek\DocumentDataCmsPage\Command\GetDocumentDataByPageIdAndStoreId;
use MateuszMesek\DocumentDataIndexIndexerApi\DataResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\DimensionResolverInterface;
use Traversable;

class DataResolver implements DataResolverInterface
{
    private DimensionResolverInterface $storeIdResolver;
    private GetDocumentDataByPageIdAndStoreId $getDocumentDataByPageId;

    public function __construct(
        DimensionResolverInterface $storeIdResolver,
        GetDocumentDataByPageIdAndStoreId $getDocumentDataByPageId
    )
    {
        $this->storeIdResolver = $storeIdResolver;
        $this->getDocumentDataByPageId = $getDocumentDataByPageId;
    }

    public function resolve(array $dimensions, Traversable $entityIds): Traversable
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        foreach ($entityIds as $entityId) {
            $data = $this->getDocumentDataByPageId->execute($entityId, $storeId);

            if (empty($data)) {
                return;
            }

            yield $data;
        }
    }
}
