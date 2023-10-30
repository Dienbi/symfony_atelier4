<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/Affiche', name: 'Affiche')]
    public function Affiche (AuthorRepository $rep)
        {
            $author=$rep->findAll() ; 
            return $this->render('author/Affiche.html.twig',['author'=>$author]);
        }
        #[Route('/ADDs', name: 'ADDs')]
        public function ADD (AuthorRepository $rep, EntityManagerInterface $em)
        {
            $author1 = new Author();
            $author1->setUsername("test"); 
            $author1->setEmail("test@gmail.com"); 
            $em->persist($author1);
            $em->flush();
    
            return $this->redirectToRoute('Affiche');
        }
        #[Route('/addF', name: 'addF')]
        public function addF(ManagerRegistry $mr, AuthorRepository $repo,Request $req): Response
        {
            $s=new Author();   // 1- instance
            $form=$this->createForm(AuthorType::class,$s);//2- creation formulaire 
            $form->handleRequest($req); //anlasyser la requette http et récuperer les données 
            if($form->isSubmitted())
            {
                $em=$mr->getManager();    //3- persist+flush
                $em->persist($s);
                $em->flush();
                return $this->redirectToRoute('Affiche');
            };
    
            return $this->render('author/add.html.twig',[
                'f'=>$form->createView()
            ]);
        }
        #[Route('/update/{id}', name: 'update')]
    public function update(ManagerRegistry $mr, AuthorRepository $repo,Request $req,$id): Response
    {
        $s= $repo->find($id); // 1- récupération
        $form=$this->createForm(AuthorType::class,$s);//2- creation formulaire 
        $form->handleRequest($req);
        if($form->isSubmitted())
        {
            $em=$mr->getManager();    //3- persist+flush
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute('Affiche');
        };

        return $this->renderForm('author/update.html.twig',[
            'f'=>$form
        ]);
    }
    #[Route('/remove/{id}', name: 'remove')]

    public function remove(AuthorRepository $repo,$id,ManagerRegistry $mr): Response
    {
    $student=$repo->find($id);
    $em=$mr->getManager();
    $em->remove($student);
    $em->flush();
    return new Response('removed');
    }
    #[Route('/list', name: 'list')]

    public function list(AuthorRepository $repo, ManagerRegistry $mr): Response
{
    $authors = $repo->listAuthorByEmail();
    return $this->render('author/list.html.twig', ['author' => $authors]);
}
#[Route('/search', name:'search')]
public function searchAuthorsByBookCountAction(Request $request, AuthorRepository $authorRepository)
{
    $form = $this->createFormBuilder()
        ->add('minBooks', IntegerType::class, ['required' => false, 'label' => 'Minimum Books'])
        ->add('maxBooks', IntegerType::class, ['required' => false, 'label' => 'Maximum Books'])
        ->add('search', SubmitType::class, ['label' => 'Search'])
        ->getForm();

    $form->handleRequest($request);

    $authors = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $minBooks = $data['minBooks'];
        $maxBooks = $data['maxBooks'];

        $authors = $authorRepository->findAuthorsByBookCountRange($minBooks, $maxBooks);
    }

    return $this->render('author/search.html.twig', [
        'form' => $form->createView(),
        'authors' => $authors,
    ]);
    }
}