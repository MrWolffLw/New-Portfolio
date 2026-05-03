<?php

namespace App\Controller;

use App\Entity\Competences;
use App\Form\CompetencesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Attribute\Route;

final class CompetencesController extends AbstractController
{
    #[Route('/competences', name: 'app_competences')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $competences = new Competences();

        $form = $this->createForm(CompetencesType::class, $competences);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 🔥 récupération du fichier
            $iconeFile = $form->get('icone')->getData();

            if ($iconeFile) {

                // 🔥 extension sécurisée (WEBP inclus + fallback)
                $extension = $iconeFile->guessExtension();

                if (!$extension) {
                    $extension = 'webp';
                }

                // 🔥 nom unique
                $newFilename = uniqid().'.'.$extension;

                try {
                    // 🔥 déplacement dans /public/uploads
                    $iconeFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Erreur upload image');
                }

                // 🔥 sauvegarde en BDD
                $competences->setIcone($newFilename);
            }

            // 🔥 persist + flush
            $em->persist($competences);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('competences/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
#[Route('/competences/{id}/edit', name: 'competences_edit')]
public function edit(
    Request $request,
    Competences $competence,
    EntityManagerInterface $em
): Response {

    $form = $this->createForm(CompetencesType::class, $competence);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $imageFile = $form->get('icone')->getData();

        if ($imageFile) {

            // 🔥 (optionnel mais propre) supprimer ancienne image
            if ($competence->getIcone()) {
                $oldPath = $this->getParameter('uploads_directory') . '/' . $competence->getIcone();
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

            $competence->setIcone($newFilename);
        }

        $em->flush();

        return $this->redirectToRoute('home');
    }

    // ✅ IMPORTANT : affichage du form
    return $this->render('competences/edit.html.twig', [
        'form' => $form->createView(),
        'competences' => $competence,
    ]);
}
}