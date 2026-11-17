<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')] 
    public function checkout(SessionInterface $session, EntityManagerInterface $entityManager, Request $request): Response
    {
        $cart = $session->get('cart', []);
        if (empty($cart)) {
            return $this->redirectToRoute('app_menu');
        }

        if ($request->isMethod('POST')) {
            $orderType = $request->request->get('order_type', 'pickup');
            $paymentMethod = $request->request->get('payment_method', 'cash');
            
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }
            $deliveryFee = ($orderType === 'delivery') ? 50.00 : 0.00;
            $total = $subtotal + $deliveryFee;

            $order = new Order();
            $order->setUser($this->getUser());
            
            $orderNumber = 'CT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
            $order->setOrderNumber($orderNumber);
            
            $order->setSubtotal(number_format($subtotal, 2, '.', ''));
            $order->setDeliveryFee(number_format($deliveryFee, 2, '.', ''));
            $order->setTotalAmount(number_format($total, 2, '.', ''));
            $order->setPaymentMethod($paymentMethod);
            $order->setPaymentStatus('pending');
            $order->setOrderStatus('pending');
            $order->setOrderType($orderType);
            
            $address = $orderType === 'pickup' 
                ? 'PICKUP - CadienTea Main Branch, Dumaguete City, Negros Oriental' 
                : $request->request->get('address', 'Dumaguete City');
            $order->setOrderAddress($address);
            $order->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($order);

            foreach ($cart as $item) {
                // Re-fetch product from DB to avoid detached entity errors
                $product = $entityManager->getRepository(Product::class)->find($item['product_id']);
                if (!$product) continue;

                // Find Size
                $sizeEntity = null;
                foreach ($product->getProductSizes() as $size) {
                    if ($size->getSize() === $item['size']) {
                        $sizeEntity = $size;
                        break;
                    }
                }
                if (!$sizeEntity) {
                    $sizeEntity = $product->getProductSizes()->first();
                }

                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setProduct($product);
                $orderItem->setProductName($product->getName());
                $orderItem->setSize($sizeEntity->getSize());
                $orderItem->setQuantity($item['quantity']);
                $orderItem->setUnitPrice(number_format($sizeEntity->getPrice(), 2, '.', ''));
                $orderItem->setSubtotal(number_format($sizeEntity->getPrice() * $item['quantity'], 2, '.', ''));
                $entityManager->persist($orderItem);

                // Decrease stock
                $newStock = $product->getStock() - $item['quantity'];
                $product->setStock(max(0, $newStock));
                $entityManager->persist($product);
            }

            $entityManager->flush();
            $session->remove('cart');

            if ($paymentMethod === 'gcash') {
                return $this->redirectToRoute('app_gcash_payment', ['orderNumber' => $order->getOrderNumber()]);
            }

            $this->addFlash('success', 'Order placed successfully!');
            return $this->redirectToRoute('app_orders');
        }

        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/gcash/{orderNumber}', name: 'app_gcash_payment', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function gcash(string $orderNumber, EntityManagerInterface $entityManager, Request $request): Response
    {
        $order = $entityManager->getRepository(Order::class)->findOneBy(['orderNumber' => $orderNumber]);
        
        if (!$order || $order->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Order not found');
        }

        if ($request->isMethod('POST')) {
            $gcashRef = 'GC' . date('Ymd') . strtoupper(bin2hex(random_bytes(4)));
            $order->setGcashReference($gcashRef);
            $order->setGcashNumber($request->request->get('gcash_number'));
            $order->setPaymentStatus('paid');
            $entityManager->flush();

            $this->addFlash('success', 'GCash Payment Successful! Reference: ' . $gcashRef);
            return $this->redirectToRoute('app_orders');
        }

        return $this->render('checkout/gcash.html.twig', ['order' => $order]);
    }
}