<?php

namespace App\Controller;

use App\Entity\Parcours;
use App\Form\ParcoursType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ParcoursController extends AbstractController
{
    #[Route('/parcours/new', name: 'parcours_new')]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $parcours = new Parcours();

    $form = $this->createForm(ParcoursType::class, $parcours);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $em->persist($parcours);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    return $this->render('parcours/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/parcours/{id}/edit', name: 'app_parcours_edit')]
public function edit(
    Request $request,
    Parcours $parcours,
    EntityManagerInterface $em
): Response {

    $form = $this->createForm(ParcoursType::class, $parcours);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $em->flush();

        return $this->redirectToRoute('home');
    }

    // ✅ IMPORTANT : affichage du form
    return $this->render('parcours/edit.html.twig', [
        'form' => $form->createView(),
        'parcours' => $parcours,
    ]);
}
}
