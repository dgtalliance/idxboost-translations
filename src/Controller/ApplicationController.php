<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\ApplicationTerm;
use App\Entity\Term;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/application")
 */
class ApplicationController extends AbstractController
{
    /**
     * @Route("/", name="app_application_index", methods={"GET"})
     * @param ApplicationRepository $applicationRepository
     * @return Response
     */
    public function index(ApplicationRepository $applicationRepository): Response
    {
        return $this->render('application/index.html.twig', [
            'applications' => $applicationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_application_new", methods={"GET", "POST"})
     * @param Request $request
     * @param ApplicationRepository $applicationRepository
     * @return Response
     */
    public function new(Request $request, ApplicationRepository $applicationRepository): Response
    {
        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $applicationRepository->add($application, true);

            return $this->redirectToRoute('app_application_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('application/new.html.twig', [
            'application' => $application,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="app_application_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Application $application
     * @param ApplicationRepository $applicationRepository
     * @return Response
     */
    public function edit(Request $request, Application $application, ApplicationRepository $applicationRepository): Response
    {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $applicationRepository->add($application, true);

            return $this->redirectToRoute('app_application_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('application/edit.html.twig', [
            'application' => $application,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_application_delete")
     * @param Application $id
     * @param ApplicationRepository $applicationRepository
     * @return Response
     */
    public function delete(Application $id, ApplicationRepository $applicationRepository): Response
    {
        $applicationRepository->remove($id, true);
        return $this->redirectToRoute('app_application_index');
    }

    /**
     * @Route("/asociate/term/{id}", name="app_application_asociate_term_index")
     * @param Application $id
     * @return Response
     */
    public function asociateTermIndex(Application $id)
    {
        return $this->render('application/asociateTermIndex.html.twig', ['application' => $id]);
    }

    /**
     * @Route("/asociate/term/data/{id}", name="app_application_asociate_term_data")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Application $id
     * @return Response
     * @throws Exception
     */
    public function asociateTermData(Request $request, EntityManagerInterface $em, Application $id)
    {
        $asociateTerms = $id->getApplicationTerms();

        $application = $id->getId();
        $RAW_QUERY = "Select te.* from term as te where te.id not in (Select term_id_id from application_term where application_id_id = $application) order by term_key ASC ;";

        $notAsociateTerms = $em->getConnection()->fetchAllAssociative($RAW_QUERY);
        return $this->render('application/asociateTermData.html.twig', [
            'asociateTerms' => $asociateTerms,
            'notAsociateTerms' => $notAsociateTerms,
            'application' => $id
        ]);
    }

    /**
     * @Route("/asociate/term/asociate/{application}/{term}", name="app_application_asociate_term_asociate")
     */
    public function asociateTermAsociate(EntityManagerInterface $entityManager, Application $application, Term $term)
    {
        $newApplicationTerm = new ApplicationTerm();
        $newApplicationTerm->setApplicationId($application);
        $newApplicationTerm->setTermId($term);
        $entityManager->persist($newApplicationTerm);
        $entityManager->flush();
        return new Response(json_encode(['status' => 200]));
    }

    /**
     * @Route("/asociate/term/remove/{id}", name="app_application_asociate_term_remove")
     * @param EntityManagerInterface $entityManager
     * @param ApplicationTerm $id
     * @return Response
     */
    public function asociateTermRemove(EntityManagerInterface $entityManager, ApplicationTerm $id)
    {
        $entityManager->remove($id);
        $entityManager->flush();
        return new Response(json_encode(['status' => 200]));
    }

    /**
     * @Route("/term/details/{id}", name="app_application_term_details")
     * @param EntityManagerInterface $entityManager
     * @param Term $id
     * @return Response
     */
    public function termDetail(EntityManagerInterface $entityManager, Term $id)
    {
        return $this->render('application/appTermDetail.html.twig', ['term' => $id] );
    }


}
