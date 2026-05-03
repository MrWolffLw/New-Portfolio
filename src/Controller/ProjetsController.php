<?php

namespace App\Controller;

use App\Entity\Projets;
use App\Form\ProjetsType;
use App\Repository\ProjetsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ProjetsController extends AbstractController
{


    #[Route('/projets', name: 'projets_new')]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $projets = new Projets();

    $form = $this->createForm(ProjetsType::class, $projets);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // 👉 UPLOAD IMAGE AVANT FLUSH
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {

            $originalExtension = $imageFile->guessExtension() ?? 'jpg';

            $newFilename = uniqid().'.'.$originalExtension;

            try {
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // log si besoin
            }

            $projets->setImage($newFilename);
        }

        $em->persist($projets);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    return $this->render('projets/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/projets/{id}/edit', name: 'projets_edit')]
public function edit(
    Request $request,
    Projets $projet,
    EntityManagerInterface $em
): Response {

    $form = $this->createForm(ProjetsType::class, $projet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $imageFile = $form->get('image')->getData();

        if ($imageFile) {

            // 🔥 (optionnel mais propre) supprimer ancienne image
            if ($projet->getImage()) {
                $oldPath = $this->getParameter('uploads_directory') . '/' . $projet->getImage();
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // log si besoin
            }

            $projet->setImage($newFilename);
        }

        $em->flush();

        return $this->redirectToRoute('home');
    }

    // ✅ IMPORTANT : affichage du form
    return $this->render('projets/edit.html.twig', [
        'form' => $form->createView(),
        'projet' => $projet,
    ]);
}
}