<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Contact;

//for text type included in form, each form type needs to be included separately
use Symfony\Component\Form\Extension\Core\Type\TextType;

//for submit type included in form, each form type needs to be included separately
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactsController extends Controller
{
    /**
     * @Route("/", name="contact_list")
     */
    public function contactListAction()
    {
        // returns all list of contacts from Database
        $contacts = $this -> getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->findAll();
        return $this->render('contact/index.html.twig', array(
          'contacts' => $contacts
        ));

    }

    /**
     * @Route("/contact/create", name="create_contact")
     */
    public function createAction(Request $request)
    {

        // creates new contact
        $newContact = new Contact;

        $form = $this-> createFormBuilder($newContact)
          ->add('name', TextType::class, array('attr' => array(
            'class' => 'form-control', 'placeholder'=>'Full name', 'style' => 'margin-bottom:0.9375rem'
          )))
          ->add('tel', TextType::class, array('attr' => array(
            'class' => 'form-control','placeholder'=>'Tel No.', 'style' => 'margin-bottom:0.9375rem'
          )))
          ->add('Save', SubmitType::class, array('attr' => array(
            'label' => 'Create Contact',  'class' => 'btn btn-primary'
            )))
          ->getForm();

          $form ->handleRequest($request);
          if($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $tel = $form['tel']->getData();


            $newContact -> setName($name);
            $newContact -> setTel($tel);

            $em = $this -> getDoctrine() ->getManager();

            //adds valid form info with $newcontact whichi is Doctine entity to database
            $em->persist($newContact);
            $em -> flush();

            $this -> addFlash(
              'notice', 'Contact Saved!'
            );

            return $this -> redirectToRoute('contact_list');


          }


        return $this->render('contact/create.html.twig', array(
          'form' => $form ->createView() ));
    }

    /**
     * @Route("/contact/edit/{id}", name="contact_edit")
     */
    public function editAction($id, Request $request)
    {
        //  edit  of contact list
        $updateContact = $this -> getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->find($id);

          $updateContact -> setName($updateContact->getName());
          $updateContact -> setTel($updateContact->getTel());

          $form = $this-> createFormBuilder($updateContact)
            ->add('name', TextType::class, array('attr' => array(
              'class' => 'form-control', 'placeholder'=>'Full name', 'style' => 'margin-bottom:0.9375rem'
            )))
            ->add('tel', TextType::class, array('attr' => array(
              'class' => 'form-control','placeholder'=>'Tel No.', 'style' => 'margin-bottom:0.9375rem'
            )))
            ->add('Update Contact', SubmitType::class, array('attr' => array(
              'label' => 'Save Contact',  'class' => 'btn btn-primary'
              )))
            ->getForm();

            $form ->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
              $name = $form['name']->getData();
              $tel = $form['tel']->getData();

              $em = $this -> getDoctrine() ->getManager();
              $updateContact = $em ->getRepository('AppBundle:Contact')->find($id);

              $updateContact -> setName($name);
              $updateContact -> setTel($tel);



              //adds valid form info with $updatecontact whichi is Doctine entity to database
              $em -> flush();

              $this -> addFlash(
                'notice', 'Contact Updated!'
              );

              return $this -> redirectToRoute('contact_list');


            }

          return $this->render('contact/edit.html.twig', array(
            'contacts' => $updateContact, 'form' => $form ->createView()
          ));
    }

    /**
     * @Route("/contact/delete/{id}", name="delete_contact")
     */
    public function deleteAction($id)
    {

        // deletes contact and redirects to index contact list
        $em = $this -> getDoctrine() ->getManager();
        $deleteContact = $em ->getRepository('AppBundle:Contact')->find($id);
        $em -> remove($deleteContact);
        $em -> flush();
        $this -> addFlash(
          'notice', 'Contact Truncated!'
        );

        return $this -> redirectToRoute('contact_list');

    }
}
