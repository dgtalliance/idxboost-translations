<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Language;
use App\Entity\Term;
use App\Repository\LanguageRepository;
use App\Repository\TranslationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/translation")
 */
class TranslationApiController extends AbstractController
{
    /**
     * @Route("/translations_by_application/{id}", name="translations_by_application")
     * @param EntityManagerInterface $entityManager
     * @param TranslationRepository $translationRepository
     * @param LanguageRepository $languageRepository
     * @param Application $id
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager, TranslationRepository $translationRepository, LanguageRepository $languageRepository, Application $id): Response
    {
        $data = [];
        $languages = $languageRepository->findAll();
        foreach ($languages as $lang) {
            $data[$lang->getCode()] = [];
            foreach ($id->getApplicationTerms() as $applicationTerm) {
                $trans = $translationRepository->findBy(['termId' => $applicationTerm->getTermId(), 'languageId' => $lang]);
                if (isset($trans) && !empty($trans)) {
                    $data[$lang->getCode()][$applicationTerm->getTermId()->getTermKey()] = $trans[0]->getDescription();
                } else {
                    $data[$lang->getCode()][$applicationTerm->getTermId()->getTermKey()] = $applicationTerm->getTermId()->getDescription() ? $applicationTerm->getTermId()->getDescription() : '' ;
                }
            }
        }


        return new Response(json_encode($data));

    }
}
