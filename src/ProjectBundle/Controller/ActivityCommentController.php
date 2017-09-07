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
                $t_activity     = $entityManager->getRepository("ProjectBundle:ActivityComment")->getActivityComments($activity->getid());

                $response->setContent(json_encode($this->renderView("ProjectBundle:ActivityComment:ActivityComment_list.html.twig",array("t_activity" => $t_activity))));
                return $response;
            }
        }
    }

}
