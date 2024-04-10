<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Champion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ChampionController extends AbstractController
{
    #[Route('/champion', name: 'app_champion', methods: ['POST'])]
    public function getAllChampions(Request $request, EntityManagerInterface $em): JsonResponse
    {

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ChampionController.php',
        ]);
    }
}
