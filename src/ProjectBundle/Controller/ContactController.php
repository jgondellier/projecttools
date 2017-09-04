<?php

namespace ProjectBundle\Controller;

use ProjectBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ContactController extends Controller
{
    public function indexAction()
    {
        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('project_contact_table');
        $table['id']                    = 'contactTable';
        $table['cols'][]                = array('filter'=>0,'name'=>'Nom','data'=>'nom');
        $table['cols'][]                = array('filter'=>0,'name'=>'Prenom','data'=>'prenom');
        $table['cols'][]                = array('filter'=>0,'name'=>'Mail','data'=>'mail');
        $table['cols'][]                = array('filter'=>0,'name'=>'Projet','data'=>'projectName');
        $table['cols'][]                = array('filter'=>0,'name'=>'description','data'=>'description');
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('ProjectBundle:Contact:Contacts.html.twig',array(
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
     */
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();

                $idBnp         = $request->get('$idBnp');
                $nom            = $request->get('nom');
                $prenom         = $request->get('prenom');
                $mail           = $request->get('mail');
                $project        = $request->get('project');

                $response       = new JsonResponse();

                /*Recuperation des contacts en base*/
                $t_contact['data']      = $entityManager->getRepository("ProjectBundle:Contact")->getContacts($idBnp,$nom,$prenom,$mail,$project);

//                $t_result       = $this->formatForDataTable($t_open,$t_closed);
                $response->setContent(json_encode($t_contact));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

     public function addAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }

        $contact = new Contact();

        $contactForm = $this->createForm( new Contact() );
        $contactForm->handleRequest($request);

        if ($contactForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return new JsonResponse(array('message' => 'Success!'), 200);
        }

        return new JsonResponse(
            array(
                'message' => 'Success !',
                'form' => $this->renderView('ProjectBundle:Contact:Contact_Form.html.twig',
                    array(
                        'form' => $contactForm->createView(),
                    ))), 200);
    }
}
