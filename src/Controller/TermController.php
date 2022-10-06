<?php

namespace App\Controller;

use App\Entity\Term;
use App\Entity\Translation;
use App\Form\TermType;
use App\Form\TranslationFormType;
use App\Repository\LanguageRepository;
use App\Repository\TermRepository;
use App\Repository\TranslationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/term")
 */
class TermController extends AbstractController
{
    /**
     * @Route("/", name="app_term_index", methods={"GET", "POST"})
     */
    public function index(Request $request,TermRepository $termRepository, LanguageRepository $languageRepository): Response
    {
        $languages = $languageRepository->findAll();
        $terms = $termRepository->findBy([], ['termKey' => 'ASC']);
        $termsFilter = [];



        if($request->request->get('languageFilter')){
//           dump($request->request->get('languageFilter'));
//           die();
            foreach ($terms as $term){
                $translations = $this->getTranslationsArrayIDByTerm($term->getTranslations());

                $cumple = true;
                foreach ($request->request->get('languageFilter') as $lId){
                    if(!in_array(intval($lId), $translations)){
                        $cumple = false;
                        break;
                    }
                }
                if($cumple){
                    $termsFilter[] = $term;
                }
            }
        }else{
            $termsFilter = $terms;
        }

        return $this->render('term/index.html.twig', [
            'terms' => $termsFilter,
            'languages' => $languages,
            'select' => json_encode($request->request->get('languageFilter'))
        ]);
    }


    public function getTranslationsArrayIDByTerm($translations){
        $translationsID = [];
        foreach ($translations as $translation){
            $translationsID[] = $translation->getLanguageId()->getId();
        }

        return $translationsID;
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

    /**
     * @Route("/load/terms", name="app_term_load_terms", methods={"GET", "POST"})
     */
    public function loadTerms(EntityManagerInterface $entityManager){

        $fichero = fopen('translate/es_ES.po', 'r');
        $termKeyText = '';
        $termTransText = '';
        $count = 0;

        while (!feof($fichero)){
            $linea = fgets($fichero);
            if($linea != "\n" && substr($linea, 0, 1) != '#' and $linea != false){
                if(strpos($linea, 'msgid') !== false){
                    if($count == 0){
                        $count = 1;
                        $termKeyText = $termKeyText . $linea;
                    }
                    if($count == 2){
                       $this->saveNewTermLoad($termKeyText, $termTransText, $entityManager);
                        $count = 1;
                        $termKeyText = $linea;
                        $termTransText = '';
                    }
                }else{
                    if($count == 1 and strpos($linea, 'msgstr') === false){
                        $termKeyText = $termKeyText . $linea;
                    }
                }

                if(strpos($linea, 'msgstr') !== false){
                    if($count == 1){
                        $termTransText = $termTransText . $linea;
                        $count = 2;
                    }
                }else{
                    if($count == 2 and strpos($linea, 'msgid') === false){
                        $termTransText = $termTransText . $linea;
                    }
                }
            }
            if ($linea === false){
                $this->saveNewTermLoad($termKeyText, $termTransText, $entityManager);
            }
        }

       return $this->redirectToRoute('app_term_index');
    }


    private function saveNewTermLoad($termKeyText, $termTransText, $entityManager){
        $term = new Term();

        $exp_regular = array();
        $exp_regular[0] = '/msgid/';
        $exp_regular[1] = '/\n/';

        $result = preg_replace($exp_regular,"", $termKeyText);
        $result = str_replace('"', '', $result);
        if(strlen($result) > 50){
            $result = substr($result,  0, 50);
        }
        $term->setTermKey($result);


        $exp_regular = array();
        $exp_regular[0] = '/msgstr/';
        $exp_regular[1] = '/\n/';

        $result = preg_replace($exp_regular,"", $termTransText);
        $result = str_replace('"', '', $result);

        $term->setDescription($result);

        $entityManager->persist($term);
        $entityManager->flush();
    }
}
