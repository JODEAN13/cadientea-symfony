<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $session->get('cart', []),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, ProductRepository $productRepository, SessionInterface $session, Request $request): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $sizeName = $request->request->get('size');
        $quantity = $request->request->getInt('quantity', 1);

        // Find the matching size on the product
        $size = null;
        foreach ($product->getProductSizes() as $s) {
            if ($s->getSize() === $sizeName) {
                $size = $s;
                break;
            }
        }

        if (!$size) {
            $size = $product->getProductSizes()->first();
        }
        if (!$size) {
            $this->addFlash('error', 'This product has no available size.');
            return $this->redirectToRoute('app_menu');
        }

        $maxPerOrder = $product->getMaxPerOrder() ?? 5;
        if ($quantity > $maxPerOrder) {
            $quantity = $maxPerOrder;
        }

        $cart = $session->get('cart', []);
        $key = $id . '_' . $size->getSize();

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
            if ($cart[$key]['quantity'] > $maxPerOrder) {
                $cart[$key]['quantity'] = $maxPerOrder;
            }
        } else {
            // 🔥 Store only the ID and string values, NOT the object!
            $cart[$key] = [
                'product_id' => $product->getId(),
                'name'       => $product->getName(),
                'size'       => $size->getSize(),
                'price'      => $size->getPrice(),
                'quantity'   => $quantity,
            ];
        }

        $session->set('cart', $cart);
        $this->addFlash('success', 'Added to cart!');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{key}', name: 'app_cart_remove')]
    public function remove(string $key, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        unset($cart[$key]);
        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
}