<?php

namespace ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ProjectBundle\Entity\ActivityComment;
use ProjectBundle\Entity\Activity;
use ProjectBundle\Form\ActivityCommentType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * ActivityComment controller.
 *
 * @Route("/projet/activitycomment")
 */
class ActivityCommentController extends Controller
{
    /**
     * Creates a new activity comment.
     *
     * @Route("/{id}/new", name="activitycomment_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Activity $activity
     * @param Request $request
     * @return JsonResponse
     */
    public function newAction(Activity $activity, Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $activityComment        = new ActivityComment();
            $form                   = $this->createForm('ProjectBundle\Form\ActivityCommentType', $activityComment);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $activityComment->setActivity($activity);
                $em->persist($activityComment);
                $em->flush();

                return new JsonResponse(array('message' => 'Success!','type' => 'new'), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'new',
                    'form' => $this->renderView(
                        'ProjectBundle:ActivityComment:ActivityComment_form.html.twig',
                        array(
                            'url' => $this->generateUrl('activitycomment_new',array('id' => $activity->getId())),
                            'form' => $form->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }
    /**
     * Creates a new activity comment.
     *
     * @Route("/{id}/list", name="activitycomment_list", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Activity $activity
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Activity $activity, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $response       = new JsonResponse();

                /*Recuperation des activitys en base*/
                $t_activityComment     = $entityManager->getRepository("ProjectBundle:ActivityComment")->getActivityComments($activity->getid());

                return new JsonResponse(
                    array(
                        'message' => 'Success !',
                        'type' => 'list',
                        'list' => $this->renderView("ProjectBundle:ActivityComment:ActivityComment_list.html.twig",array("activityComment_list" => $t_activityComment))
                ), 200);
            }
        }
    }
    /**
     * Displays a form to edit an existing activity comment entity.
     *
     * @Route("/{id}/edit", name="activitycomment_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param ActivityComment $activitycomment
     * @return JsonResponse
     */
    public function editAction(Request $request, ActivityComment $activitycomment)
    {
        if($request->isXmlHttpRequest()) {
            $editForm = $this->createForm('ProjectBundle\Form\ActivityCommentType', $activitycomment);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse(array('message' => 'Success !','type' => 'edit','id' => $activitycomment->getId()), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'edit',
                    'form' => $this->renderView(
                        'ProjectBundle:ActivityComment:ActivityComment_form.html.twig',
                        array(
                            'url' => $this->generateUrl('activitycomment_edit',array('id' => $activitycomment->getId())),
                            'urldelete' => $this->generateUrl('activitycomment_delete',array('id' => $activitycomment->getId())),
                            'form' => $editForm->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Deletes a activitycomment entity.
     *
     * @Route("/{id}/delete", name="activitycomment_delete", options={"expose"=true})
     * @Method("DELETE")
     *
     * @param Request $request
     * @param ActivityComment $activitycomment
     * @return JsonResponse
     */
    public function deleteAction(Request $request, ActivityComment $activitycomment)
    {
        $form = $this->createDeleteForm($activitycomment);
        $form->handleRequest($request);
        $id = $activitycomment->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($activitycomment);
            $em->flush();

            return new JsonResponse(array('message' => 'Success !','type' => 'delete','id' => $id), 200);
        }

        return new JsonResponse(
            array(
                'message' => 'Success !',
                'type' => 'delete',
                'form' => $this->renderView(
                    '@Project/global/Content_form_delete.html.twig',
                    array(
                        'url' => $this->generateUrl('activitycomment_delete',array('id' => $activitycomment->getId())),
                        'form' => $form->createView(),
                    ))), 200);
    }

    /**
     * Creates a form to delete a activity entity.
     *
     * @param ActivityComment $activitycomment The activity entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ActivityComment $activitycomment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('activitycomment_delete', array('id' => $activitycomment->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
