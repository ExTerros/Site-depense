<?php

namespace App\Controller;



use App\Repository\UserRepository;
use App\Service\CallWeatherApiService;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[NoReturn] #[Route('/user/{id}', name: 'user')]
    public function show(string $id,UserRepository $userRepository, CallWeatherApiService $callWeatherApiService):Response
    {

        $user = $userRepository->find($id);
        return $this->render('home/user.html.twig', [
            'users' => $user,
            'data' => $callWeatherApiService->getWheatherData(),]);


    }


}
