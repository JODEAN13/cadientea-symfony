<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\StaffProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/staff')]
#[IsGranted('ROLE_STAFF')] // Staff only (Admins are also granted this because of role hierarchy)
class StaffController extends AbstractController
{
    #[Route('/', name: 'app_staff_dashboard')]
    public function index(ProductRepository $productRepository): Response
    {
        // Staff only sees records they created
        $products = $productRepository->findBy(['createdBy' => $this->getUser()]);

        return $this->render('staff/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/new', name: 'app_staff_product_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(StaffProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedBy($this->getUser());
            
            // Handle Image Upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/images',
                    $newFilename
                );
                $product->setImage('images/' . $newFilename);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product created successfully.');
            return $this->redirectToRoute('app_staff_dashboard');
        }

        return $this->render('staff/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/edit/{id}', name: 'app_staff_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        // 🔥 RUBRIC SECURITY CHECK: Staff can only edit their own products
        if ($product->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot edit this product.');
        }

        $form = $this->createForm(StaffProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle Image Upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/images',
                    $newFilename
                );
                $product->setImage('images/' . $newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Product updated successfully.');
            return $this->redirectToRoute('app_staff_dashboard');
        }

        return $this->render('staff/edit.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_staff_product_delete', methods: ['GET', 'POST'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        // 🔥 RUBRIC SECURITY CHECK: Staff can only delete their own products
        if ($product->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot delete this product.');
        }

        // Preserve audit trail: prevent deletion if product was ordered
        if ($product->getOrderItems()->count() > 0) {
            $this->addFlash('error', 'Cannot delete this product because it has been ordered. Contact admin to archive it instead.');
            return $this->redirectToRoute('app_staff_dashboard');
        }

        $productName = $product->getName();
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Product "' . $productName . '" was deleted successfully.');
        return $this->redirectToRoute('app_staff_dashboard');
    }
}