<?php

namespace App\Controller;



use App\Entity\Purchase;
use App\Entity\User;
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
        $today = date('Y-m-d');
        $firstType = 'Loisir';
        $secondeType = 'Salaire';


        $getPurchase = $doctrine->getRepository(Purchase::class);

        $getFirstValue = $getPurchase->findSumOfTypeOnMonthById($dateStart, $dateEnd, $firstType, $user);
        $getSecondValue = $getPurchase->findSumOfSecondTypebyid($secondeType, $user);
        $totalValue = $getPurchase->findSumOfAll($user);
        $notifiOTD = $getPurchase->findSOTD($user, $today);

        return $this->render('home/user.html.twig', [
            'users' => $user,
            'data' => $callWeatherApiService->getWheatherData(),
            'purchases' => $purchaseRepository->findBy(array("who_id" => $user ),array("date" => "DESC")),
            'together_purchases' => $purchaseRepository->findBy(array(),array("date" => "DESC")),
            'SumOfFirstType' => $getFirstValue,
            'SumOfSecondType' => $getSecondValue,
            'TotalUserValue' => $totalValue,
            'Today' => $today,
            'Notif' => $notifiOTD,
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
            $id = $_POST["Id"];
            $quoi = $_POST["Quoi"];
            $combien = $_POST["Combien"];
            $quand = $_POST["Quand"];
            $buytype = $_POST["BuyType"];

            $combien = str_replace(",", ".", $combien);

            $transaction = new Purchase();
            $transaction->setWho($qui);
            $transaction->setWhat($quoi);
            $transaction->setWhoid($id);
            $transaction->setHow($combien);
            $transaction->setDate($quand);
            $transaction->setType($buytype);



            $purchaseRepository->add($transaction, true);


        }
        return $this->redirectToRoute('user', ['id' => $id]);

    }

    #[Route('/user/editmaxspend/{id}', name: 'user_editmaxspend')]
    public function MaxSpendEdit(ManagerRegistry $doctrine, int $id):Response
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(User::class)->find($id);
        $maxspend = $_POST["maxspend"];
        $maxspend = str_replace(",", ".", $maxspend);



        if (!$maxspend) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $product->setMaxspend($maxspend);
        $entityManager->flush();

        return $this->redirectToRoute('user', [
            'id' => $product->getId()
        ]);
    }

}
