<?php

namespace App\Data;

use App\Entity\Category;
use App\Entity\Thematic;

class SearchRecipe
{
    public ?string $name = null;
    public ?string $difficulty = null;
    public ?string $recipeCost = null;
    public ?Thematic $thematic = null;
    public ?Category $category = null;
}
