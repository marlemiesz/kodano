<?php

namespace App\Service\Notification;

use App\Entity\Product;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotification extends AbstractNotification
{
    private MailerInterface $mailer;
    private string $fromEmail;
    private string $toEmail;

    public function __construct(
        #[Autowire(service: 'monolog.logger.notification')]
        LoggerInterface $logger,
        MailerInterface $mailer,
        string $fromEmail = 'noreply@example.com',
        string $toEmail = 'admin@example.com'
    ) {
        parent::__construct($logger);
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
        $this->toEmail = $toEmail;
    }

    protected function doSend(Product $product, string $operation): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($this->toEmail)
            ->subject("Product {$operation}: {$product->getName()}")
            ->text(sprintf(
                "Product operation: %s\nProduct ID: %d\nProduct Name: %s\nProduct Price: %.2f\n",
                $operation,
                $product->getId(),
                $product->getName(),
                $product->getPrice()
            ));

        try {
            $this->mailer->send($email);
            $this->logger->info('Email notification sent successfully');
        } catch (\Exception $e) {
            $this->logger->error('Error sending email notification: ' . $e->getMessage());
        }
    }
} 