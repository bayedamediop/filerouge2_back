<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Profils;
use App\Service\UserService;
use App\Repository\UserRepository;
use App\Repository\ProfilsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use ApiPlatform\Core\Filter\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

    public function __construct(SerializerInterface $serialize,Security $security,
                               UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->serialize= $serialize;
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
 * @Route(
 *     "api/admin/users/{id}",
 *      name="putUserId",
 *     methods={"PUT"},
 *     defaults={
 *      "_api_resource_class"=User::class,
 *      "_api_item_operation_name"="putUserId"
 *     }
 *     )
 */
public function putUserId($id,UserService $service, Request $request,
                          EntityManagerInterface $manager,SerializerInterface $serializer,UserRepository $u)
{
    $userForm= $service->PutUser($request, 'avatar');
    //dd($userForm);
    //$userUpdate = $service->PutUser($request, 'avatar');
    // dd($userUpdate);
    $user = $u->find($id);
    foreach ($userForm as $key => $value) {
        if($key === 'profil'){
            $value = $serializer->denormalize($value, Profils::class);
        }
        $setter = 'set'.ucfirst(trim(strtolower($key)));
        //dd($setter);
        if(method_exists(User::class, $setter)) {
            $user->$setter($value);
            //dd($user);
        }
    }
    $manager->flush();
    return new JsonResponse("success",200,[],true);
}
  // _______________________________archiver un user-------------------------

    /**
     * @Route(
     *  name = "archiver",
     *  path = "/api/admin/users/{id}",
     *  methods = {"DELETE"},
     *  defaults  = {
     *      "__controller"="App\Controller\UserController::archiver",
     *      "__api_ressource_class"=User::class,
     *      "__api_collection_operation_name"="archiver_users"
     * }
     * )
     */
    public function archiver($id,UserRepository $userRepository,EntityManagerInterface $manager)
    {
        $user = $userRepository->find($id);
        $user->setArchivage(true);
        $manager->flush();
        return new JsonResponse("User Archivé",200,[],true);

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
    /**
     * @Route (
     *     name="transationduncomte",
     *      path="/api/admin/transactions",
     *      methods={"GET"},
     *     defaults={
     *           "__controller"="App\Controller\UserController::transationduncomte",
     *           "__api_ressource_class"=User::class,
     *           "__api_collection_operation_name"="mesTransactions"
     *         }
     * )
     */
    public function transationduncomte(Request $request,
                                UserRepository $userRepository,
                          TokenStorageInterface $token)
    {
        //dd('oki');
       //dd($rett);
       $userConnecte = $token->getToken()->getUser()->getId();
       //dd($userConnecte);
       $objettransaction = ($userRepository->find((int)$userConnecte));
       //dd($objettransaction);
       //dd($transation->getClientRecu()->getCni());
        //dd($transation);
           

        return $this->json($objettransaction,200,[]  );      
        //dd($userConnecte);
    }

    /**
     * @Route (
     *     name="findTransactiondepot",
     *      path="/api/admin/users/{id}/depots",
     *      methods={"GET"},
     *     defaults={
     *            "__controller"="App\Controller\UserController::findTransactiondepot",
     *           "__api_ressource_class"=User::class,
     *           "__api_collection_operation_name"="find_Transaction_depot"
     *         }
     * )
     */
    public function findTransactiondepot($id, UserRepository $comptesRepository)
    {
        $test = $comptesRepository->find($id);
      // dd($test);
      //dd($test);
        if ($test) {
            return $this->json($test,200,[],["groups"=>"transactionunuser:read"]);
        }else{
            return $this->json(" Le nu numero de ce  Comtpt inexistant");
        }
    }

    /**
     * @Route (
     *     name="findTransactionretrait",
     *      path="/api/admin/users/{id}/retrais",
     *      methods={"GET"},
     *     defaults={
     *            "__controller"="App\Controller\UserController::findTransactionretrait",
     *           "__api_ressource_class"=User::class,
     *           "__api_collection_operation_name"="find_Transaction_retrait"
     *         }
     * )
     */
    public function findTransactionretrait($id, UserRepository $comptesRepository)
    {
        $test = $comptesRepository->find($id);
       
        if ($test) {
            return $this->json($test,200,[],["groups"=>"transactionretrait:read"]);
        }else{
            return $this->json(" Le nu numero de ce  Comtpt inexistant");
        }
    }
}
