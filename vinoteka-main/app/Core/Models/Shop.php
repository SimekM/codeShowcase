<?php

namespace App\Core\Models;

use Nette;


final class Shop
{



    public function __construct(
        private Nette\Database\Explorer       $database,
        private Nette\Database\Context        $context
    ) {}





    public function getCategories()
    {
        // Get all top-level categories
        $wineCategories = $this->database->table('tags_taxonomy')
            ->where('taxonomy', 'product_category')
            ->where('parent_id IS NULL')
            ->fetchAll();

        $countries = $this->database->table('tags_taxonomy')
            ->where('taxonomy', 'product_country')
            ->where('parent_id IS NULL')
            ->fetchAll();

        $result = (object) null;
        $result->categories = [];
        $result->countries = [];

        // Process country categories

        foreach ($countries as $country) {
            $countryDetails = $this->database->table('tags')
                ->where('id', $country->tag_id)
                ->fetch();


            // Get regions (second level)


            $regions = $this->database->table('tags_taxonomy')
                ->where('taxonomy', 'product_country')
                ->where('parent_id', $countryDetails->id)
                ->fetchAll();


            $regionsArray = [];

            foreach ($regions as $region) {
                $regionDetails = $this->database->table('tags')
                    ->where('id', $region->tag_id)
                    ->fetch();

                // Get wineries (third level)
                $wineries = $this->database->table('tags_taxonomy')
                    ->where('taxonomy', 'product_country')
                    ->where('parent_id', $regionDetails->id)
                    ->fetchAll();

                $wineriesArray = [];

                foreach ($wineries as $winery) {
                    $wineryDetails = $this->database->table('tags')
                        ->where('id', $winery->tag_id)
                        ->fetch();

                    $wineriesArray[] = (object) [
                        'id' => $wineryDetails->id,
                        'title' => $wineryDetails->title,
                        'slug' => $wineryDetails->slug,
                        'img_src' => $wineryDetails->img_src
                    ];
                }

                $regionsArray[] = (object) [
                    'id' => $regionDetails->id,
                    'title' => $regionDetails->title,
                    'slug' => $regionDetails->slug,
                    'img_src' => $regionDetails->img_src,
                    'wineries' => $wineriesArray
                ];
            }

            $result->countries[] = (object) [
                'id' => $countryDetails->id,
                'title' => $countryDetails->title,
                'slug' => $countryDetails->slug,
                'img_src' => $countryDetails->img_src,
                'regions' => $regionsArray
            ];
        }

        // Process wine categories
        foreach ($wineCategories as $category) {
            $categoryDetails = $this->database->table('tags')
                ->where('id', $category->tag_id)
                ->fetch();

            // Get subcategories
            $subcategories = $this->database->table('tags_taxonomy')
                ->where('taxonomy', 'product_category')
                ->where('parent_id', $category->id)
                ->fetchAll();

            $subcategoriesArray = [];

            foreach ($subcategories as $subcategory) {
                $subcategoryDetails = $this->database->table('tags')
                    ->where('id', $subcategory->tag_id)
                    ->fetch();

                $subcategoriesArray[] = (object) [
                    'id' => $subcategoryDetails->id,
                    'title' => $subcategoryDetails->title,
                    'slug' => $subcategoryDetails->slug,
                    'img_src' => $subcategoryDetails->img_src
                ];
            }

            $result->categories[] = (object) [
                'id' => $categoryDetails->id,
                'title' => $categoryDetails->title,
                'slug' => $categoryDetails->slug,
                'img_src' => $categoryDetails->img_src,
                'subcategories' => $subcategoriesArray
            ];
        }

        return $result;
    }


    public function getCategoryFromProductId($product_id)
    {
        $query = "SELECT t.id, t.title, t.slug
        FROM tags_relationships tr
        JOIN tags t ON t.id = tr.tag_id
        JOIN tags_taxonomy tt ON tt.tag_id = t.id
        WHERE tr.product_id = ? 
        AND tt.taxonomy = 'product_category'
        AND tt.parent_id IS NULL
        ";
        return $this->database->query($query, [$product_id])->fetch();
    }



