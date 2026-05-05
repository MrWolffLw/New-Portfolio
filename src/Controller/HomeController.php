<?php

namespace App\Controller;

use App\Repository\CompetencesRepository;
use App\Repository\ParcoursRepository;
use App\Repository\ProjetsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    // #[IsGranted('ROLE_ADMIN')]
     #[Route('/', name: 'home')]
    public function index(ProjetsRepository $repo, CompetencesRepository $competences, ParcoursRepository $parc): Response
    {
        $projets = $repo->findAll();
        $competences = $competences->findAll();
        $parcours = $parc->findAll();
        return $this->render('home/index.html.twig', [
            'projets' => $projets,
            'competences' => $competences,
            'parcours' => $parcours
        ]);
    }
}
