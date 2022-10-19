<?php

namespace App\Controller;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
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
     * @param TranslationRepository $translationRepository
     * @param LanguageRepository $languageRepository
     * @param Application $id
     * @return Response
     */
    public function index(TranslationRepository $translationRepository, LanguageRepository $languageRepository, Application $id): Response
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


    /**
     * @Route("/translations_by_application_and_language/{slug}/{code}", name="translations_by_application_and_language")
     * @param EntityManagerInterface $entityManager
     * @param TranslationRepository $translationRepository
     * @param ApplicationRepository $applicationRepository
     * @param LanguageRepository $languageRepository
     * @param null $applicationId
     * @param null $code
     * @return Response
     */
    public function translationsByApplicationAndLanguage(EntityManagerInterface $entityManager, TranslationRepository $translationRepository,ApplicationRepository $applicationRepository,LanguageRepository $languageRepository, $slug = null, $code = null): Response
    {
        $data = [];

        $language = $languageRepository->findBy(['code' => $code]);
        $application = $applicationRepository->findBy(['slug' => $slug]);

        if(isset($language[0]) and !empty($language[0]) and isset($application[0]) and !empty($application[0])){
            foreach ($application[0]->getApplicationTerms() as $applicationTerm) {
                $trans = $translationRepository->findBy(['termId' => $applicationTerm->getTermId(), 'languageId' => $language[0]->getId()]);
                if (isset($trans) && !empty($trans)) {
                    $data[$applicationTerm->getTermId()->getTermKey()] = $trans[0]->getDescription();
                } else {
                    $data[$applicationTerm->getTermId()->getTermKey()] = $applicationTerm->getTermId()->getTermKey() ;
                }
            }
        }else{
            $data['status'] = 403;
            $data['data'] = 'Incorrect parameters';
        }

        return new Response(json_encode($data));
    }
}
