<?php

namespace App\Controller;
use ApiPlatform\Core\Filter\Validator\ValidatorInterface;
use App\Entity\Profils;
use App\Entity\User;
use App\Repository\ProfilsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private $security;
    private $manager;
    private $serialize;
    private $profil;
    private $encoder;
    private $encode;
    private $attente;
    private $validator;
    /**
     * @var ProfilsRepository
     */
    private $profileRepository;

    public function __construct(SerializerInterface $serialize,Security $security,
                                ProfilsRepository  $profileRepository,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->serialize= $serialize;
        $this->profileRepository = $profileRepository ;
        $this->encoder =$encoder;
        $this->manager =$manager;



    }
    /**
     * @Route(
     *  name = "addUser",
     *  path = "/api/admin/users",
     *  methods = {"POST"},
     *  defaults  = {
     *      "__controller"="App\Controller\UserController::addUser",
     *      "__api_ressource_class"=User::class,
     *      "__api_collection_operation_name"="add_users"
     * }
     * )
     */
    public function addUser(Request $request)

    { //all data
        $user = $request->request->all() ;
        // dd($user);
        //get profil
//        $profil = $user["profils"] ;
//
//        if($profil == "ADMIN") {
//            $users = $this->serialize->denormalize($user, "App\Entity\User");
//        } elseif ($profil =="APPRENANT") {
//            $users = $this->serialize->denormalize($user, "App\Entity\Apprenant");
//            $users->setAttente('1');
//        } elseif ($profil =="FORMATEUR") {
//            $users = $this->serialize->denormalize($user, "App\Entity\Formateur");
//        }elseif ($profil =="CM") {
//            $users = $this->serialize->denormalize($user, "App\Entity\Cm");
//        }
        //recupération de l'image
        $photo = $request->files->get("avatar");
        //specify entity
        //dd($photo);
        if(!$photo)
        {
            return new JsonResponse("veuillez mettre une images",Response::HTTP_BAD_REQUEST,[],true);
        }
        //$base64 = base64_decode($imagedata);
        $photoBlob = fopen($photo->getRealPath(),"rb");
        //$users = $this->serialize->denormalize($user,true);
        $users = $this->serialize->denormalize($user, "App\Entity\User");
        $password = $users->getPassword();
        $users->setAvatar($photoBlob);

        $users->setPassword($this->encoder->encodePassword($users,$password));
        //$users->setIsdelate("1");


        //$users->setProfile($this->profileRepository->findOneBy(['libelle'=>$profil])) ;

//         $errors = $validator->validate($users);
//         if (count($errors)){
//             $errors = $this->serialize->serialize($errors,"json");
//             return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
//         }
        $em = $this->getDoctrine()->getManager();
        $em->persist($users);
        $em->flush();

        return $this->json("success",201);

    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
