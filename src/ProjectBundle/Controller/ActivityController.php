<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Activity controller.
 *
 * @Route("projet/activity")
 */
class ActivityController extends Controller
{
    /**
     * Lists all activity entities.
     *
     * @Route("/", name="activity_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        /*Rendu du tableau */
        $table['order']                = 1;
        $table['ajax']['url']           = $this->generateUrl('activity_table');
        $table['id']                    = 'dataTable';
        $table['cols'][]                = array('filter'=>0,'name'=>'','data'=>'null','width'=>'10px','detail'=>'','class'=>"dt-center detail-row","searchable"=>1,"orderable"=>1);
        $table['cols'][]                = array('filter'=>0,'name'=>'Date Creation','data'=>'dateCreation','width'=>'80px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Date Modification','data'=>'dateModification','width'=>'80px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Cadre Contractuel','data'=>'cadreContractuel','width'=>'80px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Libelle','data'=>'libelle');
        $table['cols'][]                = array('filter'=>0,'name'=>'Etat','data'=>'etat','width'=>'80px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Project','data'=>'project','width'=>'100px');
        $table['cols'][]                = array('filter'=>0,'name'=>'','data'=>'null','com'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>1,"orderable"=>1);
        $table['cols'][]                = array('filter'=>0,'name'=>'','data'=>'null','edit'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>1,"orderable"=>1);
        $table['cols'][]                = array('filter'=>0,'name'=>'','data'=>'null','del'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>1,"orderable"=>1);
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('ProjectBundle:Activity:Activity.html.twig',array(
            'activeMenu' => 'activity',
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }

    /**
     * Retourne les activitys pour les afficher dans un tableau
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/table", name="activity_table")
     * @Method("GET")
     */
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();

                $libelle           = $request->get('libelle');
                $cadreContractuel  = $request->get('cadreContractuel');
                $etat              = $request->get('etat');
                $project           = $request->get('project');

                $response       = new JsonResponse();

                /*Recuperation des activitys en base*/
                $t_activity['data']      = $entityManager->getRepository("ProjectBundle:Activity")->getactivitys($libelle,$cadreContractuel,$etat,$project);

                $response->setContent(json_encode($t_activity));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    /**
     * Creates a new activity entity.
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/new", name="activity_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */

    public function newAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $activity = new Activity();
            $form = $this->createForm('ProjectBundle\Form\ActivityType', $activity);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($activity);
                $em->flush();

                return new JsonResponse(array('message' => 'Success!','type' => 'new'), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'new',
                    'form' => $this->renderView(
                        'ProjectBundle:Activity:Activity_form.html.twig',
                        array(
                            'url' => $this->generateUrl('activity_new'),
                            'form' => $form->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Finds and displays a activity entity.
     *
     * @Route("/{id}", name="activity_show")
     * @Method("GET")
     *
     * @param activity $activity
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Activity $activity)
    {
        $deleteForm = $this->createDeleteForm($activity);

        return $this->render('activity/show.html.twig', array(
            'activity' => $activity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing activity entity.
     *
     * @Route("/{id}/edit", name="activity_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Activity $activity
     * @return JsonResponse
     */
    public function editAction(Request $request, Activity $activity)
    {
        if($request->isXmlHttpRequest()) {
            $editForm = $this->createForm('ProjectBundle\Form\ActivityType', $activity);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse(array('message' => 'Success !','type' => 'edit','id' => $activity->getId()), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'edit',
                    'form' => $this->renderView(
                        'ProjectBundle:Activity:Activity_form.html.twig',
                        array(
                            'url' => $this->generateUrl('activity_edit',array('id' => $activity->getId())),
                            'form' => $editForm->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Deletes a activity entity.
     *
     * @Route("/{id}/delete", name="activity_delete", options={"expose"=true})
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Activity $activity
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Activity $activity)
    {
        $form = $this->createDeleteForm($activity);
        $form->handleRequest($request);
        $id = $activity->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($activity);
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
                        'url' => $this->generateUrl('activity_delete',array('id' => $activity->getId())),
                        'form' => $form->createView(),
                    ))), 200);
    }

    /**
     * Creates a form to delete a activity entity.
     *
     * @param Activity $activity The activity entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Activity $activity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('activity_delete', array('id' => $activity->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
