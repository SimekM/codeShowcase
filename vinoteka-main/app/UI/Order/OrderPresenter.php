<?php

declare(strict_types=1);

namespace App\UI\Order;

use Nette;
use App\UI\_basePresenter;
use Nette\DI\Attributes\Inject;
use Nette\Mail\Message;

final class OrderPresenter extends _basePresenter
{

    #[Inject]     public \App\Core\ModelCore           $model_core;



    public function renderDefault(): void
    {
        $cartId = $this->loadCartInfo();
        $this->template->cartId = $cartId;
        $cartItems = $this->model_core->model_cart->getCartItems($cartId);
        $this->template->cartItems = $cartItems;
    }

    public function renderAddress(): void
    {
        $cartId = $this->loadCartInfo();
        $this->template->cartId = $cartId;
  
    }



     
    public function renderSummary()
    {
        $session = $this->session->getSection('order');
        if (!isset($session->orderId)) {
            $this->redirect('Home:default');
        }

        $orderId = $session->orderId;

        try {
            $order = $this->model_core->model_cart->getOrderById($orderId);
            $orderItems = $this->model_core->model_cart->getOrderItems($orderId);
            $customerInfo = $this->model_core->model_cart->getOrderCustomerInfo($orderId);
            $deliveryMessage = $this->getSummaryDeliveryMessage($customerInfo);

            if (!$order) {
                $this->flashMessage('Objednávka nebyla nalezena.', 'error');
                $this->redirect('Home:');
            }
            $doplatekZaDopravu = 0;
            if ($customerInfo->payment_method == "checkbox-platba-dobirka"){ $doplatekZaDopravu += 30;  }
            if ($customerInfo->shipping_method == "checkbox-doprava-ppl"){ $doplatekZaDopravu += 130; }
            $this->template->doplatekZaDopravu = $doplatekZaDopravu;
            $this->template->order = $order;
            $this->template->orderItems = $orderItems;
            $this->template->customerInfo = $customerInfo;
            $this->template->deliveryMessage = $deliveryMessage;
            
        } catch (\Exception $e) {
            $this->flashMessage('Došlo k chybě při načítání objednávky: ' . $e->getMessage(), 'error');
            $this->redirect('Home:');
        }
    }





    public function handleCartRequest()
    {
        $data = (object)$_POST;
        $response = [];

        if ($data->action === "removeFromCart") {
            $this->removeFromCart($data->parameters);
        } else if ($data->action === "changeQuantity") {
            $this->changeQuantity($data->parameters);
        }

    }


    public function removeFromCart($data)
    {
        $this->model_core->model_cart->deleteCartItem($data['cartId'], $data['productId']);
        $cartId = $this->loadCartInfo();
        $this->template->cartId = $cartId;
        $cartItems = $this->model_core->model_cart->getCartItems($cartId);
        $this->template->cartItems = $cartItems;
        
        $this->payload->sucess = false;
        $this->payload->message = 'Položka byla úspěšně odstraněna z košíku';
        
        if($this->isAjax()){
            $this->redrawControl();
        }
    }

    public function changeQuantity($request)
    {
        $this->model_core->model_cart->changeQuantity($request['id'], $request['quantity']);
        $this->sendJson((object)null);
    }



    public function handleSendOrder(){
        $data = (object)$_POST;

        $cartId = $this->loadCartInfo();
        $cartItems = $this->model_core->model_cart->getCartItems($cartId);
        $cartTotal = $this->model_core->model_cart->getCartTotal($cartId);

        if (empty($cartItems)) {
            $response['error'] = true;
            $response['message'] = 'Váš košík je prázdný.';
            $this->sendJson($response);
            return;
        }

        // Create order (you have this line twice, keep only one)
        $orderId = $this->model_core->model_cart->createOrder(
            $cartId,
            $cartTotal,
            $cartItems,
            $data
        );
        
        // Add the order ID to the response
        $this->session->getSection('order')->orderId = $orderId;

        $response = $this->sendEmail($data, $cartItems, $cartTotal, $orderId);
        
        if ($response['error'] === false) {
            $this->model_core->model_cart->clearCartItems($cartId);
        }
        
        $this->sendJson($response);
    }



