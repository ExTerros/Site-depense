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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('home/index.html.twig', ['users' => $users]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/user/{id}', name: 'user', requirements: ['id' => '\d+'])]
    public function show(string $id,ManagerRegistry $doctrine, UserRepository $userRepository, CallWeatherApiService $callWeatherApiService, PurchaseRepository $purchaseRepository):Response
    {
        $purchaseRepository->findAll();

        $user = $userRepository->find($id);

        $dateStart = date('Y-m-01');
        $dateEnd = date('Y-m-t');
        $firstType = 'Loisir';
        $secondeType = 'Salaire';


        //TODO : Verifier Salaire quand user avec id 2 avec et id 1 sans, UPDATE Faire en sorte que le array existe dans tout les cas si il es vide



        $getPurchase = $doctrine->getRepository(Purchase::class);
        $getFirstValue = $getPurchase->findSumOfTypeOnMonthById($dateStart, $dateEnd, $firstType, $user)[0];
        $getSecondValue = $getPurchase->findSumOfSecondTypebyid($secondeType, $user)[0];

        $totalValue = $getPurchase->findSumOfAll($user)[0];




        return $this->render('home/user.html.twig', [
            'users' => $user,
            'data' => $callWeatherApiService->getWheatherData(),
            'purchases' => $purchaseRepository->findBy(array("who_id" => $user ),array("date" => "DESC")),
            'together_purchases' => $purchaseRepository->findBy(array(),array("date" => "DESC")),
            'SumOfFirstType' => $getFirstValue,
            'SumOfSecondType' => $getSecondValue,
            'TotalUserValue' => $totalValue,
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
