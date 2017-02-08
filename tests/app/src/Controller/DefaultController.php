<?php
namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('src/Resources/views/index.html.twig', [
            'invoices' => $this->get('repository.invoice')->findAll()
        ]);
    }
}