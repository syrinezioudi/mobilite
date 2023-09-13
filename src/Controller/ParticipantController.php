<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\EtudiantType;
use App\Repository\UserRepository;
use App\Repository\OffreRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\ORM\EntityManagerInterface;


class ParticipantController extends AbstractController
{
    #[Route('/participant', name: 'app_participant')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route('/listparticipants', name: 'app_listParticipants')]
    public function listParticipants(ParticipantRepository $repository)
    {
        $participants = $repository->findAll();
        return $this->render("participant/listeparticipants.html.twig", array("tabParticipants" => $participants));
    }

    #[Route('/listparticipants/{offreId}', name: 'app_list_participants_by_offer')]
    public function listParticipantsParOffre(ParticipantRepository $repository, OffreRepository $offreRepository, $offreId): Response
    {
        // Find the offer object by its ID
        $offer = $offreRepository->find($offreId);

        if (!$offer) {
            throw $this->createNotFoundException('Offer not found');
        }

        // Retrieve participants related to the offer
        $participants = $repository->findBy(['offre' => $offer->getId()]);

        // Render the Twig template and pass both the offer and participants as variables
        return $this->render("participant/liste-participants-par-offre.html.twig", [
            "offer" => $offer,
            "tabParticipants" => $participants
        ]);
    }


    #[Route('/participer/{id}', name: 'app_participer')]
    public function participer(OffreRepository $repository, $id, ManagerRegistry $doctrine, UserRepository $userRepo, ParticipantRepository $rep)
    {
        $idUser = 1;

        $user = $userRepo->find($idUser);
        $offre = $repository->find($id);

        $participant = new Participant();
        $participant->setUser($user);
        $participant->setOffre($offre);
        $participant->setDateParticipation(new \DateTime());

        $em = $doctrine->getManager();
        $em->persist($participant);
        $em->flush();


        // $reclamation->setEtat(1 );
        $em = $doctrine->getManager();
        $em->flush();
        // $rep->sms();
        $this->addFlash('danger', 'reponse envoyée avec succées');


        return $this->redirectToRoute("app_front");


    }

    #[Route('/detailsPar/{id}', name: 'app_details_par')]
    public function showParticipantDetails(ParticipantRepository $repository, $id)
    {
        $participant = $repository->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant not found');
        }