    public function getCategoriesFromProductId($product_id)
    {
        $query = "
            SELECT tt.taxonomy, t.id, t.title, t.slug
            FROM tags_taxonomy tt
            JOIN tags_relationships tr ON tr.tag_id = tt.id
            JOIN tags t ON t.id = tt.tag_id
            WHERE tr.product_id = ? AND tt.taxonomy = 'product_category' 
        ";

        $result = $this->database->query($query, [$product_id])->fetchAll();




        return $result;
    }


    public function getProductCategoriesHierarchy($productId)
    {
        // Initialize the result array
        $result = (object) null;
        $result->country = null;
        $result->region = null;
        $result->winery = null;
        $result->wine_type = null;
        $result->food_types = []; // New array to store food type categories

        // First pass: collect all tags and their taxonomy data
        $allTags = $this->database->table('tags_relationships')->where('product_id', $productId)->fetchAll();
        $tagData = [];
        $taxonomyData = [];

        foreach ($allTags as $tag) {
            $taxonomy = $this->database->table('tags_taxonomy')->where('tag_id', $tag->tag_id)->fetch();
            if (!$taxonomy) continue;

            $tagInfo = $this->database->table('tags')->where('id', $tag->tag_id)->fetch();
            if (!$tagInfo) continue;

            $tagData[$tag->tag_id] = $tagInfo;
            $taxonomyData[$tag->tag_id] = $taxonomy;
        }

        // Second pass: identify top-level categories (wine_type and country)
        foreach ($taxonomyData as $tagId => $taxonomy) {
            // Add food_type taxonomy items to the separate array
            if ($taxonomy->taxonomy == 'food_type') {
                $result->food_types[] = $tagData[$tagId];
                continue;
            }

            if ($taxonomy->parent_id == null) {
                if ($taxonomy->taxonomy == 'product_category') {
                    $result->wine_type = $tagData[$tagId];
                } else if ($taxonomy->taxonomy == 'product_country') {
                    $result->country = $tagData[$tagId];
                }
            }
        }

        // Third pass: identify region and winery based on parent relationships
        $regions = [];
        $wineries = [];

        foreach ($taxonomyData as $tagId => $taxonomy) {
            if ($taxonomy->taxonomy == 'product_country' && $taxonomy->parent_id !== null) {
                // Store all potential regions and wineries
                if ($result->country && $taxonomy->parent_id == $result->country->id) {
                    $regions[$tagId] = $tagData[$tagId];
                } else {
                    // This could be a winery or a region with parent that's not the country
                    $parentTaxonomy = $this->database->table('tags_taxonomy')
                        ->where('tag_id', $taxonomy->parent_id)
                        ->fetch();

                    if ($parentTaxonomy && $result->country) {
                        // If parent's parent is country, this is a winery
                        if ($parentTaxonomy->parent_id == $result->country->id) {
                            $wineries[$tagId] = [
                                'tag' => $tagData[$tagId],
                                'parent_id' => $taxonomy->parent_id
                            ];
                        }
                    }
                }
            }
        }

        // Set region and winery properly
        if (!empty($regions)) {
            // Just take the first region if multiple exist
            $result->region = reset($regions);

            // Match winery to the correct region
            foreach ($wineries as $wineryData) {
                if ($wineryData['parent_id'] == $result->region->id) {
                    $result->winery = $wineryData['tag'];
                    break;
                }
            }
        } elseif (!empty($wineries)) {
            // If we have wineries but no regions, try to get the region from the winery's parent
            $wineryData = reset($wineries);
            $result->winery = $wineryData['tag'];

            // Get the region from the winery's parent
            $regionTag = $this->database->table('tags')->where('id', $wineryData['parent_id'])->fetch();
            if ($regionTag) {
                $result->region = $regionTag;
            }
        }

        return $result;
    }




