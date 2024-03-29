<?php declare(strict_types=1);

namespace MateuszMesek\DocumentDataCmsPageIndexer\Model;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\DB\Select;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\DimensionResolverInterface;
use MateuszMesek\DocumentDataIndexIndexerApi\Model\IdsResolverInterface;
use Throwable;
use Traversable;

class IdsResolver implements IdsResolverInterface
{
    public function __construct(
        private readonly DimensionResolverInterface $storeIdResolver,
        private readonly CollectionFactory          $collectionFactory
    )
    {
    }

    public function resolve(array $dimensions): Traversable
    {
        $storeId = $this->storeIdResolver->resolve($dimensions);

        $collection = $this->collectionFactory->create();
        $collection->addStoreFilter($storeId);
        $collection->setOrder(PageInterface::PAGE_ID, $collection::SORT_ORDER_ASC);
        $collection->setPageSize(100);
        $collection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns([PageInterface::PAGE_ID]);

        try {
            $collection->getSelect()->setPart('disable_staging_preview', true);
        } catch (Throwable $exception) {

        }

        $lastId = 0;

        while (true) {
            $part = (clone $collection);
            $part->getSelect()->where('main_table.page_id > ?', $lastId);
            $part->load();

            $ids = array_map(
                static function (PageInterface $page) {
                    return (int)$page->getId();
                },
                $part->getItems()
            );

            if (empty($ids)) {
                return;
            }

            $lastId = end($ids);

            yield from $ids;
        }
    }
}
