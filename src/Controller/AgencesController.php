<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Agences;
use App\Entity\User;
use App\Repository\AgencesRepository;
use App\Repository\ComptesRepository;
use App\Repository\ProfilsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

use Symfony\Component\Serializer\SerializerInterface;

class AgencesController extends AbstractController
{
    private $validator;
    private $em;
    private $agence;
    private $competence;
    private $serializer;
    private $compte;
    private $userRepository;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer,
                                AgencesRepository $agence, UserRepository $userRepository, ComptesRepository $compte)
    {
        $this->validator = $validator;
        $this->em = $em;
        $this->agence = $agence;
        $this->serializer = $serializer;
        $this->compte = $compte;
        $this->userRepository = $userRepository;


    }

    /**
     * @Route (
     *     name="creatAgence",
     *      path="/api/admin/agences",
     *      methods={"POST"},
     *     defaults={
     *           "__controller"="App\Controller\AgencesController::creatAgence",
     *           "__api_ressource_class"=Agences::class,
     *           "__api_collection_operation_name"="add_Agence"
     *         }
     * )
     */
    public function creatAgence(Request $request, SerializerInterface $serialize,
                             AgencesRepository $agencesRepository,ComptesRepository $comptesRepository,
                                ProfilsRepository $profilsRepository,UserPasswordEncoderInterface $encoder,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $json = json_decode($request->getContent());
        //dd($json,401);

        //verifions s'il faut crée le groupe oubien l'affecté des competences
        // dd($agences);
        if (isset($json->agence)) {
            $newagence = $agencesRepository->find($json->agence);
            $this->em->persist($newagence);
             if ($this->userRepository->find((int)$json->userCreat)) {
                //affectation la/les competences au groupe
                $ucecreer = $this->userRepository->find((int)$json->userCreat);
               // dd($ucecreer);
                $newagence->setUserCreat($ucecreer);
                $this->em->persist($newagence);
            }
            if ($this->userRepository->find((int)$json->user)) {
                //affectation la/les competences au groupe
                $ucecreer = $this->userRepository->find((int)$json->user);
               // dd($ucecreer);

                $newagence->setUser($ucecreer);
                $this->em->persist($newagence);
            }

               //mettons a jour le bdd
            $this->em->flush();
            return $this->json('affecte succesfully',Response::HTTP_OK);
        } else { //si groupe de competence n'existe on crée
            //die();
            $newagence = new Agences();
            $newagence->setNumAgence(rand(9, 1000000000))
                ->setAdresseAgence($json->adresse)
                ->setStatut(false);
            //dd($json->comptes);
            //for ($i = 0; $i < count($json->comptes); $i++) {
                if ($this->compte->find($json->comptes)) {
                    //affectation la/les competences au groupe
                    $comptes = $comptesRepository->find((int)$json->comptes);
                     //dd($comptes);
                    $newagence->setCompte($comptes);
                    $this->em->persist($newagence);
                //}
            }
           // for ($i = 0; $i < count($json->userCreat); $i++) {
            //dd($json->userCreat);
                if ($this->userRepository->find((int)$json->userCreat)) {
                    //affectation la/les competences au groupe
                    $ucecreer = $this->userRepository->find((int)$json->userCreat);
                    //dd($ucecreer);
                    $newagence->setUserCreat($ucecreer);
                    $this->em->persist($newagence);

                //}
            }
            //dd($json->user);
            // $users = $serialize->deserialize($request->getContent(), User::class, 'json');
            //dd($users);
            // $password = $users->getPassword();
            for ($i = 0; $i < count($json->user); $i++) {
                //creation de la/les competences
                //$users = $this->serializer->denormalize($json->user, "App\Entity\User");
                //dd($users);
                $newuser = new User();
                //dd($json->user[$i]->profil);
                 if($profilsRepository->find((int)$json->user[$i]->profil)){
                     $profil = $profilsRepository->find((int)$json->user[$i]->profil);
                   //  dd($profil);
                     if ($profil->getLibelle() !== "UTILISATEUR"){
                         return $this->json('cet id n est pas un utilisater');
                     }else{
                         $newuser->setProfils($profil);
                     }
                 }
                $newuser->setEmail($json->user[$i]->email);
                $newuser->setNom($json->user[$i]->nom);
                $newuser->setPrenom($json->user[$i]->prenom);
                $newuser->setPassword($json->user[$i]->password);


                //$newuser->setPassword($this->$encoder->encodePassword($json->user[$i]->password));
                $newuser->setAdresse($json->user[$i]->adresse);
                $newuser->setCni($json->user[$i]->cni);
                $newuser->setPhone($json->user[$i]->phone);
                $newuser->setArchivage($json->user[$i] = false);
                $newagence->setUser($newuser);
                $this->em->persist($newuser);

                $this->em->persist($newagence);
            }

        }
       $this->em->flush();
        return $this->json('added succesfully', Response::HTTP_OK);
    }
    // _______________________________archiver un agence est ces utilisateurs-------------------------
    /**
     * @Route (
     *     name="archive",
     *      path="/api/admin/agences/{id}",
     *      methods={"DELETE"},
     *     defaults={
     *           "__controller"="App\Controller\AgencesController::archive",
     *           "__api_ressource_class"=Agences::class,
     *           "__api_collection_operation_name"="archive_Agence"
     *         }
     * )
     */
    public function archive($id,AgencesRepository $agencesRepository,UserRepository $userRepository,EntityManagerInterface $manager)
    {
        $user = $agencesRepository->find($id);
        $user->setStatut(true);
       $user->getUser()->setArchivage(true);

            $manager->flush();
        return new JsonResponse("User Archivé",200,[],true);

    }
}


