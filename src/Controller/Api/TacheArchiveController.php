<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\TacheArchiveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/taches/archive')]
class TacheArchiveController extends AbstractController
{
    #[Route('', name: 'api_taches_archive_index', methods: ['GET'])]
    public function index(TacheArchiveRepository $repo): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        // accès interdit aux users normaux
        if (!$user || !$user->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $archives = $repo->findAll();
        $data = [];

        foreach ($archives as $a) {
            $data[] = [
                'id'            => $a->getId(),
                'titre'         => $a->getTitre(),
                'description'   => $a->getDescription(),
                'dateEcheance'  => $a->getDateEcheance()?->format('Y-m-d'),
                'priorite'      => $a->getPriotite(),
                'categorie'     => $a->getCategorie()?->getLibelle(),
                'statut'        => $a->getStatut()?->getLibelle(),
                'dateArchivage' => $a->getDateArchivage()->format('Y-m-d'),
            ];
        }

        return $this->json($data);
    }
}