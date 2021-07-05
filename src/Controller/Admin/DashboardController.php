<?php

namespace App\Controller\Admin;

use App\Entity\Logger;
use App\Entity\News;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     * Override admin homepage
     */
    public function index(): Response
    {
        return $this->render('admin/homepage.html.twig', [
            'dashboard_controller_filepath' => (new \ReflectionClass(static::class))->getFileName(),
            'dashboard_controller_class' => (new \ReflectionClass(static::class))->getShortName(),
        ]);
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Rss Reader');
    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToCrud('Лента', 'fa fa-tags', News::class),
            MenuItem::linkToCrud('Протокол', 'fa fa-tags', Logger::class),
        ];
    }
}
