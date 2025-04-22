<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create categories
        $categories = [];
        
        $electronicsCat = new Category();
        $electronicsCat->setCode('ELECT');
        $categories[] = $electronicsCat;
        $manager->persist($electronicsCat);
        
        $booksCat = new Category();
        $booksCat->setCode('BOOKS');
        $categories[] = $booksCat;
        $manager->persist($booksCat);
        
        $clothingCat = new Category();
        $clothingCat->setCode('CLOTH');
        $categories[] = $clothingCat;
        $manager->persist($clothingCat);
        
        $homeGardenCat = new Category();
        $homeGardenCat->setCode('GARDEN');
        $categories[] = $homeGardenCat;
        $manager->persist($homeGardenCat);
        
        $sportsCat = new Category();
        $sportsCat->setCode('SPORTS');
        $categories[] = $sportsCat;
        $manager->persist($sportsCat);

        // Create products
        $products = [
            [
                'name' => 'Smartphone X10',
                'price' => 999.99,
                'categories' => [$electronicsCat]
            ],
            [
                'name' => 'Laptop Pro 15',
                'price' => 1499.99,
                'categories' => [$electronicsCat]
            ],
            [
                'name' => 'Programming in PHP',
                'price' => 39.99,
                'categories' => [$booksCat, $electronicsCat]
            ],
            [
                'name' => 'Casual T-Shirt',
                'price' => 24.99,
                'categories' => [$clothingCat, $sportsCat]
            ],
            [
                'name' => 'Running Shoes',
                'price' => 89.99,
                'categories' => [$clothingCat, $sportsCat]
            ],
            [
                'name' => 'Garden Chair',
                'price' => 49.99,
                'categories' => [$homeGardenCat]
            ],
            [
                'name' => 'Tennis Racket',
                'price' => 129.99,
                'categories' => [$sportsCat]
            ],
            [
                'name' => 'Bluetooth Speaker',
                'price' => 79.99,
                'categories' => [$electronicsCat]
            ],
            [
                'name' => 'Novel - The Adventure',
                'price' => 19.99,
                'categories' => [$booksCat]
            ],
            [
                'name' => 'Plant Pot',
                'price' => 15.99,
                'categories' => [$homeGardenCat]
            ]
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setPrice($productData['price']);
            
            foreach ($productData['categories'] as $category) {
                $product->addCategory($category);
            }
            
            $manager->persist($product);
        }

        $manager->flush();
    }
} 