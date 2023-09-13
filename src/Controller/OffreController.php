<?php

namespace App\Controller;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OffreController extends AbstractController
{
    #[Route('/offre', name: 'app_offre')]
    public function index(): Response
    {
        return $this->render('offre/index.html.twig', [
            'controller_name' => 'OffreController',
        ]);
    }


    #[Route('/offres', name: 'app_offres')]
    public function listOffre(OffreRepository $repository, PaginatorInterface $paginator, Request $request)
    {
        $offres = $repository->findAll();

        $query = $request->query->get('q');
        $offres = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->searchOffres($query);

        $pagination = $paginator->paginate(
            $offres,
            $request->query->getInt('page', 1),
            10 // number of items per page
        );

        return $this->render("offre/listoffre.html.twig", [
            "tabOffres" => $pagination,
            'query' => $query,
        ]);
    }

    #[Route('/tri', name: 'tri')]
    public function tri(OffreRepository $repository)
    {
        $offres = $repository->findAll();
        $offres=$repository->Trieparoffre();


        return $this->render("front/liste.html.twig",array("tabOffres"=>$offres));
    }

    #[Route('/trie', name: 'trie')]
    public function trie(OffreRepository $repositoryE)
    {
        $offre = $repositoryE->findAll();
        $offre=$repositoryE->Trieparoffres();


        return $this->render("offre/listoffre.html.twig",array("tabOffres"=>$offre));
    }





    #[Route('/front', name: 'app_front')]

    public function listOffreFront(OffreRepository $repository,PaginatorInterface $paginator, Request $request)
    {
        $offres= $repository->findAll();
        $pagination = $paginator->paginate(
            $offres,
            $request->query->getInt('page', 1),
            10 // number of items per page
        );

        return $this->render("front/liste.html.twig",array("tabOffres"=>$offres));
     }



    #[Route('/addoffre', name: 'app_addoffre')]
    public function addOffre(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request)
    {
        $em = $doctrine->getManager();
        $offre= new Offre();
        $form= $this->createForm(OffreType::class,$offre);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $file = $request->files->get('offre')['img'];
            $uploads_directory = $this->getParameter('postulations_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploads_directory, $filename);
            $offre->setImg($filename);
            $em->persist($offre);
            $em->flush();
            return  $this->redirectToRoute("app_offres");
        }
        return $this->renderForm("offre/add.html.twig",
            array("formOffre"=>$form));
    }

    #[Route('/updateOffre/{id}', name: 'app_updateOffre')]
    public function updateOffre(OffreRepository $repository,$id,ManagerRegistry $doctrine,Request $request)
    {
        $em =$doctrine->getManager();
        $offre= $repository->find($id);
        $form=$this->createForm(OffreType::class,$offre);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $file = $request->files->get('offre')['img'];
            $uploads_directory = $this->getParameter('uploads_directory');
           // $filename = md5(uniqid()) . '.' . $file->guessExtension();
          //  $file->move($uploads_directory, $filename);
          //  $offre->setImg($filename);
            $em->flush();
            return $this->redirectToRoute("app_offres");
        }
        return $this->renderForm("offre/add.html.twig",
            array("formOffre"=>$form));
    }

    #[Route('/removeOffre/{id}', name: 'app_removeOffre')]

    public function deleteOffre(ManagerRegistry $doctrine,$id,OffreRepository $repository)
    {
        $offre= $repository->find($id);
        $em= $doctrine->getManager();
        $em->remove($offre);
        $em->flush();
        return $this->redirectToRoute("app_offres");

    }

    /**
     * @Route("/search", name="event_search")
     */
    #[Route('/search', name: 'offre_search')]
    public function search(Request $request): Response
    {
        $query = $request->query->get('q');
        $offres = $this->getDoctrine()
            ->getRepository(Offre::class)
            ->searchOffres($query);

        return $this->render('offre/search.html.twig', [
            'offres' => $offres,
            'query' => $query,
        ]);
    }
}
