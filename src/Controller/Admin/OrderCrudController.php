<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('orderNumber', 'Order #')->hideOnForm(),
            TextField::new('user.email', 'Customer Email')->hideOnForm(),
        ];

        if ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('orderNumber', 'Order Items')
                ->formatValue(function ($value, Order $order) {
                    $items = $order->getOrderItems();
                    if ($items->isEmpty()) {
                        return 'No items';
                    }

                    $lines = [];
                    foreach ($items as $item) {
                        $lines[] = htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8');
                    }

                    return implode('<br>', $lines);
                })
                ->renderAsHtml();
        }

        if ($pageName === Crud::PAGE_DETAIL) {
            $fields[] = CollectionField::new('orderItems', 'Order Items');
        }

        $fields[] = TextField::new('totalAmount', 'Total Amount')
            ->formatValue(function ($value) {
                return '₱' . number_format((float) $value, 2);
            })
            ->hideOnForm();

        $fields[] = ChoiceField::new('orderStatus', 'Order Status')
            ->setTemplatePath('admin/field/status_badge.html.twig')
            ->setChoices([
                'Pending' => 'pending',
                'Confirmed' => 'confirmed',
                'Preparing' => 'preparing',
                'Ready' => 'ready',
                'Out for Delivery' => 'out_for_delivery',
                'Completed' => 'completed',
                'Cancelled' => 'cancelled',
            ]);

        $fields[] = ChoiceField::new('paymentStatus', 'Payment Status')->setChoices([
            'Pending' => 'pending',
            'Paid' => 'paid',
            'Failed' => 'failed',
        ]);

        $fields[] = TextField::new('paymentMethod', 'Payment Method')->hideOnForm();
        $fields[] = DateTimeField::new('createdAt', 'Date Placed')->hideOnForm();

        return $fields;
    }
}
