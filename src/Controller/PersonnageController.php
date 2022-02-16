<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Personnage;
use App\Repository\PersonnageRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PersonnageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/personnage')]
#[IsGranted('ROLE_USER')]
class PersonnageController extends AbstractController
{
    #[Route('/', name: 'personnage')]
    public function index(PersonnageRepository $personnageRepository): Response
    {
        return $this->render('personnage/index.html.twig', [
            'personnages' => $personnageRepository->findAll(),
        ]);
    }

    #[Route('/add', name : "add_personnage")]
    public function add(Request $request) : Response
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        if(!$hasAccess){
            return $this->redirectToRoute('personnage');
        }
        $personnage = new Personnage;
        $form = $this->createForm(PersonnageType::class, $personnage);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get('image')->getData();
            $newFilename = uniqid().'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_upload'),
                    $newFilename
                );
            }catch(FileException $e)
            {

            }
            $personnage->setImage($newFilename);
            $em = $this->getDoctrine()->getManager(); //On récupere l'EntityManager
            $em->persist($personnage); // = Equivalent à insert
            $em->flush(); //Execute l'instruction
            return $this->redirectToRoute('personnage');
        }
        // $personnage->setNom("Wandja");
        // $personnage->setPrenom("Charly");
        
        return $this->render('personnage/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'one')]
    public function one(Request $request, Personnage $personnage) : Response
    {
        return $this->render('personnage/one.html.twig', [
            'personnage' => $personnage
        ]);
    }

    #[Route('/update/{id}', name: "update")]
    public function update(Request $request, Personnage $personnage) : Response
    {
        $form = $this->createForm(PersonnageType::class, $personnage);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($form->get('image')->getData()){
                $file = $form->get('image')->getData();
                $newFilename = uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('images_upload'),
                        $newFilename
                    );
                }catch(FileException $e)
                {

                }
                $personnage->setImage($newFilename);
            }
            $em = $this->getDoctrine()->getManager(); //On récupere l'EntityManager
            $em->persist($personnage); // = Equivalent à insert
            $em->flush(); //Execute l'instruction
            return $this->redirectToRoute('personnage');
        }
        return $this->render('personnage/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route("/delete/{id}", name: "delete")]
    public function delete(Request $request, Personnage $personnage) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($personnage);
        $entityManager->flush();
        return $this->redirectToRoute('personnage');
    }
}
