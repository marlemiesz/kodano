<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Persistence\DataPersisterInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Product;
use App\Service\Notification\NotificationManager;
use Doctrine\ORM\EntityManagerInterface;

class ProductDataPersister implements DataPersisterInterface, ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private NotificationManager $notificationManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationManager $notificationManager
    ) {
        $this->entityManager = $entityManager;
        $this->notificationManager = $notificationManager;
    }

    public function supports($data, Operation $operation = null, array $context = []): bool
    {
        return $data instanceof Product;
    }

    public function persist($data, Operation $operation = null, array $context = [])
    {
        $isNew = $data->getId() === null;
        
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        
        $operationType = $isNew ? 'created' : 'updated';
        $this->notificationManager->notify($data, $operationType);
        
        return $data;
    }

    public function remove($data, Operation $operation = null, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
        
        $this->notificationManager->notify($data, 'deleted');
        
        return $data;
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $isNew = !$this->entityManager->contains($data);
        
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        
        $operationType = $isNew ? 'created' : 'updated';
        $this->notificationManager->notify($data, $operationType);
        
        return $data;
    }
} 