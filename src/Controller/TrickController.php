<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;


class TrickController extends AbstractController
{
    /**
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();

        return $this->render('trick/index.html.twig', [
          'tricks' => $tricks,
            'fixed_menu'=> 'enabled'
        ]);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // $file stores the uploaded file

            $file = $form->get('cover')->getData();
            $files = $form->get('images')->getData();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            // Move the file to the directory where images are stored
            try {
                $file->move(
                    $this->getParameter('image_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'cover' property to store the image file name
            // instead of its contents
            $trick->setCover($fileName);

            foreach ($files as $file) {
               $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                // Move the file to the directory where images are stored
                try {
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $trick->addImage($fileName);
            }



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index');
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="trick_show", methods={"GET"})
     */
    public function show(Trick $trick): Response
    {
         $niveau = Trick::NIVEAU[$trick->getNiveau()];
         $trick_group = Trick::NIVEAU[$trick->getTrickGroup()];

        return $this->render('trick/show.html.twig', [
          'trick' => $trick,
          'niveau' => $niveau,
          'trick_group' => $trick_group
        ]);
    }


    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {  
        $trick->setCover(
        new File($this->getParameter('image_directory').'/'.$trick->getCover())
        );
    
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


 // $file stores the uploaded file

        $file = $form->get('cover')->getData();
        $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
        // Move the file to the directory where images are stored
        try {
            $file->move(
                $this->getParameter('image_directory'),
                $fileName
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        // updates the 'cover' property to store the image file name
        // instead of its contents
        $trick->setCover($fileName);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trick_index', ['id' => $trick->getId()]);
        }

        return $this->render('trick/edit.html.twig', [

            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_index');
    }

    /**
     * @Route("/ajax/", name="trick_ajax", methods={"POST"})
     */
    public function ajax(TrickRepository $trickRepository, Request $request){
      
        return $this->render('trick/ajax.html.twig', [

            'tricks' => $trickRepository->loadXtricks($request->request->get('first'), 3),
            
        ]);
        
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
