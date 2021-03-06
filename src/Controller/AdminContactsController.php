<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Form\ContactsType;
use App\Repository\ContactsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/contacts")
 */
class AdminContactsController extends AbstractController
{
    /**
     * @Route("/", name="admin_contacts_index", methods={"GET"})
     */
    public function index(ContactsRepository $contactsRepository, Request $request): Response
    {
        //Accessing request object error_log(print_r($request->query));
        $active = $request->query->get('active'); 
        error_log(print_r($active));
        if($active === 1) {
           return $this->render('admin_contacts/index.html.twig', [
                'contacts' => $contactsRepository->returnIsEnabled(),
            ]);
        }

        if($active === 0) {
           return $this->render('admin_contacts/index.html.twig', [
                'contacts' => $contactsRepository->returnIsNotEnabled(),
            ]);
        } 
            
        return $this->render('admin_contacts/index.html.twig', [
            'contacts' => $contactsRepository->findAll(),
        ]);    
    }

    /**
     * @Route("/active", name="admin_contacts_active", methods={"GET"})
     */
    public function active(ContactsRepository $contactsRepository): Response
    {
        return $this->render('admin_contacts/index.html.twig', [
            'contacts' => $contactsRepository->returnIsEnabled(),
        ]);
    }

    /**
     * @Route("/inactive", name="admin_contacts_inactive", methods={"GET"})
     */
    public function inactive(ContactsRepository $contactsRepository): Response
    {
        return $this->render('admin_contacts/index.html.twig', [
            'contacts' => $contactsRepository->returnIsNotEnabled(),
        ]);
    }

    /**
     * @Route("/new", name="admin_contacts_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contacts();
        $form = $this->createForm(ContactsType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('admin_contacts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_contacts/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_contacts_show", methods={"GET"})
     */
    public function show(Contacts $contact): Response
    {
        return $this->render('admin_contacts/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_contacts_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Contacts $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactsType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_contacts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_contacts/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_contacts_delete", methods={"POST"})
     */
    public function delete(Request $request, Contacts $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_contacts_index', [], Response::HTTP_SEE_OTHER);
    }
}