    public function getCategoryBySlug($slug)
    {
        return $this->database->query("SELECT * FROM tags WHERE slug = ?", ...[$slug])->fetch();
    }



    public function getRecommendedProducts($productId = null)
    {
        if ($productId == null) {
            $query = "
                SELECT * FROM products
                WHERE visibility = 1
                LIMIT 3
            ";

            $products =  $this->database->query($query)->fetchAll();
            return $products;
        } else {

            $tagQuery = "SELECT tag_id FROM tags_relationships WHERE product_id = ?";
            $tags = $this->database->query($tagQuery, $productId)->fetchPairs(null, 'tag_id');

            if (!empty($tags)) {
                $productQuery = "
                    SELECT tr.product_id, COUNT(*) AS shared_tag_count
                    FROM tags_relationships tr
                    JOIN products p ON p.id = tr.product_id
                    WHERE tr.tag_id IN (?)
                    AND tr.product_id != ?
                    AND p.visibility = 1
                    GROUP BY tr.product_id
                    ORDER BY shared_tag_count DESC
                    LIMIT 12
                ";
                $recommendedProducts = $this->database->query($productQuery, array_values($tags), $productId)->fetchAll();

                if (!empty($recommendedProducts)) {
                    $productIds = array_map(function ($item) {
                        return $item->product_id;
                    }, $recommendedProducts);

                    $detailsQuery = "
                    SELECT p.id, p.title_cs AS title, p.subtitle, p.slug AS slug, p.img_src, category_info.tag_id AS subcategory_id, tags_subcategory.slug_cs AS subcategory_slug,  category_tags.tag_id AS category_id
                    FROM products p
                    LEFT JOIN ( SELECT 
                        tr.product_id, 
                        tt.tag_id
                    FROM 
                        tags_relationships tr
                    JOIN 
                        tags_taxonomy tt ON tt.tag_id = tr.tag_id
                    WHERE tt.taxonomy = 'product_subcategory')

                    AS category_info ON category_info.product_id = p.id
                    LEFT JOIN tags tags_subcategory ON category_info.tag_id = tags_subcategory.id
                    LEFT JOIN ( SELECT tr.product_id, tt.tag_id
                    FROM tags_relationships tr
                    JOIN tags_taxonomy tt ON tt.tag_id = tr.tag_id
                    WHERE tt.taxonomy = 'product_category') 
                    AS category_tags ON category_tags.product_id = p.id
                    WHERE p.id IN (?)
                    AND p.visibility = 1
                    ";

                    $productDetails = $this->database->query($detailsQuery, $productIds)->fetchAll();

                    return $productDetails;
                }
            }
        }

        return [];
    }


    public function getTableSelection($table = "products")
    {
        return $this->database->table($table)->select('*');
    }




    public function getProductById(int $id)
    {
        $product = $this->database->table('products')->get($id);

        return $product;
    }



    public function getProductBySlug($slug)
    {
        $productQuery = "SELECT * FROM products WHERE slug = ? AND visibility = 1";
        $product = $this->database->query($productQuery, $slug)->fetch();

        if (!$product) {
            return null;
        }

        $attributesQuery = "SELECT * FROM product_attributes WHERE product_id = ?";
        $attributes = $this->database->query($attributesQuery, $product->id);

        return (object)["product" => $product, "attributes" => $attributes];
    }



    //ORIGINALNI FUNC!!!!
    // public function getProducts($category, $page, $cartId,  $searchText = null)
    // {
    //     bdump($category);
    //     $response = (object) null;
    //     $response->table = "products";
    //     $response->search = $searchText;
    //     $response->page = $page;
    //     $response->limit = 1000;
    //     $response->offset = ($response->page - 1) * $response->limit;
    //     $response->items = null;
    //     $response->countItems = 0;

    //     $joinClause = " LEFT JOIN tags_relationships tr ON products.id = tr.product_id
    //                     LEFT JOIN tags_taxonomy tt ON tr.tag_id = tt.id
    //                     LEFT JOIN cart_items ci ON ci.product_id = products.id AND ci.cart_id = ?" 
    //                     ;
    //     $whereConditions = [];
    //     $queryParams = [$cartId];

