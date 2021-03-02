<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Agences;
use App\Entity\Comptes;
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
                         UserRepository $userRepository,    AgencesRepository $agencesRepository,ComptesRepository $comptesRepository,
                                ProfilsRepository $profilsRepository,UserPasswordEncoderInterface $encoder
                             )
    {
        $json = json_decode($request->getContent());
        //dd($json);
        $userCreat = $this->getUser();

        //verifions s'il faut crée le groupe oubien l'affecté des competences
        // dd($agences);
        if (isset($json->agence)) {
           // dd($json->agence);
            $newagence = $agencesRepository->find($json->agence);
            //dd($newagence);
            $this->em->persist($newagence);
             if ($this->userRepository->find((int)$json->userCreat)) {
                //affectation la/les competences au groupe
                $ucecreer = $this->userRepository->find((int)$json->userCreat);
               // dd($ucecreer);
                $newagence->setUserCreat($ucecreer);
                $this->em->persist($newagence);
            }
            if ($json->utilisateur) {
       // dd($json->utilisateur);
                $newuser = new User();
                //dd($json->user[$i]->profil);
                if($profilsRepository->find((int)$json->user[$i]->profil)){
                    $profil = $profilsRepository->find((int)$json->user[$i]->profil);
                    //  dd($profil);
                    if (($profil->getLibelle() !== "UTILISATEUR")){
                        return $this->json('cet profil est pas un utilisater');
                    }else{
                        $newuser->setProfils($profil);
                    }
                }
                $newuser->setEmail($json->user[$i]->email);
                $newuser->setNom($json->user[$i]->nom);
                $newuser->setPrenom($json->user[$i]->prenom);
                $newuser->setPassword($json->user[$i]->password);
                //$password = $json->getPassword();
                //$users->setPassword($passwordEncoder->encodePassword($users,$password));
                $newuser->setAdresse($json->user[$i]->adresse);
                $newuser->setCni($json->user[$i]->cni);
                $newuser->setPhone($json->user[$i]->phone);
                $newuser->setArchivage($json->user[$i] = false);
                $newagence->setUser($newuser);
                $this->em->persist($newuser);

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
                //->setNomAgence($json->nomAgence)
                ->setStatut(false);
            for ($i = 0; $i < count($json->compte); $i++) {
                $newcompte = new Comptes();
                $newcompte->setSolde($json->compte[$i]->solde);
                $newagence->setCompte($newcompte);
                $this->em->persist($newcompte);

            }

           // for ($i = 0; $i < count($json->userCreat); $i++) {
            //dd($json->userCreat);
            
            if ($userRepository->find((int)$userCreat->getId())) {

                $objet = ($userRepository->find((int)$userCreat->getId()));
               // dd($objet);
                $newagence->setUserCreat($objet);
                $manage = $this->getDoctrine()->getManager();
                $manage->persist($newagence);
                //}
            }
            //     if ($this->userRepository->find((int)$json->userCreat)) {
            //         //affectation la/les competences au groupe
            //         $ucecreer = $this->userRepository->find((int)$json->userCreat);
            //         //dd($ucecreer);
            //         $newagence->setUserCreat($ucecreer);
            //         $this->em->persist($newagence);

            //     //}
            // }
            if ($json->utilisateur) {

                 
               // dd($users->getProfils()->getLibelle());
               //  if($profilsRepository->find((int)$json->user[$i]->profil)){
                    //  $profil = $profilsRepository->find((int)$json->utilisateur);
                    // //dd($profil->getLibelle());
                    //  if (($profil->getLibelle() !== "ADMIN_PARTENAIRE")){
                    //      return $this->json('cet id user n est pas un utilisater !!!');
                    //  }else{
                        $users=($userRepository->find((int)$json->utilisateur));
                         $newagence->setUser($users);
                     //}
                 //}
                // $newuser->setEmail($json->user[$i]->email);
                // $newuser->setNom($json->user[$i]->nom);
                // $newuser->setPrenom($json->user[$i]->prenom);
                // $newuser->setPassword($json->user[$i]->password);
                // //$password = $json->getPassword();
                // //$users->setPassword($passwordEncoder->encodePassword($users,$password));
                // $newuser->setAdresse($json->user[$i]->adresse);
                // $newuser->setCni($json->user[$i]->cni);
                // $newuser->setPhone($json->user[$i]->phone);
                // $newuser->setArchivage($json->user[$i] = false);
                // $newagence->setUser($newuser);
                // $this->em->persist($newuser);

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


