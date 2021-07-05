<?php

namespace App\Controller\Admin\Crud;

use App\Entity\News;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class NewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return News::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('guid', 'GUID'),
            TextField::new('title', 'Название'),
            UrlField::new('link', 'Ссылка'),
            TextareaField::new('description', 'Описание')->hideOnIndex(),
            TextField::new('author', 'Автор'),
            DateTimeField::new('pubDate', 'Опубликовано'),
            CollectionField::new('media', 'Изображения')->hideOnIndex(),
        ];
    }
}
