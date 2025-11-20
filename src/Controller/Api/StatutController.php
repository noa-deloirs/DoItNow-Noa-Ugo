<?php

namespace App\Controller\Api;

use App\Entity\Statut;
use App\Entity\User;
use App\Repository\StatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/statuts')]
class StatutController extends AbstractController
{
    #[Route('', name: 'api_statuts_index', methods: ['GET'])]
    public function index(StatutRepository $statutRepository): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], 401);
        }

        $statuts = $statutRepository->findAll();

        $data = [];
        foreach ($statuts as $statut) {
            $data[] = [
                'id'      => $statut->getId(),
                'libelle' => $statut->getLibelle(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'api_statuts_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user || !$user->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['message' => 'JSON invalide'], 400);
        }

        if (empty($data['libelle'])) {
            return $this->json(['message' => "Le champ 'libelle' est obligatoire"], 400);
        }

        $statut = new Statut();
        $statut->setLibelle($data['libelle']);

        $em->persist($statut);
        $em->flush();

        return $this->json([
            'id'      => $statut->getId(),
            'libelle' => $statut->getLibelle(),
        ], 201);
    }

    #[Route('/{id}', name: 'api_statuts_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        StatutRepository $statutRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user || !$user->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $statut = $statutRepository->find($id);

        if (!$statut) {
            return $this->json(['message' => 'Statut introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['message' => 'JSON invalide'], 400);
        }

        if (!empty($data['libelle'])) {
            $statut->setLibelle($data['libelle']);
        }

        $em->flush();

        return $this->json([
            'id'      => $statut->getId(),
            'libelle' => $statut->getLibelle(),
        ]);
    }

    #[Route('/{id}', name: 'api_statuts_delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        StatutRepository $statutRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user || !$user->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $statut = $statutRepository->find($id);

        if (!$statut) {
            return $this->json(['message' => 'Statut introuvable'], 404);
        }

        $em->remove($statut);
        $em->flush();

        return $this->json(['message' => 'Statut supprimé'], 204);
    }
}
