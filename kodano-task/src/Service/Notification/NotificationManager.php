<?php

namespace App\Service\Notification;

use App\Entity\Product;

class NotificationManager
{
    /**
     * @var NotificationInterface[]
     */
    private array $notificationServices = [];

    public function addNotificationService(NotificationInterface $notificationService): void
    {
        $this->notificationServices[] = $notificationService;
    }

    public function notify(Product $product, string $operation): void
    {
        foreach ($this->notificationServices as $notificationService) {
            $notificationService->send($product, $operation);
        }
    }
} 