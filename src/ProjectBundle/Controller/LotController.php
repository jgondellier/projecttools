<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Lot;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Lot controller.
 *
 * @Route("projet/lots")
 */
class LotController extends Controller
{
    /**
     * Lists all lots entities.
     *
     * @Route("/", name="lot_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('lot_table');
        $table['id']                    = 'dataTable';
        $table['height']                = '';
        $table['page']                  = 'True';
        $table['pageLength']            = '15';
        $table['cols'][]                = array('filter'=>0,'name'=>'Date Creation','data'=>'dateCreation','type'=>'date-uk','width'=>'70px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Version','data'=>'version','width'=>'60px');
        $table['cols'][]                = array('filter'=>1,'name'=>'project','data'=>'project','width'=>'90px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Description','data'=>'description');
        $table['cols'][]                = array('filter'=>1,'name'=>'UAT','data'=>'recette','width'=>'20px',"checkbox"=>true,'class'=>"dt-center");
        $table['cols'][]                = array('filter'=>1,'name'=>'PPROD','data'=>'preprod','width'=>'20px',"checkbox"=>true,'class'=>"dt-center");
        $table['cols'][]                = array('filter'=>1,'name'=>'PROD','data'=>'prod','width'=>'20px',"checkbox"=>true,'class'=>"dt-center");
        $table['cols'][]                = array('filter'=>1,'name'=>'Etat','data'=>'etat','width'=>'80px');
        $table['cols'][]                = array('filter'=>0,'name'=>'','data'=>'null','edit'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>0,"orderable"=>0);
        $table['cols'][]                = array('filter'=>0,'name'=>'','data'=>'null','del'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>0,"orderable"=>0);
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('ProjectBundle:Lot:Lot.html.twig',array(
            'activeMenu' => 'lot',
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }

    /**
     * Retourne les lots pour les afficher dans un tableau
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/table", name="lot_table")
     * @Method("GET")
     */
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();

                $version           = $request->get('version');
                $project        = $request->get('project');

                $response       = new JsonResponse();

                /*Recuperation des lots en base*/
                $t_lot['data']      = $entityManager->getRepository("ProjectBundle:Lot")->getLots($version,$project);

                $response->setContent(json_encode($t_lot));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    /**
     * Creates a new lot entity.
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/new", name="lot_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */

    public function newAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $lot = new Lot();
            $form = $this->createForm('ProjectBundle\Form\LotType', $lot);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($lot);
                $em->flush();

                return new JsonResponse(array('message' => 'Success!','type' => 'new'), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'new',
                    'form' => $this->renderView('ProjectBundle:Lot:Lot_form.html.twig',
                        array(
                            'url' => $this->generateUrl('lot_new'),
                            'form' => $form->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Finds and displays a lot entity.
     *
     * @Route("/{id}", name="lot_show")
     * @Method("GET")
     *
     * @param Lot $lot
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Lot $lot)
    {
        $deleteForm = $this->createDeleteForm($lot);

        return $this->render('lot/show.html.twig', array(
            'lot' => $lot,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing lot entity.
     *
     * @Route("/{id}/edit", name="lot_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Lot $lot
     * @return JsonResponse
     */
    public function editAction(Request $request, Lot $lot)
    {
        if($request->isXmlHttpRequest()) {
            $editForm = $this->createForm('ProjectBundle\Form\LotType', $lot);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse(array('message' => 'Success !','type' => 'edit','id' => $lot->getId()), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'edit',
                    'form' => $this->renderView(
                        '@Project/Lot/Lot_form.html.twig',
                        array(
                            'url' => $this->generateUrl('lot_edit',array('id' => $lot->getId())),
                            'form' => $editForm->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Deletes a lot entity.
     *
     * @Route("/{id}/delete", name="lot_delete", options={"expose"=true})
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Lot $lot
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Lot $lot)
    {
        $form = $this->createDeleteForm($lot);
        $form->handleRequest($request);
        $id = $lot->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lot);
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
                        'url' => $this->generateUrl('lot_delete',array('id' => $lot->getId())),
                        'form' => $form->createView(),
                    ))), 200);
    }

    /**
     * Creates a form to delete a lot entity.
     *
     * @param Lot $lot The lot entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Lot $lot)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lot_delete', array('id' => $lot->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
