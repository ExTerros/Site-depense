<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditDeleteController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete_payment')]
    public function delete_Payment( Purchase $purchase, PurchaseRepository $purchaseRepository): Response
    {
        $purchaseRepository->remove($purchase);


        return $this->redirectToRoute('user', ['id' => $purchase->getWhoId()]);
    }
}
