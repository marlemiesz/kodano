<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use App\DataPersister\ProductDataPersister;
use App\Entity\Product;
use App\Service\Notification\NotificationManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ProductDataPersisterTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private NotificationManager $notificationManager;
    private ProductDataPersister $persister;
    private Product $product;
    private Operation $operation;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->notificationManager = $this->createMock(NotificationManager::class);
        $this->persister = new ProductDataPersister($this->entityManager, $this->notificationManager);
        
        $this->product = $this->createMock(Product::class);
        $this->product->method('getId')->willReturn(1);
        $this->product->method('getName')->willReturn('Test Product');
        $this->product->method('getPrice')->willReturn(99.99);
        
        $this->operation = $this->createMock(Operation::class);
    }
    
    public function testCreateProduct(): void
    {
        // EntityManager nie zawiera produktu, więc zostanie on utworzony
        $this->entityManager->method('contains')->willReturn(false);
        
        $this->entityManager->expects($this->once())->method('persist')->with($this->product);
        $this->entityManager->expects($this->once())->method('flush');
        $this->notificationManager->expects($this->once())->method('notify')
            ->with($this->product, 'created');
            
        $result = $this->persister->process($this->product, $this->operation);
        $this->assertSame($this->product, $result);
    }
    
    public function testUpdateProduct(): void
    {
        // EntityManager zawiera produkt, więc zostanie on zaktualizowany
        $this->entityManager->method('contains')->willReturn(true);
        
        $this->entityManager->expects($this->once())->method('persist')->with($this->product);
        $this->entityManager->expects($this->once())->method('flush');
        $this->notificationManager->expects($this->once())->method('notify')
            ->with($this->product, 'updated');
            
        $result = $this->persister->process($this->product, $this->operation);
        $this->assertSame($this->product, $result);
    }
    
    public function testDeleteProduct(): void
    {
        // Używamy klasy anonimowej, która implementuje oba interfejsy
        $deleteOperation = new class extends Operation implements DeleteOperationInterface {
            public function getClass(): string 
            {
                return 'Test';
            }
            
            public function getUriTemplate(): ?string
            {
                return '/test';
            }
        };
        
        // Oczekiwania
        $this->entityManager->expects($this->once())->method('remove')->with($this->product);
        $this->entityManager->expects($this->once())->method('flush');
        $this->notificationManager->expects($this->once())->method('notify')
            ->with($this->product, 'deleted');
            
        $result = $this->persister->process($this->product, $deleteOperation);
        $this->assertSame($this->product, $result);
    }
} 