<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\ImageUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private PostRepository $postRepository;
    private UserRepository $userRepository;
    private ImageUploaderService $imageUploaderService;


    public function __construct(PostRepository $postRepository, UserRepository $userRepository, ImageUploaderService $imageUploaderService)
    {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->imageUploaderService = $imageUploaderService;
    }

    #[Route('/posts/show/{id}', name: 'app_posts_show')]
    public function show(int $id): Response
    {
        $post = $this->postRepository->find($id);
        if (!$post)
            throw $this->createNotFoundException(
                'Post not found !'
            );
        return $this->render('post/index.html.twig', [
            'post' => $post,
            'posts_folder' => 'posts/' . $post->getProfile()->getId() .'/',
        ]);
    }

    #[Route('/posts/create', name: 'app_posts_create')]
    public function create(Request $request): Response
    {
        if (!$this->getUser())
            return $this->redirect(path('app_login'));

        $profile = $this->getUser()->getProfile();

        $post = new Post();
        $post->setProfile($profile);

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploaded = $form->get('uploadFile')->getData();
            if ($uploaded) {

                $post_image = $this->imageUploaderService->upload($uploaded, 'posts/'. $profile->getId());
                $post_thumbnail = $this->imageUploaderService->upload_thumbnail($uploaded, 'posts/'. $profile->getId());
                if (! $post_image || !$post_thumbnail) {
                    throw $this->createAccessDeniedException(
                        'could not upload the requested file'
                    );
                }
                $post->setPicture($post_image);
                $post->setThumbnail($post_thumbnail);
            }

            $post->setCreatedAt(new \DateTimeImmutable());
            $this->postRepository->save($post, true);
            return $this->redirectToRoute('app_home');

        }

        return $this->render('post/create.html.twig', [
            'create_form' => $form->createView(),
        ]);
    }

    #[Route('/posts/like/{id}', name: 'app_posts_like')]
    public function like(int $id): Response
    {
        $post = $this->postRepository->find($id);
        if (!$post)
            return new JsonResponse('post not found!', 404);
        if (!$this->getUser())
            return new JsonResponse('you need to be login !', 403);

        if ($post->getLikes()->contains($this->getUser()->getProfile()))
            $post->removeLike($this->getUser()->getProfile());
        else
            $post->addLike($this->getUser()->getProfile());
        $this->postRepository->save($post, true);
        return new JsonResponse(null, 200);
    }

}
