<?php

namespace App\Core\Models;

use Nette;


final class Shop 
{



    public function __construct
        (
          private Nette\Database\Explorer       $database,
          private Nette\Database\Context        $context
        )
    {
    
        
    }






    public function getCategories() 
    {
        $categories = $this->database->table('tags_taxonomy')
                                     ->where('taxonomy', 'product_category')
                                     ->fetchAll();
    
        $result = [];
    
        foreach ($categories as $category) {

            $categoryDetails = $this->database->table('tags')
                                              ->where('id', $category->tag_id)
                                              ->fetch();
    
            $subcategories = $this->database->table('tags_taxonomy')
                                            ->where('parent_id', $category->id)
                                            ->where('taxonomy', 'product_subcategory')
                                            ->fetchAll();
    
            $subcategoriesObect = [];
            foreach ($subcategories as $subcategory) {
                $subCategoryDetails = $this->database->table('tags')
                                                ->where('id', $subcategory->tag_id)
                                                ->fetch();
                $subcategoriesObect[] = (object) [
                    'id' => $subCategoryDetails->id,
                    'title' => $subCategoryDetails->title_cs,
                    'slug' => $subCategoryDetails->slug_cs,
                    'img_src' => $subCategoryDetails->img_src

                ];
            }
    
            $result[] = (object) [
                'categories' => (object) [
                    'id' => $categoryDetails->id,
                    'title' => $categoryDetails->title_cs,
                    'slug' => $categoryDetails->slug_cs,
                    'img_src' => $categoryDetails->img_src

                ],
                'subcategories' => $subcategoriesObect
            ];


            
        }
    
        return $result;
    }

    
    public function getCategoriesFromProductId($product_id) {
        $query = "
            SELECT tt.taxonomy, t.id, t.title_cs AS title, t.slug_cs AS slug
            FROM tags_taxonomy tt
            JOIN tags_relationships tr ON tr.tag_id = tt.id
            JOIN tags t ON t.id = tt.tag_id
            WHERE tr.product_id = ? AND (tt.taxonomy = 'product_category' OR tt.taxonomy = 'product_subcategory')
        ";
    
        $result = $this->database->query($query, [$product_id])->fetchAll();
        
        $categories = (object) null;
    
        foreach ($result as $row) {
            if ($row->taxonomy == 'product_category') {
                $categories->category = (object) [
                    'id' => $row->id,
                    'title' => $row->title,
                    'slug' => $row->slug,
                ];
            } elseif ($row->taxonomy == 'product_subcategory') {
                $categories->subcategory = (object) [
                    'id' => $row->id,
                    'title' => $row->title,
                    'slug' => $row->slug
                ];
            }
        }
    
        return $categories;
    }



    public function getRecommendedProducts($productId = null) 
    {
        if($productId == null){
            $query = "
                SELECT * FROM products
                LIMIT 3
            ";

            $products =  $this->database->query($query)->fetchAll();
            return $products;

        }else{

            $tagQuery = "SELECT tag_id FROM tags_relationships WHERE product_id = ?";
            $tags = $this->database->query($tagQuery, $productId)->fetchPairs(null, 'tag_id');
        
            if (!empty($tags)) {
                $productQuery = "
                    SELECT product_id, COUNT(*) AS shared_tag_count
                    FROM tags_relationships
                    WHERE tag_id IN (?)
                    AND product_id != ?
                    GROUP BY product_id
                    ORDER BY shared_tag_count DESC
                    LIMIT 12
                ";
                $recommendedProducts = $this->database->query($productQuery, array_values($tags), $productId)->fetchAll();

                if (!empty($recommendedProducts)) {
                    $productIds = array_map(function($item) { return $item->product_id; }, $recommendedProducts);
                
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
                    ";

                    $productDetails = $this->database->query($detailsQuery, $productIds)->fetchAll();

                    return $productDetails;
                }
            }
        }
    
        return [];
    }
    
    
    public function getTableSelection($table="products")
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
            // Fetch the base product
            $product = $this->database->query(' 
            SELECT p.*,
                   (SELECT GROUP_CONCAT(description ORDER BY description_order SEPARATOR "\n\n")
                    FROM product_descriptions 
                    WHERE product_id = p.id) AS full_description,
                   (SELECT CONCAT("[", GROUP_CONCAT(
                       CONCAT(
                           "{",
                           "\"attribute_name\": \"", attribute_name, "\",",
                           "\"attribute_value\": \"", attribute_value, "\",",
                           "\"attribute_detail\": \"", attribute_detail, "\",",
                           "\"price_adjustment\": ", price_adjustment,
                           "}"
                       )
                   ), "]")
                   FROM product_variations 
                   WHERE product_id = p.id) AS variations
            FROM products p
            WHERE p.slug = ?
        ', $slug)->fetch();
        
            if ($product) {
                // Parse variations
                if ($product->variations !== null) {
                    $product->variations = json_decode($product->variations, true);                    
                }
                
                // Set full description
                $product->description_cs = $product->full_description;
            }
        
            return $product;
        }
        
        
        
        //ORIGINALNI FUNC!!!!
        public function getProducts($category, $page, $cartId,  $searchText = null)
        {
            $response = (object) null;
            $response->table = "products";
            $response->search = $searchText;
            $response->page = $page;
            $response->limit = 100;
            $response->offset = ($response->page - 1) * $response->limit;
            $response->items = null;
            $response->countItems = 0;
        
            $joinClause = " LEFT JOIN tags_relationships tr ON products.id = tr.product_id
                            LEFT JOIN tags_taxonomy tt ON tr.tag_id = tt.id
                            LEFT JOIN cart_items ci ON ci.product_id = products.id AND ci.cart_id = ?" 
                            ;
            $whereConditions = [];
            $queryParams = [$cartId];

            if ($category !== null) {
                $taxonomyType = $category->type === 'categories' ? 'product_category' : 'product_subcategory';
                $whereConditions[] = 'tt.taxonomy = ? AND tt.tag_id = ?';
                array_push($queryParams, $taxonomyType, $category->categoryData->id);
            }
        
            if ($response->search) {
                $response->searchTerm = '%' . $response->search . '%';
                $whereConditions[] = '(products.title_cs LIKE ? OR products.supplier LIKE ?)';
                array_push($queryParams, $response->searchTerm, $response->searchTerm);
            }
        
            $whereClause = count($whereConditions) ? ' WHERE ' . implode(' AND ', $whereConditions) : '';
            $groupByClause = " GROUP BY products.id ";
            $selectQuery = "SELECT products.*, tt.taxonomy, tt.tag_id, ci.quantity  FROM products {$joinClause} {$whereClause} {$groupByClause} LIMIT ? OFFSET ?";
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