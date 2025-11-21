<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/users')]
class UserController extends AbstractController
{
    #[Route('', name: 'api_users_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user || !$user->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $users = $userRepository->findAll();

        $data = [];
        foreach ($users as $u) {
            $data[] = [
                'id'     => $u->getId(),
                'pseudo' => $u->getPseudo(),
                'email'  => $u->getEmail(),
                'roles'  => $u->getRoles(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'api_users_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): JsonResponse {
        /** @var User|null $admin */
        $admin = $this->getUser();

        if (!$admin || !$admin->isAdmin()) {
            return $this->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $pseudo   = $data['pseudo']   ?? null;
        $email    = $data['email']    ?? null;
        $password = $data['password'] ?? null;

        if (!$pseudo || !$email || !$password) {
            return $this->json(['message' => 'Champs manquants'], 400);
        }

        if ($userRepository->findOneBy(['email' => $email])) {
            return $this->json(['message' => 'Email déjà utilisé'], 400);
        }

        $user = new User();
        $user->setPseudo($pseudo);
        $user->setEmail($email);
        $user->setAdmin(false);

        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setMotDePasse($hashedPassword);

        $em->persist($user);
        $em->flush();

        return $this->json([
            'id'     => $user->getId(),
            'pseudo' => $user->getPseudo(),
            'email'  => $user->getEmail(),
            'roles'  => $user->getRoles(),
        ], 201);
    }
}
