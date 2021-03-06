<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Contact controller.
 *
 * @Route("projet/contact")
 */
class ContactController extends Controller
{
    /**
     * Lists all contacts entities.
     *
     * @Route("/", name="contact_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('contact_table');
        $table['id']                    = 'dataTable';
        $table['cols'][]                = array('filter'=>0,'name'=>'Name','data'=>'name');
        $table['cols'][]                = array('filter'=>0,'name'=>'Prenom','data'=>'prenom');
        $table['cols'][]                = array('filter'=>0,'name'=>'Mail','data'=>'mail');
        $table['cols'][]                = array('filter'=>0,'name'=>'Matricule','data'=>'idBnp');
        $table['cols'][]                = array('filter'=>0,'name'=>'jtracId','data'=>'idJtrac');
        $table['cols'][]                = array('filter'=>1,'name'=>'project','data'=>'project','width'=>'100px');
        $table['cols'][]                = array('filter'=>0,'name'=>'Description','data'=>'description');
        $table['cols'][]                = array('filter'=>0,'name'=>'modifier','data'=>'null','edit'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>0,"orderable"=>0);
        $table['cols'][]                = array('filter'=>0,'name'=>'supprimer','data'=>'null','del'=>1,'width'=>'35px','class'=>"dt-center","searchable"=>0,"orderable"=>0);
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('ProjectBundle:Contact:Contact.html.twig',array(
            'activeMenu' => 'contact',
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }

    /**
     * Retourne les contacts pour les afficher dans un tableau
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/table", name="contact_table")
     * @Method("GET")
     */
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();

                $name           = $request->get('name');
                $prenom         = $request->get('prenom');
                $mail           = $request->get('mail');
                $idJtrac        = $request->get('idJtrac');
                $idBnp          = $request->get('idBnp');
                $project        = $request->get('project');

                $response       = new JsonResponse();

                /*Recuperation des contacts en base*/
                $t_contact['data']      = $entityManager->getRepository("ProjectBundle:Contact")->getContacts($name,$prenom,$mail,$idJtrac,$idBnp,$project);

                $response->setContent(json_encode($t_contact));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    /**
     * Creates a new contact entity.
     *
     * @param Request $request
     * @return null|JsonResponse
     *
     * @Route("/new", name="contact_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     */

    public function newAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $contact = new Contact();
            $form = $this->createForm('ProjectBundle\Form\ContactType', $contact);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();

                return new JsonResponse(array('message' => 'Success!','type' => 'new'), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'new',
                    'form' => $this->renderView('ProjectBundle:Contact:Contact_form.html.twig',
                        array(
                            'url' => $this->generateUrl('contact_new'),
                            'form' => $form->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Finds and displays a contact entity.
     *
     * @Route("/{id}", name="contact_show")
     * @Method("GET")
     *
     * @param Contact $contact
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Contact $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);

        return $this->render('contact/show.html.twig', array(
            'contact' => $contact,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing contact entity.
     *
     * @Route("/{id}/edit", name="contact_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Contact $contact
     * @return JsonResponse
     */
    public function editAction(Request $request, Contact $contact)
    {
        if($request->isXmlHttpRequest()) {
            $editForm = $this->createForm('ProjectBundle\Form\ContactType', $contact);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse(array('message' => 'Success !','type' => 'edit','id' => $contact->getId()), 200);
            }

            return new JsonResponse(
                array(
                    'message' => 'Success !',
                    'type' => 'edit',
                    'form' => $this->renderView(
                        '@Project/Contact/Contact_form.html.twig',
                        array(
                            'url' => $this->generateUrl('contact_edit',array('id' => $contact->getId())),
                            'form' => $editForm->createView(),
                        ))), 200);
        }
        throw new AccessDeniedException('Access denied');
    }

    /**
     * Deletes a contact entity.
     *
     * @Route("/{id}/delete", name="contact_delete", options={"expose"=true})
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Contact $contact
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Contact $contact)
    {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);
        $id = $contact->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
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
                        'url' => $this->generateUrl('contact_delete',array('id' => $contact->getId())),
                        'form' => $form->createView(),
                    ))), 200);
    }

    /**
     * Creates a form to delete a contact entity.
     *
     * @param Contact $contact The contact entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Contact $contact)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contact_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
