<?php

namespace App\Controller;

use App\Entity\Term;
use App\Entity\Translation;
use App\Form\TermType;
use App\Form\TranslationFormType;
use App\Repository\TermRepository;
use App\Repository\TranslationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/term")
 */
class TermController extends AbstractController
{
    /**
     * @Route("/", name="app_term_index", methods={"GET"})
     */
    public function index(TermRepository $termRepository): Response
    {
        return $this->render('term/index.html.twig', [
            'terms' => $termRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_term_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TermRepository $termRepository): Response
    {
        $term = new Term();
        $form = $this->createForm(TermType::class, $term);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $termRepository->add($term, true);

            return $this->redirectToRoute('app_term_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('term/new.html.twig', [
            'term' => $term,
            'form' => $form,
        ]);
    }



    /**
     * @Route("/{id}/edit", name="app_term_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Term $term, TermRepository $termRepository): Response
    {
        $form = $this->createForm(TermType::class, $term);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $termRepository->add($term, true);

            return $this->redirectToRoute('app_term_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('term/edit.html.twig', [
            'term' => $term,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_term_delete")
     */
    public function delete(Request $request, Term $id, TermRepository $termRepository): Response
    {

            $termRepository->remove($id, true);


        return $this->redirectToRoute('app_term_index');
    }

    /**
     * @Route("/add/translation/{id}", name="app_term_add_translation", methods={"GET", "POST"})
     */
    public function addTranslation(Request $request, TranslationRepository $translationRepository, Term $id): Response
    {
        $translation = new Translation();
        $form = $this->createForm(TranslationFormType::class, $translation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existTranslation = $translationRepository->findBy(['termId' => $id->getId(), 'languageId' => $form->get('languageId')->getData()]);

            if(isset($existTranslation) and !empty($existTranslation)){
                $this->addFlash('error', 'There is already a translation for this language');

                return $this->redirectToRoute('app_term_add_translation', ['id' => $id->getId()], Response::HTTP_SEE_OTHER);
            }else{
                $translation->setTermId($id);
                $translationRepository->add($translation, true);

                $this->addFlash('success', 'Translation added successfully');

                return $this->redirectToRoute('app_term_add_translation', ['id' => $id->getId()], Response::HTTP_SEE_OTHER);
            }


        }

        $translations = $translationRepository->findBy(['termId' => $id->getId()]);

        return $this->renderForm('term/translation.html.twig', [
            'translation' => $translation,
             'term' => $id,
            'translations' => $translations,
            'form' => $form,
            'edit' => false
        ]);
    }

    /**
     * @Route("/edit/translation/{id}/{translationId}", name="app_term_edit_translation", methods={"GET", "POST"})
     */
    public function editTranslation(Request $request, TranslationRepository $translationRepository, Term $id, Translation $translationId): Response
    {
        $translation = $translationId;
        $form = $this->createForm(TranslationFormType::class, $translation);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $translationRepository->add($translation, true);

            $this->addFlash('success', 'Translation edited successfully');

            return $this->redirectToRoute('app_term_add_translation', ['id' => $id->getId()], Response::HTTP_SEE_OTHER);
        }

        $translations = $translationRepository->findBy(['termId' => $id->getId()]);

        return $this->renderForm('term/translation.html.twig', [
            'translation' => $translation,
            'term' => $id,
            'translations' => $translations,
            'form' => $form,
            'edit' => true
        ]);
    }

    /**
     * @Route("/remove/translation/{id}/{term}", name="app_term_remove_translation", methods={"GET", "POST"})
     */
    public function removeTranslation(Request $request, TranslationRepository $translationRepository, Translation $id, Term $term): Response
    {
        $translationRepository->remove($id, true);
        return $this->redirectToRoute('app_term_add_translation', ['id' => $term->getId()]);
    }
}
