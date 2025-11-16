<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ContactController class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ContactController extends AbstractController {
    #[Route('/contact/delete/{id}', name: 'contact_delete', methods: ['GET'])]
    public function delete(int $id, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $contact = $em->getRepository(Contact::class)->find($id);
        if (!$contact) {
            // $this->addFlash('error', 'Kontakt nije pronađen.');
            return $this->redirect($request->headers->get('referer') ?: '/');
        }
        // Optionally add CSRF protection here
        $em->remove($contact);
        $em->flush();
        // $this->addFlash('success', 'Kontakt je uspešno obrisan.');
        return $this->redirect($request->headers->get('referer') ?: '/');
    }
}
