<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contact;
use App\Form\ContactFormType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Entity\Post;


final class PageController extends AbstractController
{
    private const POSTS_PER_PAGE = 5;

    #[Route('/', name: 'index')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $repository = $doctrine->getRepository(Category::class);
        $categories = $repository->findAll();
        return $this->render('index.html.twig', ['categories' => $categories]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('about.html.twig', []);
    }

    #[Route('/blog', name: 'blog')]
    public function blog(ManagerRegistry $doctrine, int $page = 1): Response
    {
        $page = max(1, $page);

        $repository = $doctrine->getRepository(Post::class);
        $posts = $repository->findAll();
        $recents = $repository->findRecents();
        
        $total = count($posts);
        $totalPages = max(1, (int) ceil($total / self::POSTS_PER_PAGE));

        if ($page > $totalPages) {
            return $this->redirectToRoute('blog', ['page' => $totalPages]);
        }

        return $this->render('blog.html.twig', [
            'posts' => $posts,
            'recents' => $recents,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }

    #[Route('/blog/buscar/{page}', name: 'blog_buscar')]
    public function buscar(ManagerRegistry $doctrine, Request $request, int $page = 1): Response
    {
        $page = max(1, $page);

        $repository = $doctrine->getRepository(Post::class);
        $searchTerm = $request->query->get('searchTerm', '');
        $posts = $repository->findBuscadosPaginados($searchTerm, $page, self::POSTS_PER_PAGE);
        $recents = $repository->findRecents();

        $total = count($posts);
        $totalPages = max(1, (int) ceil($total / self::POSTS_PER_PAGE));

        if ($page > $totalPages) {
            return $this->redirectToRoute('blog_buscar', ['page' => $totalPages, 'searchTerm' => $searchTerm]);
        }

        return $this->render('blog/blog.html.twig', [
            'posts'      => $posts,
            'recents'    => $recents,
            'searchTerm' => $searchTerm,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(ManagerRegistry $doctrine, Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contacto = $form->getData();    
            $entityManager = $doctrine->getManager();    
            $entityManager->persist($contacto);
            $entityManager->flush();
            return $this->redirectToRoute('thankyou', []);
        }
        return $this->render('contact.html.twig', array(
            'form' => $form->createView()    
        ));
    }
    #[Route('/contact/thankyou', name: 'thankyou')]
    public function thankyou(): Response
    {
        return $this->render('thankyou.html.twig', []);
    }
}
