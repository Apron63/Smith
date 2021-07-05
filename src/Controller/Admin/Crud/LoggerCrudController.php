<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Logger;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class LoggerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Logger::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            DateTimeField::new('moment', 'Запущено'),
            TextField::new('method', 'Метод'),
            UrlField::new('url', 'Урл'),
            IntegerField::new('responseCode', 'Код ответа'),
        ];
    }
}
