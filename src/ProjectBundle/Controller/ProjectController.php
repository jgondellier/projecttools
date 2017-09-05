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
 * @Route("projet")
 */
class ProjectController extends Controller
{
    /**
     * Lists all project entities.
     *
     * @Route("/", name="projet_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        /*Formulaire de nouveau projet*/
        $project = new Project();
        $form = $this->createForm('ProjectBundle\Form\ProjectType', $project);

        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('project_table');
        $table['id']                    = 'projectTable';
        $table['cols'][]                = array('filter'=>0,'name'=>'Name','data'=>'name');
        $table['cols'][]                = array('filter'=>0,'name'=>'Code Source','data'=>'sourcecodeUrl');
        $table['cols'][]                = array('filter'=>0,'name'=>'jtracId','data'=>'jtracId');
        $table['cols'][]                = array('filter'=>0,'name'=>'jiraId','data'=>'jiraId');
        $table['cols'][]                = array('filter'=>0,'name'=>'modifier','data'=>'null','edit'=>1);
        $table['cols'][]                = array('filter'=>0,'name'=>'supprimer','data'=>'null','del'=>1);
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('ProjectBundle:Project:Project.html.twig',array(
            'activeMenu' => 'projet',
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
            'form' => $form->createView(),
        ));
    }

    /**
     * Retourne les contacts pour les afficher dans un tableau
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
     * @Route("/new", name="projet_new")
     * @Method({"GET", "POST"})
     */

    public function newAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'POST') {
                $project = new Project();
                $form = $this->createForm('ProjectBundle\Form\ProjectType', $project);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($project);
                    $em->flush();

                    return new JsonResponse(array('message' => 'Success!'), 200);
                }

                return new JsonResponse(
                    array(
                        'message' => 'Success !',
                        'form' => $this->renderView('ProjectBundle:Contact:Contact_Form.html.twig',
                            array(
                                'project' => $project,
                                'form' => $form->createView(),
                            ))), 200);
            }else{
                throw new AccessDeniedException('Access denied');
            }
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Finds and displays a project entity.
     *
     * @Route("/{id}", name="projet_show")
     * @Method("GET")
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
     * @Route("/{id}/edit", name="projet_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Project $project)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'POST') {
                $editForm = $this->createForm('ProjectBundle\Form\ProjectType', $project);
                $editForm->handleRequest($request);

                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->getDoctrine()->getManager()->flush();

                    return new JsonResponse(array('message' => 'Success!'), 200);
                }

                return new JsonResponse(
                    array(
                        'message' => 'Success !',
                        'form' => $this->renderView('ProjectBundle:Project:Project_edit_form.html.twig',
                            array(
                                'project' => $project,
                                'form' => $editForm->createView(),
                            ))), 200);
            }else{
                throw new AccessDeniedException('Access denied');
            }
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Deletes a project entity.
     *
     * @Route("/{id}", name="projet_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Project $project)
    {
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('projet_index');
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
            ->setAction($this->generateUrl('projet_delete', array('id' => $project->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