    public function sendEmail($values, $cartItems, $cartTotal, $orderId)
    {
        $values = (array)$values;

        $response = [
            'error' => true,
            'message' => 'Objednávku se nepodařilo objednat zkuste to znovu, nebo nás kontaktujte.',
        ];

        $emailAccountInfo = (object)null;
        $emailAccountInfo->smtpServer   = 'smtp.seznam.cz';
        $emailAccountInfo->userName     = 'simekmatyas@seznam.cz';
        $emailAccountInfo->password     = 'Yourmumgay666';
        $emailAccountInfo->smtpSecure   = 'ssl';

        $mailer = new Nette\Mail\SmtpMailer
        (
            host        : $emailAccountInfo->smtpServer, 	
            username    : $emailAccountInfo->userName ,
            password    : $emailAccountInfo->password,
            encryption  : $emailAccountInfo->smtpSecure, //secure
        );
        $messageForCompany = new Message();
        $messageForCustomer = new Message();

        // ZPUSOB PLATBY TITLE
        $zpusobPlatby = $values['zpusob-platby'];
        $zpusobDopravy = $values['zpusob-dopravy'];
        $poplatkyZaDopravu = 0;
        $platbyTitles = [
            'checkbox-platba-hotove' => 'Hotově při osobním převzetí',
            'checkbox-platba-bakovni-prevod' => 'Bankovním převodem',
            'checkbox-platba-dobirka' => 'Dobírkou'
        ];
        
        $dopravyTitles = [
            'checkbox-doprava-hotove' => 'Osobní odběr na prodejně',
            'checkbox-doprava-ppl' => 'Přeprava službou PPL'
        ];
        
        $zpusobPlatby = isset($platbyTitles[$zpusobPlatby]) ? $platbyTitles[$zpusobPlatby] : 'Nezvoleno';
        $zpusobDopravy = isset($dopravyTitles[$zpusobDopravy]) ? $dopravyTitles[$zpusobDopravy] : 'Nezvoleno';
        
        // POPLATEK ZA DOPRAVU
        
        $productsListMessage = '';
        $customerPaymentMessage = "";
        $poplatkyZaDopravu = 0;
        if ($zpusobDopravy == 'checkbox-platba-bakovni-prevod') {
            $customerPaymentMessage += 'Úhradu proveďte na bankovní účet naší společnosti 123-6516170227/0100. Jako variabilní symbol uveďte číslo objednávky. ';
        }
        if ($zpusobDopravy == 'checkbox-doprava-ppl') {
            $poplatkyZaDopravu += 130; // Poplatek za PPL
            $productsListMessage.= '<br><p>Poplatek za PPL: 130 Kč</p>';
            $customerPaymentMessage += 'Objednané zboží bude doručeno prostřednictvím přepravní služby PPL na určenou adresu.';
        }
        if ($zpusobPlatby == 'checkbox-platba-dobirka') {
            $poplatkyZaDopravu += 30; // Poplatek za dobírku
            $productsListMessage.= '<br><p>Poplatek za Dobírku: 30 Kč</p>';
        }
      
      
        $cartTotal += $poplatkyZaDopravu; 
        
        // PRODUKTY DO ZPRAVY
        foreach ($cartItems as $cartItem) {
            $productsListMessage.= '<br><p>Kód položky: '.$cartItem->product_id.'</p><p>Název produktu: '.$cartItem->title.'</p><p>Počet kusů: '.$cartItem->quantity.'</p><p>Cena za jeden: '.$cartItem->price.'</p><p>Celková cena produktu: '.$cartItem->price * $cartItem->quantity.'</p>';
        }

        $messageForCompany->setFrom($emailAccountInfo->userName)
        ->addTo('simekmatyas@seznam.cz')
        ->setSubject('Objednávka z webu od '.$values['input-name'])
        ->setHtmlBody('
            <html>
                <head></head>
            <body>
                <h3>Číslo objednávky: '.$orderId.'</h3>
                <h3>Platební kontakt</h3>
                <p>Jméno a příjmení: '.$values['input-name'].'</p>
                <p>Telefon: '.$values['input-phone'].'</p>
                <p>Email: '.$values['input-email'].'</p>
                <p>Město: '.$values['input-city'].'</p>
                <p>Ulice: '.$values['input-street'].'</p>
                <p>PSČ: '.$values['input-psc'].'</p>
                <br>
                <h3>Způsob platby a dodání</h3>
                <p>Způsob platby: '.$zpusobPlatby.'</p>
                <p>Způsob dopravy: '.$zpusobDopravy.'</p>
                <br>
                <h3>Poznámka</h3>
                <p>'.$values['input-poznamka'].'</p>
                <br>
                <h3>Objednané produkty</h3>
                '.$productsListMessage.'
                <br>
                <h3>Celková cena objednávky: '.$cartTotal.' Kč</h3>

            </body>
            </html>');


            $messageForCustomer->setFrom($emailAccountInfo->userName)
            ->addTo($values['input-email'])
            ->setSubject('Potvrzení ojednávky Vinotéka mille')
            ->setHtmlBody('
                <html>
                    <head></head>
                <body>
                    <h3>Číslo objednávky: '.$orderId.'</h3>
                    <p>Děkujeme za vaši objednávku. Vaši objednávku s číslem '.$orderId.' jsme přijali a budeme ji co nejdříve vyřizovat. <br> '.$customerPaymentMessage.'</p>
                    <br>
                    <h3>Objednané produkty</h3>
                    '.$productsListMessage.'
                    <br>
                    <h3>Celková cena objednávky: '.$cartTotal.' Kč</h3>
    
                </body>
                </html>');

            	
            	
        	try 
            	{ 
            	    $mailer->send($messageForCompany); 
                    $mailer->send($messageForCustomer); 

            	  	$response['error']	            = false;
            		$response['message']	        = "Objednávku se podařilo odeslat.";
            	} 
        	catch (Nette\Mail\SmtpException $e) 
            	{   
                    bdump($e->getMessage());
            	    $response['error']	            = true;
            		$response['message']	        = "Objednávku se nepodařilo odeslat, zkuste to prosím znovu, nebo nás kontaktujte";	
            	}    
            


            return $response;
        
        
    }





public function getSummaryDeliveryMessage($data) {
    // Extract payment and delivery method classes
    $paymentClass = $data->{'payment_method'};
    $deliveryClass = $data->{'shipping_method'};

    // Define all information for payment methods
    $paymentInfo = [
        'checkbox-platba-hotove' => [
            'title' => 'Hotově při osobním odběru',
            'message' => 'Zboží zaplatí zákazník hotově při osobním převzetí.'
        ],
        'checkbox-platba-bakovni-prevod' => [
            'title' => 'Bankovní převod',
            'message' => 'Úhradu proveďte na bankovní účet naší společnosti 123-6516170227/0100. Jako variabilní symbol uveďte číslo objednávky.'
        ],
        'checkbox-platba-dobirka' => [
            'title' => 'Dobírka',
            'message' => 'Zboží bude zákazníkovi zasláno na dobírku.'
        ]
    ];
    
    // Define all information for delivery methods
    $deliveryInfo = [
        'checkbox-doprava-hotove' => [
            'title' => 'Osobní odběr na prodejně',
            'message' => 'Zákazník si zboží převezme osobně na pobočce Otrokovice.'
        ],
        'checkbox-doprava-ppl' => [
            'title' => 'Doručení přepravní službou PPL',
            'message' => 'Objednané zboží bude doručeno prostřednictvím přepravní služby PPL. Zboží rozesíláme jen v rámci ČR! Poštovné: 130,- Kč.'
        ]
    ];
    
    // Define combined messages for specific combinations
    $combinedMessages = [
        'platba-hotove_doprava-hotove' => 'Zboží zaplatí zákazník hotově při osobním převzetí. Zákazník si zboží převezme osobně na pobočce Otrokovice.',
        'platba-bakovni-prevod_doprava-hotove' => 'Úhradu proveďte na bankovní účet naší společnosti 123-6516170227/0100. Jako variabilní symbol uveďte číslo objednávky. Zákazník si zboží převezme osobně na pobočce Otrokovice.',
        'platba-bakovni-prevod_doprava-ppl' => 'Úhradu proveďte na bankovní účet naší společnosti 123-6516170227/0100. Jako variabilní symbol uveďte číslo objednávky. Objednané zboží bude doručeno prostřednictvím přepravní služby PPL. Zboží rozesíláme jen v rámci ČR! Poštovné: 130,- Kč.',
        'platba-dobirka_doprava-ppl' => 'Zboží bude zákazníkovi zasláno na dobírku. Objednané zboží bude doručeno prostřednictvím přepravní služby PPL. Zboží rozesíláme jen v rámci ČR! Poštovné: 130,- Kč + Dobírka: 30,- Kč.'
    ];
    
    // Get payment and delivery titles
    $paymentTitle = isset($paymentInfo[$paymentClass]) ? $paymentInfo[$paymentClass]['title'] : '';
    $deliveryTitle = isset($deliveryInfo[$deliveryClass]) ? $deliveryInfo[$deliveryClass]['title'] : '';
    
    // Get the combined message if it exists, otherwise combine individual messages
    $combinedKey = $paymentClass . '_' . $deliveryClass;
    if (isset($combinedMessages[$combinedKey])) {
        $message = $combinedMessages[$combinedKey];
    } else {
        $paymentMessage = isset($paymentInfo[$paymentClass]) ? $paymentInfo[$paymentClass]['message'] : '';
        $deliveryMessage = isset($deliveryInfo[$deliveryClass]) ? $deliveryInfo[$deliveryClass]['message'] : '';
        $message = $paymentMessage . ' ' . $deliveryMessage;
    }
    
    return (object)[
        'message' => $message,
        'paymentTitle' => $paymentTitle,
        'deliveryTitle' => $deliveryTitle
    ];
}
}
