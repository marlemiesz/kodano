<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class ProductCategoriesLinkDto
{
    #[Assert\NotBlank(message: 'At least one category URI must be specified')]
    #[Assert\Count(min: 1, minMessage: 'At least one category URI must be specified')]
    #[Assert\All([
        new Assert\NotBlank(message: 'Category URI must not be empty'),
        new Assert\Regex(
            pattern: '/^\/api\/categories\/\d+$/',
            message: 'Invalid category URI format. Expected format: /api/categories/{id}'
        )
    ])]
    #[Groups(['product_categories:write', 'product_categories:read'])]
    private array $category = [];

    public function getCategory(): array
    {
        return $this->category;
    }

    public function setCategory(array $category): self
    {
        $this->category = $category;
        return $this;
    }
} 