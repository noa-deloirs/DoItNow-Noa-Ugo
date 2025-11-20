<?php

namespace App\Controller\Api;

use App\Entity\Categorie;
use App\Entity\User;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories')]
class CategorieController extends AbstractController
{
    #[Route('', name: 'api_categories_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], 401);
        }

        $categories = $categorieRepository->findAll();

        $data = [];
        foreach ($categories as $categorie) {
            $data[] = [
                'id'      => $categorie->getId(),
                'libelle' => $categorie->getLibelle(),
                'color'   => $categorie->getColor(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'api_categories_create', methods: ['POST'])]
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

        if (empty($data['libelle']) || empty($data['color'])) {
            return $this->json(['message' => "Champs 'libelle' et 'color' obligatoires"], 400);
        }

        $categorie = new Categorie();
        $categorie->setLibelle($data['libelle']);
        $categorie->setColor($data['color']);

        $em->persist($categorie);
        $em->flush();

        return $this->json([
            'id'      => $categorie->getId(),
            'libelle' => $categorie->getLibelle(),
            'color'   => $categorie->getColor(),
        ], 201);
    }

    #[Route('/{id}', name: 'api_categories_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user || !$user->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            return $this->json(['message' => 'Catégorie introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['message' => 'JSON invalide'], 400);
        }

        if (!empty($data['libelle'])) {
            $categorie->setLibelle($data['libelle']);
        }

        if (!empty($data['color'])) {
            $categorie->setColor($data['color']);
        }

        $em->flush();

        return $this->json([
            'id'      => $categorie->getId(),
            'libelle' => $categorie->getLibelle(),
            'color'   => $categorie->getColor(),
        ]);
    }

    #[Route('/{id}', name: 'api_categories_delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user || !$user->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            return $this->json(['message' => 'Catégorie introuvable'], 404);
        }

        $em->remove($categorie);
        $em->flush();

        return $this->json(['message' => 'Catégorie supprimée'], 204);
    }
}