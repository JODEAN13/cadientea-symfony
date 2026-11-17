<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Product Name'),
            AssociationField::new('category', 'Category'),
            TextField::new('tag', 'Tag (e.g. BESTSELLER)'),
            TextareaField::new('description', 'Description')->hideOnIndex(),
            ImageField::new('image', 'Image')->setBasePath('images/')->setUploadDir('public/images/')->setRequired(false),
            IntegerField::new('stock', 'Stock Quantity'),
            IntegerField::new('maxPerOrder', 'Max Per Order'),
            // Show who created it (staff/admin)
            AssociationField::new('createdBy', 'Created By')->hideOnForm(), 
        ];
    }
}