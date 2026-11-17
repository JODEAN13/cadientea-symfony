<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('orderNumber', 'Order #')->hideOnForm(),
            TextField::new('user.email', 'Customer Email')->hideOnForm(),
            
            // FIX: Use TextField to display the exact decimal amount correctly
            TextField::new('totalAmount', 'Total Amount')
                ->formatValue(function ($value) {
                    return '₱' . number_format((float) $value, 2);
                })
                ->hideOnForm(),
            
            // FIX: Custom badge for status
            ChoiceField::new('orderStatus', 'Order Status')
                ->setTemplatePath('admin/field/status_badge.html.twig')
                ->setChoices([
                    'Pending' => 'pending',
                    'Confirmed' => 'confirmed',
                    'Preparing' => 'preparing',
                    'Ready' => 'ready',
                    'Out for Delivery' => 'out_for_delivery',
                    'Completed' => 'completed',
                    'Cancelled' => 'cancelled',
                ]),
            
            ChoiceField::new('paymentStatus', 'Payment Status')->setChoices([
                'Pending' => 'pending',
                'Paid' => 'paid',
                'Failed' => 'failed',
            ]),
            
            TextField::new('paymentMethod', 'Payment Method')->hideOnForm(),
            DateTimeField::new('createdAt', 'Date Placed')->hideOnForm(),
            
            // FIX: Prevents the 30-second timeout when clicking Edit
            CollectionField::new('orderItems', 'Order Items')->hideOnForm(),
        ];
    }
}