<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentsRepository;
use App\Entity\User;
use App\Repository\UserRepository;


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
     * @Route("member/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            
            
            $trick->setVideoDocs($this->docsInputManager($form->get('videoDocs')->getData()));
            $trick->setImgDocs($this->docsInputManager($form->get('imgDocs')->getData()));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index');
        }

        return $this->render('member/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{slug}-{id}", name="trick_show", methods={"GET","POST"}, requirements= {"slug": "[a-z0-9\-]*"})
     */
    public function show(Trick $trick, Request $request, string $slug): Response
    {   
        if ($trick->getSlugName() !== $slug) {
          return $this->redirectToRoute("trick_show", [
            'id' => $trick->getId(),
            'slug' => $trick->getSlugName()
          ], 301);
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
        }

         $niveau = Trick::NIVEAU[$trick->getNiveau()];
         $trick_group = Trick::TRICK_GROUP[$trick->getTrickGroup()];

        return $this->render('trick/show.html.twig', [
          'comment'=> $comment,
          'form' => $form->createView(),
          'trick' => $trick,
          'niveau' => $niveau,
          'trick_group' => $trick_group
        ]);
    }


    /**
     * @Route("member/{id}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {  
        $docOfFiles = [];
        $attachements = $trick->getImgDocs();

        foreach ($attachements as $file) {
          $docOfFiles[] = new File($this->getParameter('image_directory').'/'.$file);
        }

        $trick->setImgDocs($docOfFiles);
    
        $form = $this->createForm(TrickType::class, $trick);

        if ($request->isMethod('post')) {

          $storedFiles = $trick->getImgDocs();
        }

        $form->handleRequest($request);


       
        if ($form->isSubmitted() && $form->isValid()) {
          
          $arrayOfDocs = $form->get('imgDocs')->getData();

          if ($form->get('imgDocs')->getData() == null && isset($storedFile)) {

              $trick->setImgDocs($storedFiles);

          }else{

           // $file stores the uploaded file

            // $file = $form->get('cover')->getData();
            $docs = $form->get('imgDocs')->getData();
            $arrayOfDocs=[];

            foreach ($docs as $file) {

              $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
              $arrayOfDocs[] = $fileName;
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
              // $trick->setCover($fileName);

            }
            $trick->setImgDocs($arrayOfDocs);


          }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index');
        }


        return $this->render('member/edit.html.twig', [

            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("member/{id}/delete", name="trick_delete", methods={"DELETE"})
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

      if (!$request->request->has('first')) {

         return $this->render('trick/ajax.html.twig', [
          'count' => $trickRepository->countTricks()  
        ]);
          
      }else{

        return $this->render('trick/ajax.html.twig', [

            'tricks' => $trickRepository->loadXtricks($request->request->get('first'), 4),    
        ]);

      }   
   
    }


    /**
     * @Route("/new_comments/{id}", name="new_comments", methods={"POST"})
     */
    public function newComments(CommentsRepository $commentRepo, Request $request, Trick $trick){

      if (null !== $request->request->get('first')) {
        return $this->render('trick/comments.html.twig', [

        'comments' => $commentRepo->findComments($trick->getId(), $request->request->get('first'))
            
        ]);
      }
      else
      {
        return $this->render('trick/comments.html.twig', [

        'comments' => $commentRepo->findComments($trick->getId()),
        'initial_load' => true
            
        ]);
      }

        
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

    /**
     * generate unique names for docs and manage their storage
     * @return array
     */
    private function docsInputManager($docs){

      $arrayOfDocs=[];

      if (!empty($docs)) {

         foreach ($docs as $file) {

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $arrayOfDocs[] = $fileName;
            // Move the file to the directory where images are stored
              try {
                  $file->move(
                      $this->getParameter('image_directory'),
                      $fileName
                  );
              } catch (FileException $e) {
                  // ... handle exception if something happens during file upload
              }

            }
      }

      return $arrayOfDocs;

    }
}
