<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Environnement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Environnement controller.
 *
 * @Route("environnement")
 */
class EnvironnementController extends Controller
{
    /**
     * Lists all environnement entities.
     *
     * @Route("/", name="environnement_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $environnements = $em->getRepository('ProjectBundle:Environnement')->findAll();

        return $this->render('environnement/index.html.twig', array(
            'environnements' => $environnements,
        ));
    }

    /**
     * Creates a new environnement entity.
     *
     * @Route("/new", name="environnement_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $environnement = new Environnement();
        $form = $this->createForm('ProjectBundle\Form\EnvironnementType', $environnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($environnement);
            $em->flush();

            return $this->redirectToRoute('environnement_show', array('id' => $environnement->getId()));
        }

        return $this->render('environnement/new.html.twig', array(
            'environnement' => $environnement,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a environnement entity.
     *
     * @Route("/{id}", name="environnement_show")
     * @Method("GET")
     */
    public function showAction(Environnement $environnement)
    {
        $deleteForm = $this->createDeleteForm($environnement);

        return $this->render('environnement/show.html.twig', array(
            'environnement' => $environnement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing environnement entity.
     *
     * @Route("/{id}/edit", name="environnement_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Environnement $environnement)
    {
        $deleteForm = $this->createDeleteForm($environnement);
        $editForm = $this->createForm('ProjectBundle\Form\EnvironnementType', $environnement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('environnement_edit', array('id' => $environnement->getId()));
        }

        return $this->render('environnement/edit.html.twig', array(
            'environnement' => $environnement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a environnement entity.
     *
     * @Route("/{id}", name="environnement_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Environnement $environnement)
    {
        $form = $this->createDeleteForm($environnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($environnement);
            $em->flush();
        }

        return $this->redirectToRoute('environnement_index');
    }

    /**
     * Creates a form to delete a environnement entity.
     *
     * @param Environnement $environnement The environnement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Environnement $environnement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('environnement_delete', array('id' => $environnement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
