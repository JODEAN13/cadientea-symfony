<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    private UserRepository $userRepo;
    private ProductRepository $productRepo;
    private OrderRepository $orderRepo;

    public function __construct(
        UserRepository $userRepo,
        ProductRepository $productRepo,
        OrderRepository $orderRepo
    ) {
        $this->userRepo = $userRepo;
        $this->productRepo = $productRepo;
        $this->orderRepo = $orderRepo;
    }

    public function index(): Response
    {
        $totalUsers = $this->userRepo->count([]);
        $totalStaff = $this->userRepo->count(['role' => 'staff']);
        $totalProducts = $this->productRepo->count([]);
        $totalOrders = $this->orderRepo->count([]);

        $revenue = $this->orderRepo->createQueryBuilder('o')
            ->select('SUM(o.totalAmount)')
            ->where('o.orderStatus = :status')
            ->setParameter('status', 'completed')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalStaff' => $totalStaff,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'revenue' => $revenue,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        // Using setTitle to render the logo image
        return Dashboard::new()
            ->setTitle('<img src="/images/cadienteamainlogo.png" alt="CadienTea" style="max-height: 40px;">')
            ->setFaviconPath('/images/cadienteamainlogo.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('System Management');
        yield MenuItem::linkTo(UserCrudController::class, 'Manage Users & Staff', 'fas fa-users');
        
        yield MenuItem::section('Store Records');
        yield MenuItem::linkTo(ProductCrudController::class, 'Products', 'fas fa-mug-hot');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Categories', 'fas fa-tags');
        yield MenuItem::linkTo(OrderCrudController::class, 'Orders', 'fas fa-receipt');
        yield MenuItem::linkTo(MessageCrudController::class, 'Messages', 'fas fa-envelope');
    }
}