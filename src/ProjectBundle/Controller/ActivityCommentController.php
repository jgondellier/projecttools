<?php

namespace ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ProjectBundle\Entity\ActivityComment;
use ProjectBundle\Form\ActivityCommentType;

/**
 * ActivityComment controller.
 *
 * @Route("/projet/activitycomment")
 */
class ActivityCommentController extends Controller
{
    /**
     * Lists all ActivityComment entities.
     *
     * @Route("/", name="projet_activitycomment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $activityComments = $em->getRepository('ProjectBundle:ActivityComment')->findAll();

        return $this->render('activitycomment/index.html.twig', array(
            'activityComments' => $activityComments,
        ));
    }

    /**
     * Creates a new ActivityComment entity.
     *
     * @Route("/new", name="projet_activitycomment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $activityComment = new ActivityComment();
        $form = $this->createForm('ProjectBundle\Form\ActivityCommentType', $activityComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($activityComment);
            $em->flush();

            return $this->redirectToRoute('projet_activitycomment_show', array('id' => $activityComment->getId()));
        }

        return $this->render('activitycomment/new.html.twig', array(
            'activityComment' => $activityComment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ActivityComment entity.
     *
     * @Route("/{id}", name="projet_activitycomment_show")
     * @Method("GET")
     */
    public function showAction(ActivityComment $activityComment)
    {
        $deleteForm = $this->createDeleteForm($activityComment);

        return $this->render('activitycomment/show.html.twig', array(
            'activityComment' => $activityComment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ActivityComment entity.
     *
     * @Route("/{id}/edit", name="projet_activitycomment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ActivityComment $activityComment)
    {
        $deleteForm = $this->createDeleteForm($activityComment);
        $editForm = $this->createForm('ProjectBundle\Form\ActivityCommentType', $activityComment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($activityComment);
            $em->flush();

            return $this->redirectToRoute('projet_activitycomment_edit', array('id' => $activityComment->getId()));
        }

        return $this->render('activitycomment/edit.html.twig', array(
            'activityComment' => $activityComment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ActivityComment entity.
     *
     * @Route("/{id}", name="projet_activitycomment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ActivityComment $activityComment)
    {
        $form = $this->createDeleteForm($activityComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($activityComment);
            $em->flush();
        }

        return $this->redirectToRoute('projet_activitycomment_index');
    }

    /**
     * Creates a form to delete a ActivityComment entity.
     *
     * @param ActivityComment $activityComment The ActivityComment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ActivityComment $activityComment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('projet_activitycomment_delete', array('id' => $activityComment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
