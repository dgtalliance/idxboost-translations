<?php

namespace App\Controller;

use App\Entity\Language;
use App\Entity\Term;
use App\Form\LanguageType;
use App\Form\TermType;
use App\Repository\LanguageRepository;
use App\Repository\TermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/language")
 */
class LanguageController extends AbstractController
{
    /**
     * @Route("/", name="app_language_index", methods={"GET"})
     * @param LanguageRepository $languageRepository
     * @return Response
     */
    public function index(LanguageRepository $languageRepository): Response
    {
        return $this->render('language/index.html.twig', [
            'languages' => $languageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_language_new", methods={"GET", "POST"})
     * @param Request $request
     * @param LanguageRepository $languageRepository
     * @return Response
     */
    public function new(Request $request, LanguageRepository $languageRepository): Response
    {
        $language = new Language();
        $form = $this->createForm(LanguageType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $languageRepository->add($language, true);

            return $this->redirectToRoute('app_language_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('language/new.html.twig', [
            'language' => $language,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="app_language_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Language $language
     * @param LanguageRepository $languageRepository
     * @return Response
     */
    public function edit(Request $request, Language $language, LanguageRepository $languageRepository): Response
    {
        $form = $this->createForm(LanguageType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $languageRepository->add($language, true);

            return $this->redirectToRoute('app_language_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('language/edit.html.twig', [
            'language' => $language,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_language_delete")
     * @param Language $id
     * @param LanguageRepository $languageRepository
     * @return Response
     */
    public function delete(Language $id, LanguageRepository $languageRepository): Response
    {
        $languageRepository->remove($id, true);

        return $this->redirectToRoute('app_language_index');
    }
}
