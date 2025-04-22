<?php

namespace App\Tests\Service\Notification;

use App\Entity\Product;
use App\Service\Notification\EmailNotification;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationTest extends TestCase
{
    private LoggerInterface $logger;
    private MailerInterface $mailer;
    private EmailNotification $emailNotification;
    private Product $product;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        
        $this->emailNotification = new EmailNotification(
            $this->logger,
            $this->mailer,
            'test@example.com',
            'admin@example.com'
        );
        
        $this->product = $this->createMock(Product::class);
        $this->product->method('getId')->willReturn(1);
        $this->product->method('getName')->willReturn('Test Product');
        $this->product->method('getPrice')->willReturn(99.99);
    }

    public function testSendEmailNotification(): void
    {
        // Set up logger expectations
        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                [$this->stringContains('notification about product "Test Product" (1)')],
                [$this->equalTo('Email notification sent successfully')]
            );
        
        // Set up mailer expectations
        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) {
                $this->assertEquals(['test@example.com' => ''], $email->getFrom());
                $this->assertEquals(['admin@example.com' => ''], $email->getTo());
                $this->assertEquals('Product created: Test Product', $email->getSubject());
                $this->assertStringContainsString('Product operation: created', $email->getTextBody());
                $this->assertStringContainsString('Product ID: 1', $email->getTextBody());
                return true;
            }));
        
        // Call the method
        $this->emailNotification->send($this->product, 'created');
    }

    public function testSendEmailWithError(): void
    {
        // Set up logger expectations
        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('notification about product "Test Product" (1)'));
        
        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error sending email notification:'));
        
        // Make mailer throw exception
        $this->mailer->expects($this->once())
            ->method('send')
            ->willThrowException(new \Exception('Test error'));
        
        // Call the method
        $this->emailNotification->send($this->product, 'created');
    }
} 