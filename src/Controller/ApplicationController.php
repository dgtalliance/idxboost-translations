<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\ApplicationTerm;
use App\Entity\Term;
use App\Form\ApplicationType;
use App\Form\TermType;
use App\Repository\ApplicationRepository;
use App\Repository\TermRepository;
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
     */
    public function index(ApplicationRepository $applicationRepository): Response
    {
        return $this->render('application/index.html.twig', [
            'applications' => $applicationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_application_new", methods={"GET", "POST"})
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
     */
    public function delete(Request $request, Application $id, ApplicationRepository $applicationRepository): Response
    {
        $applicationRepository->remove($id, true);
        return $this->redirectToRoute('app_application_index');
    }

    /**
     * @Route("/asociate/term/{id}", name="app_application_asociate_term_index")
     */
    public function asociateTermIndex(Request $request, Application $id)
    {
        return $this->render('application/asociateTermIndex.html.twig', ['application' => $id]);
    }

    /**
     * @Route("/asociate/term/data/{id}", name="app_application_asociate_term_data")
     */
    public function asociateTermData(Request $request, EntityManagerInterface $em, Application $id)
    {
        $asociateTerms = $id->getApplicationTerms();

        $application = $id->getId();
        $RAW_QUERY = "Select te.* from term as te where te.id not in (Select term_id_id from application_term where application_id_id = $application);";

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
     */
    public function asociateTermRemove(EntityManagerInterface $entityManager, ApplicationTerm $id)
    {
        $entityManager->remove($id);
        $entityManager->flush();
        return new Response(json_encode(['status' => 200]));
    }


}
