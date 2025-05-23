<?php

namespace App\Service\Notification;

use App\Entity\Product;
use Psr\Log\LoggerInterface;

class LogNotification extends AbstractNotification
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }

    protected function doSend(Product $product, string $operation): void
    {
        $categories = $product->getCategories()->map(fn($category) => $category->getCode())->toArray();
        
        $this->logger->info(
            sprintf(
                'Product %s: %s (ID: %d, Price: %.2f, Categories: %s)',
                $operation,
                $product->getName(),
                $product->getId(),
                $product->getPrice(),
                implode(', ', $categories)
            )
        );
    }
} 