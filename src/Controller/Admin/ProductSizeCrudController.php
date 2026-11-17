<?php

namespace App\Controller\Admin;

use App\Entity\ProductSize;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductSizeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductSize::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('product', 'Product'),
            TextField::new('size', 'Size (e.g. Regular, Large)'),
            MoneyField::new('price', 'Price')->setCurrency('PHP'),
        ];
    }
}