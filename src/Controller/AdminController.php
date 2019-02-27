<?php 

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\Persistence\ObjectManager;

class AdminController extends AbstractController
{ 

	private $repository;
	private $em;

	public function __construct(TrickRepository $repository, ObjectManager $em)
	{
		$this->repository = $repository;
		$this->em = $em;
	}
	
/**
 * @Route("/toto", name="manage_tricks")
 */
	public function index()
	{
		$tricks = $this->repository->findAll();
		return $this->render('admin/manage.html.twig', ['tricks'=> $tricks]);
	}
}