    //     // if ($category !== null) {
    //     //     $taxonomyType = 'product_category';
    //     //     $whereConditions[] = 'tt.taxonomy = ? AND tt.tag_id = ? AND tt.parent_id != ?';
    //     //     array_push($queryParams, $taxonomyType, $category->id, null);
    //     // }

    //     if ($response->search) {
    //         $response->searchTerm = '%' . $response->search . '%';
    //         $whereConditions[] = '(products.title_cs LIKE ? OR products.supplier LIKE ?)';
    //         array_push($queryParams, $response->searchTerm, $response->searchTerm);
    //     }

    //     $whereClause = count($whereConditions) ? ' WHERE ' . implode(' AND ', $whereConditions) : '';
    //     $groupByClause = " GROUP BY products.id ";
    //     $selectQuery = "SELECT products.*, tt.taxonomy, tt.tag_id, ci.quantity  FROM products {$joinClause} {$whereClause} {$groupByClause} LIMIT ? OFFSET ?";
    //     $countQuery = "SELECT COUNT(DISTINCT products.id) FROM products {$joinClause} {$whereClause}";

    //     $queryParamsForSelect = array_merge($queryParams, [$response->limit, $response->offset]);

    //     $response->items = $this->database->query($selectQuery, ...$queryParamsForSelect)->fetchAll();
    //     $response->countItems = $this->database->query($countQuery, ...$queryParams)->fetchField();

    //     $response->pages = ceil($response->countItems / $response->limit);
    //     $response->currentCount = $response->offset + count($response->items);
    //     $response->endOfList = $response->currentCount >= $response->countItems;

    //     return $response;
    // }

    public function getProducts($category, $page, $cartId, $searchText = null)
    {
        bdump($category);
        $response = (object) null;
        $response->table = "products";
        $response->search = $searchText;
        $response->page = $page;
        $response->limit = 1000;
        $response->offset = ($response->page - 1) * $response->limit;
        $response->items = null;
        $response->countItems = 0;

        $joinClause = " LEFT JOIN tags_relationships tr ON products.id = tr.product_id
                            LEFT JOIN tags t ON tr.tag_id = t.id
                            LEFT JOIN cart_items ci ON ci.product_id = products.id AND ci.cart_id = ?";
        $whereConditions = [];
        $queryParams = [$cartId];

        // Only show products with visibility = 1
        $whereConditions[] = 'products.visibility = 1';

        // Add category filter based on slug if category is provided
        if ($category !== null) {
            $whereConditions[] = 't.slug = ?';
            array_push($queryParams, $category);
        }

        if ($response->search) {
            $response->searchTerm = '%' . $response->search . '%';
            $whereConditions[] = '(products.title_cs LIKE ? OR products.supplier LIKE ?)';
            array_push($queryParams, $response->searchTerm, $response->searchTerm);
        }

        $whereClause = count($whereConditions) ? ' WHERE ' . implode(' AND ', $whereConditions) : '';
        $groupByClause = " GROUP BY products.id ";
        $selectQuery = "SELECT products.*, t.slug as category_slug, ci.quantity FROM products {$joinClause} {$whereClause} {$groupByClause} LIMIT ? OFFSET ?";
        $countQuery = "SELECT COUNT(DISTINCT products.id) FROM products {$joinClause} {$whereClause}";

        $queryParamsForSelect = array_merge($queryParams, [$response->limit, $response->offset]);

        $response->items = $this->database->query($selectQuery, ...$queryParamsForSelect)->fetchAll();
        $response->countItems = $this->database->query($countQuery, ...$queryParams)->fetchField();

        $response->pages = ceil($response->countItems / $response->limit);
        $response->currentCount = $response->offset + count($response->items);
        $response->endOfList = $response->currentCount >= $response->countItems;

        return $response;
    }
}