        return $this->render("participant/details-participant.html.twig", [
            "participant" => $participant
        ]);
    }

    #[Route('/updatePar/{id}', name: 'app_updatePar')]
    public function updateParticipant(ParticipantRepository $repository, $id, ManagerRegistry $doctrine, Request $request)
    {


        $participant = $repository->find($id);
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_listParticipants");
        }
        return $this->renderForm("participant/add.html.twig",
            array("formParticipant" => $form));
    }

    #[Route('/removePar/{id}', name: 'app_removePar')]
    public function deleteParticipant(ManagerRegistry $doctrine, $id, ParticipantRepository $repository)
    {
        $participant = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($participant);
        $em->flush();
        return $this->redirectToRoute("app_listParticipants");

    }

    #[Route('/pdf', name: 'pdf')]
    public function pdf(ParticipantRepository $ParticipantRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('participant/pdf.html.twig', [
            'participant' => $ParticipantRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }


    #[Route('/participants', name: 'app_participants')]
    public function listParticipant(ParticipantRepository $repository, PaginatorInterface $paginator, Request $request)
    {
        $participants = $repository->findAll();

        $query = $request->query->get('q');
        $participants = $this->getDoctrine()
            ->getRepository(Participant::class)
            ->searchOffres($query);

        $pagination = $paginator->paginate(
            $participants,
            $request->query->getInt('page', 1),
            10 // number of items per page
        );

        return $this->render("participant/listparticipant.html.twig", [
            "tabParticipants" => $pagination,
            'query' => $query,
        ]);
    }

    #[Route('/addp/{idOffre}', name: 'app_addp')]
    public function addParticipant(Request         $request, EntityManagerInterface $entityManager,
                                                   $idOffre,
                                   OffreRepository $offreRepository,
                                   UserRepository  $userRepository
    )
    {
        $user = $userRepository->find(1);
        $offre = $offreRepository->find($idOffre);

        $participant = new Participant();
        $form = $this->createForm(EtudiantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant->setDateParticipation(new \DateTime());
            $participant->setOffre($offre);
            $participant->setUser($user);

            $uploads_directory = $this->getParameter('postulations_directory');

            $fileRBac = $request->files->get('etudiant')['RBac'];
            $fileR1 = $request->files->get('etudiant')['R1'];
            $fileR2 = $request->files->get('etudiant')['R2'];
            $fileR3 = $request->files->get('etudiant')['R3'];
            $fileR4 = $request->files->get('etudiant')['R4'];
            $fileRL1 = $request->files->get('etudiant')['RL1'];
            $fileRL2 = $request->files->get('etudiant')['RL2'];
            $fileRL3 = $request->files->get('etudiant')['RL3'];

            $filenameRBac = md5(uniqid()) . '.' . $fileRBac->guessExtension();
            $filenameR1 = md5(uniqid()) . '.' . $fileR1->guessExtension();
            $filenameR2 = md5(uniqid()) . '.' . $fileR2->guessExtension();
            $filenameR3 = md5(uniqid()) . '.' . $fileR3->guessExtension();
            $filenameR4 = md5(uniqid()) . '.' . $fileR4->guessExtension();
            $filenameRL1 = md5(uniqid()) . '.' . $fileRL1->guessExtension();
            $filenameRL2 = md5(uniqid()) . '.' . $fileRL2->guessExtension();
            $filenameRL3 = md5(uniqid()) . '.' . $fileRL3->guessExtension();

            $participant->setRBac($filenameRBac);
            $participant->setR1($filenameR1);
            $participant->setR2($filenameR2);
            $participant->setR3($filenameR3);
            $participant->setR4($filenameR4);
            $participant->setRL1($filenameRL1);
            $participant->setRL2($filenameRL2);
            $participant->setRL3($filenameRL3);

            $entityManager->persist($participant);
            $entityManager->flush();

            $fileRBac->move($uploads_directory, $filenameRBac);
            $fileR1->move($uploads_directory, $filenameR1);
            $fileR2->move($uploads_directory, $filenameR2);
            $fileR3->move($uploads_directory, $filenameR3);
            $fileR4->move($uploads_directory, $filenameR4);
            $fileRL1->move($uploads_directory, $filenameRL1);
            $fileRL2->move($uploads_directory, $filenameRL2);
            $fileRL3->move($uploads_directory, $filenameRL3);

            return $this->redirectToRoute("app_front");
        }

        return $this->renderForm("participant/addp.html.twig", [
            "formParticipant" => $form,
        ]);
    }

    #[Route('/acceptOrRefuse/{idParticipant}', name: 'app_accept_part')]
    public function accepterParticipant(Request $request, int $idParticipant, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($idParticipant);


        $message = 'Vous avez été accepté pour loffre ' . $participant->getOffre()->getTitre();

        $email = $participant->getEmail();

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
            $transport->setUsername('esprit.memelegends.app@gmail.com')->setPassword('rrgr hxjr lahy ezpf');
            $mailer = new Swift_Mailer($transport);
            $swiftMessage = new Swift_Message('Message from admin');
            $swiftMessage->setFrom(array('esprit.memelegends.app@gmail.com' => "Message from admin"))
                ->setTo(array($email))
                ->setBody("<h1>" . $message . "</h1>", 'text/html');
            $mailer->send($swiftMessage);
        } else {
            dd("email invalide");
        }

        return $this->redirectToRoute('app_display_message_success');
    }

    #[Route('/refuse/{idParticipant}', name: 'app_refuse_part')]
    public function refuserParticipant(Request $request, int $idParticipant, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($idParticipant);

        $message = 'Vous avez été refusé pour loffre ' . $participant->getOffre()->getTitre();

        $email = $participant->getEmail();

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
            $transport->setUsername('esprit.memelegends.app@gmail.com')->setPassword('rrgrhxjrlahyezpf');
            $mailer = new Swift_Mailer($transport);
            $swiftMessage = new Swift_Message('Message from admin');
            $swiftMessage->setFrom(array('pidev.app.esprit@gmail.com' => "Message from admin"))
                ->setTo(array($email))
                ->setBody("<h1>" . $message . "</h1>", 'text/html');
            $mailer->send($swiftMessage);
        } else {
            dd("email invalide");
        }

        return $this->redirectToRoute('app_display_message_not_success');
    }

    #[Route('/displayMessage/isSuccess}', name: 'app_display_message_success')]
    public function displayMessage(): Response
    {
        $message = 'Vous avez accepté la postulation, un email sera envoyé a l\'etudiant.';

        return $this->render('participant/display-message.html.twig', ['message' => $message]);
    }

    #[Route('/displayMessage/isNotSuccess}', name: 'app_display_message_not_success')]
    public function displayMessageNotSuccess(): Response
    {
        $message = 'Vous avez refusé la postulation, un email sera envoyé a l\'etudiant.';

        return $this->render('participant/display-message.html.twig', ['message' => $message]);
    }

    #[Route('/sort-by-score', name: 'ajax_sort_by_score')]
    public function sortByScore(ParticipantRepository $participantRepository): JsonResponse
    {
        // Fetch and sort data by score
        $participantList = $participantRepository->findByScore(); // Replace with your actual sorting logic

        $jsonData = [];
        foreach ($participantList as $participant) {
            $jsonData[] = $participant->jsonSerialize();
        }

        return new JsonResponse($jsonData);
    }

}
