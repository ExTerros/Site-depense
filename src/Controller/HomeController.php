<?php

namespace App\Controller;



use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use App\Service\CallWeatherApiService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('home/index.html.twig', ['users' => $users]);
    }

    #[Route('/user/{id}', name: 'user', requirements: ['id' => '\d+'])]
    public function show(string $id,ManagerRegistry $doctrine, UserRepository $userRepository, CallWeatherApiService $callWeatherApiService, PurchaseRepository $purchaseRepository,):Response
    {
        $purchaseRepository->findAll();

        $user = $userRepository->find($id);

        $firstType = 'Loisir';
        $secondType = 'Salaire';


        $getPurchase = $doctrine->getRepository(Purchase::class);
        $getFirstValue = $getPurchase->findSumOfFirstTypebyid($firstType);
        $getSecondValue = $getPurchase->findSumOfSecondTypebyid($secondType);

        $totalValue = $getPurchase->findSumOfAll();

        if($id == '1'){
            $getFirstUserValue = $getFirstValue[0] ?? null;
            $getSecondUserValue = $getSecondValue[0] ?? null;
            $getTotalUserValue = $totalValue[0] ?? null;
        }else{
            $getFirstUserValue = $getFirstValue[1] ?? null;
            $getSecondUserValue = $getSecondValue[1] ?? null;
            $getTotalUserValue = $totalValue[1] ?? null;
        }

        if ($getFirstUserValue == NULL){
            $getFirstUserValue = array(
                "loisir" => 0.0,
                "qui" => 0.0,);
        }

        if ($getSecondUserValue == NULL){
            $getSecondUserValue = array(
                "salaire" => 0.0,
                "qui" => 0.0,);
        }

        if ($getTotalUserValue == NULL){
            $getTotalUserValue = array(
                "total" => 0.0,
                "qui" => 0.0,);
        }



        return $this->render('home/user.html.twig', [
            'users' => $user,
            'data' => $callWeatherApiService->getWheatherData(),
            'purchases' => $purchaseRepository->findBy(array("who_id" => $user ),array("id" => "DESC")),
            'together_purchases' => $purchaseRepository->findBy(array(),array("id" => "DESC")),
            'SumOfFirstType' => $getFirstUserValue,
            'SumOfSecondType' => $getSecondUserValue,
            'TotalUserValue' => $getTotalUserValue,
            ]);


    }


    /**
     * @throws Exception
     */
    #[Route('/user/transaction', name: 'user_transaction')]
    public function newTransaction(PurchaseRepository $purchaseRepository, Request $request):Response
    {

        dump($_POST);

        if ($request->isMethod("POST")){
            $qui = $_POST["Qui"];
            $iden = $_POST["Id"];
            $quoi = $_POST["Quoi"];
            $combien = $_POST["Combien"];
            $quand = $_POST["Quand"];
            $buytype = $_POST["BuyType"];

            $combien = str_replace(",", ".", $combien);

            $transaction = new Purchase();
            $transaction->setWho($qui);
            $transaction->setWhat($quoi);
            $transaction->setWhoid($iden);
            $transaction->setHow($combien);
            $transaction->setDate($quand);
            $transaction->setType($buytype);



            $purchaseRepository->add($transaction, true);


        }
        return $this->redirectToRoute('user', ['id' => $iden]);

    }

}
