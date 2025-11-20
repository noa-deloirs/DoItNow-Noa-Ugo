<?php

namespace App\Controller\Api;

use App\Entity\Tache;
use App\Entity\User;
use App\Entity\TacheArchive;
use App\Repository\TacheRepository;
use App\Repository\StatutRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Route('/api/taches')]
class TacheController extends AbstractController
{
    #[Route('', name: 'api_taches_index', methods: ['GET'])]
    public function index(Request $request, TacheRepository $tacheRepository): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], 401);
        }

        $statutId    = $request->query->get('statut');
        $categorieId = $request->query->get('categorie');
        $search      = $request->query->get('q');

        $qb = $tacheRepository->createQueryBuilder('t')
            ->join('t.Statut', 's')
            ->join('t.Categorie', 'c')
            ->addSelect('s', 'c');

        if (!$user->isAdmin()) {
            $qb->andWhere('t.User = :user')
            ->setParameter('user', $user);
        }

        if ($statutId) {
            $qb->andWhere('s.id = :statutId')
            ->setParameter('statutId', $statutId);
        }

        if ($categorieId) {
            $qb->andWhere('c.id = :categorieId')
            ->setParameter('categorieId', $categorieId);
        }

        if ($search) {
            $qb->andWhere('LOWER(t.Titre) LIKE :search OR LOWER(t.description) LIKE :search')
            ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        $taches = $qb->getQuery()->getResult();

        $data = [];
        foreach ($taches as $tache) {
            $data[] = $this->serializeTache($tache);
        }

        return $this->json($data);
    }


    #[Route('', name: 'api_taches_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        StatutRepository $statutRepository,
        CategorieRepository $categorieRepository
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['message' => 'JSON invalide'], 400);
        }

        foreach (['titre', 'description', 'statut', 'categorie'] as $field) {
            if (empty($data[$field])) {
                return $this->json(['message' => "Le champ '$field' est obligatoire"], 400);
            }
        }

        $statut = $statutRepository->find($data['statut']);
        $categorie = $categorieRepository->find($data['categorie']);

        if (!$statut || !$categorie) {
            return $this->json(['message' => 'Statut ou catégorie introuvable'], 400);
        }

        $tache = new Tache();
        $tache->setTitre($data['titre']);
        $tache->setDescription($data['description']);

        if (!empty($data['dateEcheance'])) {
            try {
                $date = new \DateTime($data['dateEcheance']);
                $tache->setDateEcheance($date);
            } catch (\Exception $e) {
                return $this->json(['message' => 'Format de date invalide (attendu: YYYY-MM-DD)'], 400);
            }
        }

        if (!empty($data['priorite'])) {
            $tache->setPriorite($data['priorite']);
        }

        $tache->setUser($user);
        $tache->setStatut($statut);
        $tache->setCategorie($categorie);

        $em->persist($tache);
        $em->flush();

        return $this->json($this->serializeTache($tache), 201);
    }

    #[Route('/{id}', name: 'api_taches_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        TacheRepository $tacheRepository,
        StatutRepository $statutRepository,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], 401);
        }

        $tache = $tacheRepository->find($id);

        if (!$tache) {
            throw new NotFoundHttpException('Tâche introuvable');
        }

        if (!$user->isAdmin() && $tache->getUser() !== $user) {
            throw new AccessDeniedHttpException('Vous ne pouvez pas modifier cette tâche');
        }

        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return $this->json(['message' => 'JSON invalide'], 400);
        }

        if (isset($data['titre'])) {
            $tache->setTitre($data['titre']);
        }
        if (isset($data['description'])) {
            $tache->setDescription($data['description']);
        }
        if (isset($data['dateEcheance'])) {
            try {
                $tache->setDateEcheance(new \DateTime($data['dateEcheance']));
            } catch (\Exception $e) {
                return $this->json(['message' => 'Format de date invalide (YYYY-MM-DD)'], 400);
            }
        }
        if (isset($data['priorite'])) {
            $tache->setPriorite($data['priorite']);
        }
        if (isset($data['statut'])) {
            $statut = $statutRepository->find($data['statut']);
            if (!$statut) {
                return $this->json(['message' => 'Statut introuvable'], 400);
            }
            $tache->setStatut($statut);
        }
        if (isset($data['categorie'])) {
            $categorie = $categorieRepository->find($data['categorie']);
            if (!$categorie) {
                return $this->json(['message' => 'Catégorie introuvable'], 400);
            }
            $tache->setCategorie($categorie);
        }

        $em->flush();

        return $this->json($this->serializeTache($tache));
    }

    #[Route('/{id}', name: 'api_taches_delete', methods: ['DELETE'])]
    public function Delete(
        int $id,
        TacheRepository $tacheRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Non authentifié'], 401);
        }

        $tache = $tacheRepository->find($id);

        if (!$tache) {
            throw new NotFoundHttpException('Tâche introuvable');
        }

        if (!$user->isAdmin() && $tache->getUser() !== $user) {
            throw new AccessDeniedHttpException('Vous ne pouvez pas supprimer cette tâche');
        }

        $archive = new TacheArchive();
        $archive->setTache($tache);
        $archive->setUser($tache->getUser());
        $archive->setCategorie($tache->getCategorie());
        $archive->setStatut($tache->getStatut());

        $archive->setTitre($tache->getTitre());
        $archive->setDescription($tache->getDescription());

        if ($tache->getDateEcheance() !== null) {
            $archive->setDateEcheance($tache->getDateEcheance());
        } else {
            $archive->setDateEcheance(new \DateTime());
        }

        if ($tache->getPriorite() !== null) {
            $archive->setPriotite($tache->getPriorite());
        } else {
            $archive->setPriotite('Non définie');
        }

        $archive->setDateArchivage(new \DateTime());

        $em->persist($archive);
        $em->remove($tache);
        $em->flush();

        return $this->json(['message' => 'Tâche archivée'], 204);
    }



    private function serializeTache(Tache $tache): array
    {
        return [
            'id'           => $tache->getId(),
            'titre'        => $tache->getTitre(),
            'description'  => $tache->getDescription(),
            'dateEcheance' => $tache->getDateEcheance()?->format('Y-m-d'),
            'priorite'     => $tache->getPriorite(),
            'statut'       => $tache->getStatut()?->getLibelle(),
            'categorie'    => $tache->getCategorie()?->getLibelle(),
        ];
    }
}
