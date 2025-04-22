<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Product;
use App\Service\Notification\NotificationManager;
use Doctrine\ORM\EntityManagerInterface;

class ProductDataPersister implements ProcessorInterface
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

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof Product) {
            return $data;
        }
        
        // Sprawdź czy to operacja usunięcia
        if ($operation instanceof DeleteOperationInterface) {
            // Powiadom o usunięciu produktu
            $this->notificationManager->notify($data, 'deleted');
            
            // Usuń produkt
            $this->entityManager->remove($data);
            $this->entityManager->flush();
            
            return $data;
        }
        
        $isNew = !$this->entityManager->contains($data);
        
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        
        $operationType = $isNew ? 'created' : 'updated';
        $this->notificationManager->notify($data, $operationType);
        
        return $data;
    }
} 