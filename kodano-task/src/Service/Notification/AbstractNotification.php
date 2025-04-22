<?php

namespace App\Service\Notification;

use App\Entity\Product;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class AbstractNotification implements NotificationInterface
{
    protected LoggerInterface $logger;

    public function __construct(
        #[Autowire(service: 'monolog.logger.notification')]
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function send(Product $product, string $operation): void
    {
        $this->logger->info(sprintf(
            '%s notification about product "%s" (%d) - operation: %s',
            static::class,
            $product->getName(),
            $product->getId(),
            $operation
        ));

        $this->doSend($product, $operation);
    }

    /**
     * Implementation of the actual notification sending
     */
    abstract protected function doSend(Product $product, string $operation): void;
} 