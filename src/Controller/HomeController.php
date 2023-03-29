<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class HomeController extends AbstractController
{

    #[Route('/')]
    public function app(): Response
    {
      return $this->redirectToRoute('app_home');
    }
    #[Route('/home', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        if ($this->getUser()->isAdmin())
            return $this->redirectToRoute('admin');

        /* @var Profile $profile */
        $profile = $this->getUser()->getProfile();

        $likes = array();

        foreach ($profile->getPosts() as $post){
            $likes[$post->getId()] = $profile->getLikedPosts()->contains($post);
        }

        return $this->render('home/index.html.twig', [
            'profile' => $profile,
            'profile_picture' => 'profiles/' . ($profile->getPicture() ?? 'filler.png'),
            'posts_folder' => 'posts/' . $profile->getId() .'/',
            'likes'=> $likes,
        ]);
    }
}
