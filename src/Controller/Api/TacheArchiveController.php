<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Tache;
use App\Entity\TacheArchive;
use App\Repository\TacheArchiveRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        // accès interdits ux users normaux
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
                'priorite'      => $a->getPriotite(), // (typo dans l’entité, on respecte)
                'categorie'     => $a->getCategorie()?->getLibelle(),
                'statut'        => $a->getStatut()?->getLibelle(),
                'dateArchivage' => $a->getDateArchivage()->format('Y-m-d'),
            ];
        }

        return $this->json($data);
    }

    #[Route('/{id}/restore', name: 'api_taches_archive_restore', methods: ['POST'])]
    public function restore(
        TacheArchive $archive,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user || !$user->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $tache = new Tache();
        $tache->setTitre($archive->getTitre());
        $tache->setDescription($archive->getDescription());
        $tache->setDateEcheance($archive->getDateEcheance());
        $tache->setPriorite($archive->getPriotite());
        $tache->setCategorie($archive->getCategorie());
        $tache->setStatut($archive->getStatut());

        if (method_exists($tache, 'setUser') && method_exists($archive, 'getUser')) {
            $tache->setUser($archive->getUser());
        }

        $em->persist($tache);
        $em->remove($archive);
        $em->flush();

        return $this->json([
            'message' => 'Tâche restaurée',
            'id'      => $tache->getId(),
        ]);
    }
}
