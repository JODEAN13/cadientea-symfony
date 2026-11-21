<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PagesController extends AbstractController
{
#[Route('/about', name: 'app_about')]
public function about(): Response
{
    return $this->render('pages/about.html.twig');
}

#[Route('/community', name: 'app_community')]
public function community(): Response
{
    return $this->render('pages/community.html.twig');
}

#[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
public function contact(
    Request $request,
    EntityManagerInterface $em,
    UserRepository $userRepository,
    UserPasswordHasherInterface $passwordHasher
): Response {
    if ($request->isMethod('POST')) {
        $submittedToken = (string) $request->request->get('_csrf_token', '');
        if (!$this->isCsrfTokenValid('contact_message', $submittedToken)) {
            $this->addFlash('error', 'Invalid security token. Please refresh and try again.');
            return $this->redirectToRoute('app_contact');
        }

        $name = trim((string) $request->request->get('name', ''));
        $email = strtolower(trim((string) $request->request->get('email', '')));
        $subject = trim((string) $request->request->get('subject', ''));
        $body = trim((string) $request->request->get('message', ''));

        if ($subject === '' || $body === '') {
            $this->addFlash('error', 'Please fill in both a subject and a message.');
            return $this->redirectToRoute('app_contact');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            $user = $email !== '' ? $userRepository->findOneBy(['email' => $email]) : null;
        }

        if (!$user instanceof User) {
            $fallbackEmail = $email !== '' ? $email : 'guest-customer@cadientea.local';
            $user = $userRepository->findOneBy(['email' => $fallbackEmail]);

            if (!$user instanceof User) {
                $nameParts = preg_split('/\s+/', $name !== '' ? $name : 'Guest Customer', 2);
                $user = new User();
                $user->setEmail($fallbackEmail);
                $user->setFirstName($nameParts[0] ?? 'Guest');
                $user->setLastName($nameParts[1] ?? 'Customer');
                $user->setRole('customer');
                $user->setPassword($passwordHasher->hashPassword($user, bin2hex(random_bytes(16))));
                $em->persist($user);
            }
        }

        $message = new Message();
        $message->setUser($user);
        $message->setSubject(mb_substr($subject, 0, 150));
        $message->setMessage($body);
        $message->setSender('customer');
        $message->setIsRead(false);
        $message->setCreatedAt(new \DateTimeImmutable());

        $em->persist($message);
        $em->flush();

        $this->addFlash('success', 'Your message has been sent to our team!');
        return $this->redirectToRoute('app_contact');
    }

    return $this->render('pages/contact.html.twig');
}
}
