<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserChampion;
use App\Entity\Fight;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Console\Output\OutputInterface;

#[Route('/api')]
class CombatController extends AbstractController
{
    #[Route('/combat', name: 'app_combat', methods: ['POST'])]
    public function Vs(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usernamePlayer1 = $data['username1'];
        $usernamePlayer2 = $data['username2'];

        if (empty($usernamePlayer1) || empty($usernamePlayer2)) {
            return new JsonResponse(['error' => 'Invalid request'], 400);
        }
        $player1 = $em
            ->getRepository(User::class)
            ->findOneBy(["username" => $usernamePlayer1]);
        $player2 = $em
            ->getRepository(User::class)
            ->findOneBy(["username" => $usernamePlayer2]);
        if (empty($usernamePlayer1) || empty($usernamePlayer2)) {
            return new JsonResponse(['error' => 'Users need to exist'], 404);
        }
        $champion1 = $em
            ->getRepository(UserChampion::class)
            ->findOneByUserId(["user" => $player1]);
        $champion2 = $em
            ->getRepository(UserChampion::class)
            ->findOneByUserId(["user" =>  $player2]);

        $winner = $this->Combat($champion1, $player1, $champion2, $player2);
        if (!$winner) {
            return new JsonResponse(["message" => "winner null"], 400);
        }
        $fight = new Fight();
        $fight->setUser1($player1);
        $fight->setUser2($player2);
        $fight->setWinner($winner);
        $fight->setCreatedAt(new DateTimeImmutable());
        $em->persist($fight);
        $em->flush();



        return new JsonResponse(['message' => 'Combat terminer', "winner" => $winner->getUsername()], 201);
    }

    public function Combat($champion1, $user1, $champion2, $user2)
    {
        $first = null;
        while ($champion1->getPv() != 0 && $champion2->getPv() != 0) {
            $first = mt_rand(0, 1);
            if ($first == 1) {
                $this->Attaque($champion1, $champion2);
                if ($champion2->getPv() == 0) {
                    return $user1;
                }
                $this->Attaque($champion2, $champion1);
                if ($champion1->getPv() == 0) {
                    return $user2;
                }
            } else {
                $this->Attaque($champion2, $champion1);
                if ($champion1->getPv() == 0) {
                    return $user2;
                }
                $this->Attaque($champion1, $champion2);
                if ($champion2->getPv() == 0) {
                    return $user1;
                }
            }
        }
    }

    public function Attaque($champion1, $champion2)
    {
        $degats = mt_rand(0, $champion1->getPower());
        $pvRestant = $champion2->getPv() - $degats;
        print('Champion : ' . $champion1->getName() . "attaque champion : " . $champion2->getName());

        if ($pvRestant < 0) {
            $champion2->setPv(0);
            print($degats . " point de dégats ont été infligés il reste " . $champion2->getPv() . " au Champion : " . $champion2->getName());
        } else {
            $champion2->setPv($champion2->getPv() - $degats);
        }
    }
}
