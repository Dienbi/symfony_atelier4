<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/AfficheB', name: 'AfficheB')]
    public function AfficheB (BookRepository $repo, ManagerRegistry $mr)
        {
            $published=$this->getDoctrine()->getRepository(Book::class)->findBy(['published'=>true]);
            $numpub=count($published);
            $numUnpub=count($this->getDoctrine()->getRepository(Book::class)->findBy(['published'=>false]));
            if($numpub>0)
            {
                return $this->render('book/affiche.html.twig',[
                    'publishedBooks' =>$published,'numpub'=>$numpub,'numUnpub'=>$numUnpub
                ]);
            }else{
                return $this->render('book/nobooks.html.twig');
            }
            /*$repo=$mr->getRepository(Book::class);
            $result=$repo->findAll();
            return $this->render('book/affiche.html.twig',[
                'response' =>$result
            ]);*/
        }
    #[Route('/addb', name: 'addb')]
        public function addb(ManagerRegistry $mr, BookRepository $repo,Request $req): Response
        {
            $s=new Book();   // 1- instance
            $form=$this->createForm(BookType::class,$s);//2- creation formulaire 
            $form->handleRequest($req); //anlasyser la requette http et récuperer les données 
            if($form->isSubmitted())
            {
                
                $author = $s->getAuthor();
                if($author instanceof Author){
                    $author->setNbBooks($author->getNbBooks()+1);
                }
                $em=$mr->getManager();    //3- persist+flush
                $em->persist($s);
                $em->flush();
                return $this->redirectToRoute('AfficheB');
            };
    
            return $this->render('book/add.html.twig',[
                'f'=>$form->createView()
            ]);
        }
        #[Route('/updateB/{ref}', name: 'updateB')]
    public function updateB(ManagerRegistry $mr, BookRepository $repo,Request $req,$ref): Response
    {
        $s= $repo->find($ref); // 1- récupération
        $form=$this->createForm(BookType::class,$s);//2- creation formulaire 
        $form->handleRequest($req);
        if($form->isSubmitted())
        {
            $em=$mr->getManager();    //3- persist+flush
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute('AfficheB');
        };
        return $this->render('book/update.html.twig',[
            'f'=>$form->createView(),
        ]);
}
#[Route('/removeB/{ref}', name: 'removeB')]

public function remove(BookRepository $repo,$ref,ManagerRegistry $mr): Response
{
$student=$repo->find($ref);
$em=$mr->getManager();
$em->remove($student);
$em->flush();
return $this->redirectToRoute('AfficheB');
}
#[Route('/showB/{ref}', name: 'showB')]

public function showB(BookRepository $repo,$ref,ManagerRegistry $mr): Response
{
    $b=$repo->find($ref);
    if(!$b){
    return $this->redirectToRoute('AfficheB');
}
return $this->render('book/show.html.twig', ['b' => $b]); 
}
#[Route('/listB', name: 'listB')]

public function listB(Request $request, BookRepository $bookRepository)
{
    $searchQuery = $request->query->get('search');
    $searchedBook = null;

    if ($searchQuery) {
        $searchedBook = $bookRepository->findOneByRef($searchQuery);
    }

    return $this->render('book/list.html.twig', [
        'searchedBook' => $searchedBook,
    ]);
}
#[Route('/listByAuth', name: 'listB')]
public function listBooksByAuthors(BookRepository $bookRepository)
{
    $books = $bookRepository->booksListByAuthors();

    return $this->render('book/list_by_auth.html.twig', [
        'books' => $books,
    ]);
}
#[Route('/list2023', name: 'list2023')]
public function list2023(BookRepository $bookRepository)
{
    $books = $bookRepository->find2023();

    return $this->render('book/list2023.html.twig', [
        'books' => $books,
    ]);
}

#[Route('/updateCategory', name: 'updateCategory')]
public function updateCategory(BookRepository $bookRepository): Response
{
    $bookRepository->updateCategory();

    return $this->redirectToRoute('AfficheB'); // Redirigez où vous le souhaitez après la mise à jour.
}
#[Route('/countBooksByCategory', name: 'countBooksByCategory')]
public function countBooksByCategoryAction(BookRepository $bookRepository)
{
    $count = $bookRepository->countBooksByCategory('Romance');

    return $this->render('book/count.html.twig', [
        'count' => $count,
    ]);
}
#[Route('/dates', name: 'dates')]
public function dates(BookRepository $bookRepository)
{
    $startDate = new \DateTime('2014-01-01');
    $endDate = new \DateTime('2018-12-31');

    $books = $bookRepository->dates($startDate, $endDate);

    return $this->render('book/dates.html.twig', [
        'books' => $books,
    ]);
}

}