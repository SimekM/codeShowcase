<?php

declare(strict_types=1);

namespace App\Core\Models;

use Nette;



class Cart
{
    public      $database;
    public      $context;

    public function __construct(Nette\Database\Explorer $database, Nette\Database\Context $context){
        $this->database = $database;
        $this->context = $context;
    }


    public function getAllCarts()
    {
        $query = "
            SELECT 
                c.id, 
                c.session_id, 
                c.created_at,
                c.updated_at
            FROM carts AS c
            WHERE c.created_at < (NOW() - INTERVAL 1 HOUR)
        ";
        $allCarts = $this->database->fetchAll($query);

        return $allCarts;
    }

    public function getOrCreateCartBySessionId($sessionId)
    {
        $isUserNew = true;
        $cartId = null;

        $cart = $this->database->table('carts')->where('session_id', $sessionId)->fetch();
        if (!$cart) {
            $cartId = $this->database->table('carts')->insert([
                'session_id' => $sessionId
            ])->getPrimary();
        }else{
            $cartId = $cart->id;
            $isUserNew = false;
        }

        return ['isUserNew' => $isUserNew,
            'id' => $cartId];
    }





    public function getCartItems($cartId)
    {
        $query = "
            SELECT 
                c.id, 
                c.product_id, 
                c.quantity, 
                p.title, 
                p.img_src,
                p.price
            FROM cart_items AS c
            JOIN products AS p ON c.product_id = p.id
            WHERE c.cart_id = ?
        ";
    
        $cartItems = $this->database->fetchAll($query, $cartId);
        return $cartItems;
    }

    // public function getCartTotalPrice($cartId)
    // {
    //     $cartItems = $this->getCartItems($cartId);
    //     $totalPrice = 0;

    //     foreach ($cartItems as $item) {
    //         $totalPrice += $item->price * $item->quantity;
    //     }

    //     return $totalPrice;
    // }



    public function deleteAbandonedCarts()
    {
        $carts = $this->getAllCarts();
        foreach ($carts as $cart) {
            $cart_items = $this->getCartItems($cart->id);
            if($cart_items)
            {
            }
            else
            {
                $this->database->table('carts')
                ->where('id', $cart->id)
                ->delete()
                ; 
            }
        }
    }

    
    public function addToCart($cartId, $productId, $quantity)
    {

        $existingItem = $this->database->table('cart_items')
            ->where('cart_id' , $cartId)
            ->where('product_id' , $productId)
            ->fetch();

        if ($existingItem) {
            $this->database->table('cart_items')
                ->where('id', $existingItem->id)
                ->update(['quantity' => $existingItem->quantity + $quantity]);
        } else {
            $this->database->table('cart_items')->insert([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    public function deleteCartItem($cartId, $productId)
    {
        $this->database->table('cart_items')
            ->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->delete()
        ;
    }
    public function deleteAllCartItems($cartId)
    {
       $this->database->table('cart_items')
            ->where('cart_id', $cartId)
            ->delete()
        ;
    }

    public function changeQuantity($id, $quantity)
    {
        $this->database->table('cart_items')
            ->where('id', $id)
            ->update(['quantity' =>$quantity]);
    }

    
    public function getOrderCartItems($cartId)
    {
        return $this->database->table('cart_items')
            ->where('cart_id', $cartId)
            ->fetchAll();
    }
    
    /**
     * Calculate cart total
     * 
     * @param int $cartId
     * @return float
     */
    public function getCartTotal($cartId)
    {
        $total = 0;
        $items = $this->getOrderCartItems($cartId);
        
        foreach ($items as $item) {
            // Assuming you have a products table with prices
            $product = $this->database->table('products')->get($item->product_id);
            if ($product) {
                $total += $product->price * $item->quantity;
            }
        }
        
        return $total;
    }
    
    /**
     * Clear all items from cart after order is created
     * 
     * @param int $cartId
     * @return bool
     */
    public function clearCartItems($cartId)
    {
        return $this->database->table('cart_items')
            ->where('cart_id', $cartId)
            ->delete();
    }



    public function createOrder($cartId, $cartTotal, $cartItems, $values)
    {
        $this->database->beginTransaction();

        $values = (array)$values;
        try {

            // $order = $this->database->table('orders')->insert([
            //     'cart_id' => $cartId,
            //     'created_at' => new \DateTime(),
            //     'total_price' => $cartTotal,
            //     'status' => 'pending',
            // ]);
            // bdump($order);

            // $orderId = $order->id;

             $insertSql = "INSERT INTO orders (cart_id, created_at, total_price, status) 
              VALUES (?, ?, ?, ?)";
              

            $order = $this->database->query($insertSql, 
                $cartId,
                (new \DateTime())->format('Y-m-d H:i:s'), // Format DateTime for SQL
                $cartTotal,
                'pending'
            );

            $orderId = $this->database->getInsertId();
            
            // Insert order items
            foreach ($cartItems as $item) {
                $this->database->table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price ?? 0, // Assuming you have price in cart_items or fetch it from products
                ]);
            }
            
            // Insert customer information
            $this->database->table('order_customer_info')->insert([
                'order_id' => $orderId,
                'name' => $values['input-name'],
                'phone' => $values['input-phone'],
                'email' => $values['input-email'],
                'city' => $values['input-city'],
                'street' => $values['input-street'],
                'postal_code' => $values['input-psc'],
                'payment_method' => $values['zpusob-platby'],
                'shipping_method' => $values['zpusob-dopravy'],
                'note' => $values['input-poznamka'],
            ]);
            
            $this->database->commit();
            
            return $orderId;
            
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
    }
    
    /**
     * Get order by ID
     * 
     * @param int $orderId
     * @return array|null
     */
    public function getOrderById($orderId)
    {
        return $this->database->table('orders')
            ->where('id', $orderId)->fetch();
    }
    
    public function getOrderItems($orderId) {
        $query = $this->database->query("
            SELECT oi.quantity, oi.price, p.title, p.id AS product_id, p.img_src
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = '" . (int)$orderId . "'
        ")->fetchAll();
        
        return $query;
    }
    
   
    public function getOrderCustomerInfo($orderId)
    {
        return $this->database->table('order_customer_info')
        ->where('order_id', $orderId)
        ->fetch();
    }
    
    /**
     * Get all orders for a user (if you implement user authentication)
     * 
     * @param int $userId
     * @return \Nette\Database\Table\Selection
     */
    public function getUserOrders($userId)
    {
        return $this->database->table('orders')
            ->where('user_id', $userId)
            ->order('created_at DESC');
    }



}
