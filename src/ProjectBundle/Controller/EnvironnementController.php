<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Environnement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Environnement controller.
 *
 * @Route("projet/environnement")
 */
class EnvironnementController extends Controller
{
    /**
     * Lists all environnements entities.
     *
     * @Route("/", name="environnement_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('environnement_table');
        $table['id']                    = 'dataTable';
        $table['cols'][]                = array('filter'=>0,'name'=>'project','data'=>'project','width'=>'125px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Name','data'=>'name','width'=>'100px');
        $table['cols'][]                = array('filter'=>0,'name'=>'url','data'=>'url','href'=>'');
        $table['cols'][]                = array('filter'=>0,'name'=>'modifier','data'=>'null','edit'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>0,"orderable"=>0);
        $table['cols'][]                = array('filter'=>0,'name'=>'supprimer','data'=>'null','del'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>0,"orderable"=>0);
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('ProjectBundle:Environnement:Environnement.html.twig',array(
            'activeMenu' => 'environnement',
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }

    /**
     * Retourne les environnements pour les afficher dans un tableau
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/table", name="environnement_table")
     * @Method("GET")
     */
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();

                $name           = $request->get('name');
                $url            = $request->get('url');
                $project        = $request->get('project');

                $response       = new JsonResponse();

                /*Recuperation des environnements en base*/
                $t_environnement['data']      = $entityManager->getRepository("ProjectBundle:Environnement")->getEnvironnements($name,$url,$project);

                $response->setContent(json_encode($t_environnement));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    /**
     * Creates a new environnement entity.
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/new", name="environnement_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */

    public function newAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $environnement = new Environnement();
            $form = $this->createForm('ProjectBundle\Form\EnvironnementType', $environnement);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($environnement);
                $em->flush();

                return new JsonResponse(array('message' => 'Success!','type' => 'new'), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'new',
                    'form' => $this->renderView('ProjectBundle:Environnement:Environnement_form.html.twig',
                        array(
                            'url' => $this->generateUrl('environnement_new'),
                            'form' => $form->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Finds and displays a environnement entity.
     *
     * @Route("/{id}", name="environnement_show")
     * @Method("GET")
     *
     * @param environnement $environnement
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @Route("/{id}/edit", name="environnement_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Environnement $environnement
     * @return JsonResponse
     */
    public function editAction(Request $request, Environnement $environnement)
    {
        if($request->isXmlHttpRequest()) {
            $editForm = $this->createForm('ProjectBundle\Form\EnvironnementType', $environnement);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse(array('message' => 'Success !','type' => 'edit','id' => $environnement->getId()), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'edit',
                    'form' => $this->renderView(
                        '@Project/Environnement/Environnement_form.html.twig',
                        array(
                            'url' => $this->generateUrl('environnement_edit',array('id' => $environnement->getId())),
                            'form' => $editForm->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Deletes a environnement entity.
     *
     * @Route("/{id}/delete", name="environnement_delete", options={"expose"=true})
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Environnement $environnement
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Environnement $environnement)
    {
        $form = $this->createDeleteForm($environnement);
        $form->handleRequest($request);
        $id = $environnement->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($environnement);
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
                        'url' => $this->generateUrl('environnement_delete',array('id' => $environnement->getId())),
                        'form' => $form->createView(),
                    ))), 200);
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
