<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Model\ContactDTO;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ContactType::class, new ContactDTO());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactDTO = $form->getData();
            try {
//            $mail = (new Email())
//                ->from($contactDTO->email)
//                ->to('admin@admin.com')
//                ->subject('Contact')
//                ->text($contactDTO->name . ' send a new message: ' . $contactDTO->message)
//                ->html('Hello, <br/>You have a new message from <strong>' . $contactDTO->name . '</strong>'
//                    . '<br/><p>Message: ' . $contactDTO->message . '</p>');

                $mail = (new TemplatedEmail())
                    ->from($contactDTO->email)
                    ->to($contactDTO->service ?? 'admin@admin.com')
                    ->subject('Demande de contact')
                    ->context(['data' => $contactDTO])
                    ->htmlTemplate('emails/contact.html.twig');

                $mailer->send($mail);
                $this->addFlash('success', 'Message envoyé !');
                return $this->redirectToRoute('contact');
            } catch (Exception|TransportExceptionInterface $exc) {
                $logger->error(self::class, [$exc->getMessage(), $exc]);
                $this->addFlash('danger', 'Unexpected error !');
            }
        }

        return $this->render('app/contact.html.twig', [
             'form' => $form->createView(),
        ]);
    }

    /**
     * @param HttpExceptionInterface $exception
     *
     * @return Response
     */
    public function notFound(HttpExceptionInterface $exception): Response
    {
//        return new Response('Not found', $exception->getStatusCode());
        return new Response(
            $this->renderView('bundles/TwigBundle/Exception/error404.html.twig', [
                'message' => 'Something went wrong.',
            ]),
            $exception->getStatusCode()
        );
    }
}
