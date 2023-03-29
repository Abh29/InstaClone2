<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileEditFormType;
use App\Repository\PostRepository;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use App\Service\ImageUploaderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{

    public function __construct(private ProfileRepository       $profileRepository,
                                private UserRepository          $userRepository,
                                private ImageUploaderService    $imageUploaderService,
                                private PostRepository $postRepository
    )
    {
    }

    #[Route('/profiles/{id}', name: 'app_profiles')]
    public function index(ProfileRepository $profileRepository, int $id, Request $request): Response
    {
        if ( $this->getUser() && $this->getUser()->getUserIdentifier() == $id)
            return $this->redirect(path('app_home'));

        $profile = $profileRepository->find($id);
        if (!$profile || $profile->getUser()->isAdmin())
            throw $this->createNotFoundException(
                'No Profile found for id '.$id
            );
        /* @var Profile $currentProfile */
        $currentProfile = $this->getUser()->getProfile();
        $posts = null;
        $likes = array();

        if ($currentProfile->getId() === $profile->getId())
            $posts = $this->postRepository->getForFollowers($profile, $request->get('page', 1), $request->get('limit', 12));
        else if ($currentProfile->getFollowing()->contains($profile, ))
            $posts = $this->postRepository->getForFollowers($profile, $request->get('page', 1), $request->get('limit', 12));
        else
            $posts = $this->postRepository->getPublicOnly($profile, $request->get('page', 1), $request->get('limit', 12));

        foreach ($posts as $post){
            $likes[$post->getId()] = $currentProfile->getLikedPosts()->contains($post);
        }

        $isSubscribed = $profile->getFollowers()->contains($this->getUser()->getProfile());
        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
            'posts' => $posts,
            'likes' => $likes,
            'profile_picture' => 'profiles/' . ($profile->getPicture() ?? 'filler.png'),
            'posts_folder' => 'posts/' . $id .'/',
            'isSubscribed' => $isSubscribed,
        ]);
    }

    #[Route('/profiles/{id}/edit', name: 'app_profiles_edit')]
    public function edit(int $id, Request $request) {
        $profile = $this->profileRepository->find($id);
        if (!$profile)
            throw $this->createNotFoundException(
                'No Profile found for id '.$id
            );
        $form = $this->createForm(ProfileEditFormType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploaded = $form->get('uploadFile')->getData();
            if ($uploaded) {
                $new_file = $this->imageUploaderService->upload($uploaded, 'profiles', 300);
                $this->imageUploaderService->remove_image($profile->getPicture(), 'profiles' );
                $profile->setPicture($new_file);
            }

            $this->profileRepository->save($profile, true);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('profile/edit.html.twig', [
            'edit_form' => $form->createView(),
            'profile_picture' => 'profiles/' . ($profile->getPicture() ?? 'filler.png'),
            'posts_folder' => 'posts/' . $id .'/',
            'current_profile' => $profile
        ]);
    }

    #[Route('/profiles/{id}/subscribe', name: 'app_profiles_subscribe')]
    public function subscribe(int $id, Request $request) {
//        return new JsonResponse(['you have successfully subscribed !'],200);
        if (!$this->getUser() || $this->getUser()->getID() === $id)
            return new JsonResponse(['you can not subscribe to your own profile !'],403);
        $profile = $this->profileRepository->find($id);
        if (!$profile)
            return new JsonResponse(['this profile does not exist!'], 400);
        if ($this->getUser()->getProfile()->getFollowing()->contains($profile)){
            $this->getUser()->getProfile()->removeFollowing($profile);
            $this->profileRepository->save($this->getUser()->getProfile(), true);
            return new JsonResponse(['you have successfully unsubscribed !'],200);
        }
        $this->getUser()->getProfile()->addFollowing($profile);
        $this->profileRepository->save($this->getUser()->getProfile(), true);
        return new JsonResponse(['you have successfully subscribed !'],200);
    }

    #[Route('/search', name: 'app_profile_search')]
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        $result  = $this->userRepository->findLikeUserName($query);

        if (count($result) == 0)
            $result = $this->userRepository->findLikeUserName('%' . $query);

        $out = array();

        foreach ($result as $u){
            if ($u->isAdmin())
                continue;
            $out[] = [
                'id' => $u->getId(),
                'userName' => $u->getUserName(),
                'picture' => '/images/profiles/' . $u->getProfile()->getPicture(),
                'link' => $this->generateUrl('app_profiles', ['id' => $u->getProfile()->getId()]),
            ];
        }
        return new JsonResponse($out, 200);
    }

}
