<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ProductCategoriesLinkDto;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Service\Notification\NotificationManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductCategoriesLinkProcessor implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private NotificationManager $notificationManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        NotificationManager $notificationManager
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->notificationManager = $notificationManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ProductCategoriesLinkDto
    {
        if (!$data instanceof ProductCategoriesLinkDto) {
            throw new BadRequestHttpException('Invalid input data');
        }

        // Get product ID from URI variables
        if (!isset($uriVariables['id']) || !is_numeric($uriVariables['id'])) {
            throw new BadRequestHttpException('Product ID is missing or invalid in the URI');
        }
        
        $productId = (int) $uriVariables['id'];
        $categoryUris = $data->getCategory();

        // Get the product
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new NotFoundHttpException(sprintf('Product with ID %d not found', $productId));
        }

        // Extract category IDs from URIs
        $categoryIds = [];
        foreach ($categoryUris as $uri) {
            // Extract ID from URI, expecting format like /api/categories/123
            if (preg_match('/\/api\/categories\/(\d+)$/', $uri, $matches)) {
                $categoryIds[] = (int) $matches[1];
            } else {
                throw new BadRequestHttpException(sprintf('Invalid category URI format: %s', $uri));
            }
        }

        // Get the categories
        $categories = $this->categoryRepository->findBy(['id' => $categoryIds]);
        if (count($categories) !== count($categoryIds)) {
            // Find which categories don't exist
            $foundIds = array_map(fn(Category $category) => $category->getId(), $categories);
            $missingIds = array_diff($categoryIds, $foundIds);
            
            throw new BadRequestHttpException(sprintf(
                'Categories with the following IDs do not exist: %s',
                implode(', ', $missingIds)
            ));
        }

        // Store original categories for comparison
        $originalCategoryIds = [];
        foreach ($product->getCategories() as $category) {
            $originalCategoryIds[] = $category->getId();
        }

        // Clear existing categories
        foreach ($product->getCategories() as $existingCategory) {
            $product->removeCategory($existingCategory);
        }

        // Add new categories
        foreach ($categories as $category) {
            $product->addCategory($category);
        }

        // Save changes
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Send notification about categories update
        $this->notificationManager->notify($product, 'categories_updated');

        return $data;
    }
} 