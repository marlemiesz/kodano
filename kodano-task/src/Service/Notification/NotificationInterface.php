<?php

namespace App\Service\Notification;

use App\Entity\Product;

interface NotificationInterface
{
    /**
     * Send notification about product operation
     */
    public function send(Product $product, string $operation): void;
} 