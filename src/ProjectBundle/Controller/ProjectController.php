<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Project controller.
 *
 * @Route("projet/manage")
 */
class ProjectController extends Controller
{
    /**
     * Lists all project entities.
     *
     * @Route("/", name="project_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('project_table');
        $table['id']                    = 'dataTable';
        $table['height']                = '';
        $table['cols'][]                = array('filter'=>0,'name'=>'Name','data'=>'name','width'=>'125px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Code Source','data'=>'sourcecodeUrl');
        $table['cols'][]                = array('filter'=>0,'name'=>'jtracId','data'=>'jtracId','width'=>'50px');
        $table['cols'][]                = array('filter'=>0,'name'=>'jiraId','data'=>'jiraId','width'=>'100px');
        $table['cols'][]                = array('filter'=>0,'name'=>'modifier','data'=>'null','edit'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>0,"orderable"=>0);
        $table['cols'][]                = array('filter'=>0,'name'=>'supprimer','data'=>'null','del'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>0,"orderable"=>0);
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('ProjectBundle:Project:Project.html.twig',array(
            'activeMenu' => 'project',
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }

    /**
     * Retourne les projets pour les afficher dans un tableau
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/table", name="project_table")
     * @Method("GET")
     */
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();

                $name           = $request->get('name');
                $sourcecodeUrl  = $request->get('sourcecodeUrl');
                $jtracId        = $request->get('jtracId');
                $jiraId         = $request->get('jiraId');

                $response       = new JsonResponse();

                /*Recuperation des contacts en base*/
                $t_contact['data']      = $entityManager->getRepository("ProjectBundle:Project")->getProjects($name,$sourcecodeUrl,$jtracId,$jiraId);

                $response->setContent(json_encode($t_contact));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    /**
     * Creates a new project entity.
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/new", name="project_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */

    public function newAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $project = new Project();
            $form = $this->createForm('ProjectBundle\Form\ProjectType', $project);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();

                return new JsonResponse(array('message' => 'Success!','type' => 'new'), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'new',
                    'form' => $this->renderView('ProjectBundle:Project:Project_form.html.twig',
                        array(
                            'url' => $this->generateUrl('project_new'),
                            'form' => $form->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Finds and displays a project entity.
     *
     * @Route("/{id}", name="project_show")
     * @Method("GET")
     *
     * @param Project $project
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Project $project)
    {
        $deleteForm = $this->createDeleteForm($project);

        return $this->render('project/show.html.twig', array(
            'project' => $project,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing project entity.
     *
     * @Route("/{id}/edit", name="project_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function editAction(Request $request, Project $project)
    {
        if($request->isXmlHttpRequest()) {
            $editForm = $this->createForm('ProjectBundle\Form\ProjectType', $project);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse(array('message' => 'Success !','type' => 'edit','id' => $project->getId()), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'edit',
                    'form' => $this->renderView(
                        '@Project/Project/Project_form.html.twig',
                        array(
                            'url' => $this->generateUrl('project_edit',array('id' => $project->getId())),
                            'form' => $editForm->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Deletes a project entity.
     *
     * @Route("/{id}/delete", name="project_delete", options={"expose"=true})
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Project $project)
    {
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);
        $id = $project->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
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
                        'url' => $this->generateUrl('project_delete',array('id' => $project->getId())),
                        'form' => $form->createView(),
                    ))), 200);
    }

    /**
     * Creates a form to delete a project entity.
     *
     * @param Project $project The project entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Project $project)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_delete', array('id' => $project->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
