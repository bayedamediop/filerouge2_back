<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Agences;
use App\Entity\User;
use App\Repository\AgencesRepository;
use App\Repository\ComptesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function creatAgence(Request $request, SerializerInterface $serialize, UserPasswordEncoderInterface $encoder)
    {
        $json = json_decode($request->getContent());
        //dd($json,401);

        //verifions s'il faut crée le groupe oubien l'affecté des competences
        if (isset($json->id)) {
            $agences = $this->agence->find($json->id);

        } else {
            $agences = null;

        }
        // dd($agences);
        if ($agences != null) {

            //dans le cas ou agence existe deja
            for ($i = 0; $i < count($json->comptes); $i++) {
                if (isset($json->compte[$i]->id)) {
                    //affectation la/les competences au groupe
                    $comptes = $this->compte->find($json->comptes[$i]->id);
                    $agences->setCompte($comptes);
                }
            }
            //dd($json->user);
            //for ($i=0; $i < count($json->user); $i++) {
            // $users = $this->serializer->denormalize($json->user, "App\Entity\User");
            // $password = $users->getPassword();
            //creation de la user
//                    $newuser = new User();
//                $newuser->setEmail($json->user[$i]->email)
//                        ->setNom($json->user[$i]->nom)
//                        ->setPrenom($json->user[$i]->prenom)
//                        ->setPassword($json->user[$i]="diop")
//                        ->setProfils($json->user[$i]->profil)
//                        ->setAdresse($json->user[$i]->adresse)
//                        ->setCni($json->user[$i]->cni)
//                        ->setPhone($json->user[$i]->phone)
//                        ->setArchivage($json->user[$i]=false);
            //$agences->setUser($users);

            //}
            $this->em->persist($agences);
        } else { //si groupe de competence n'existe on crée
            $newagence = new Agences();
            $newagence->setNumAgence(rand(9, 1000000000))
                ->setAdresseAgence($json->adresse)
                ->setStatut(false);
            for ($i = 0; $i < count($json->comptes); $i++) {
                if (isset($json->compte[$i]->id)) {
                    //affectation la/les competences au groupe
                    $comptes = $this->compte->find($json->comptes[$i]->id);

                    $newagence->setCompte($comptes);
                    $this->em->persist($newagence);
                }
            }
            for ($i = 0; $i < count($json->userCreat); $i++) {
                if (isset($json->compte[$i]->id)) {
                    //affectation la/les competences au groupe
                    $comptes = $this->userRepository->find($json->userCreat[$i]->id);
                    $comptes->addAgence($newagence);
                    $this->em->persist($comptes);

                }
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
                //dd($json->user[$i]->email);
                $newuser->setEmail($json->user[$i]->email);
                $newuser->setNom($json->user[$i]->nom);
                $newuser->setPrenom($json->user[$i]->prenom);
                $newuser->setPassword($json->user[$i]->password);


                //$newuser->setPassword($this->$encoder->encodePassword($json->user[$i]->password));
                $newuser->setAdresse($json->user[$i]->adresse);
                $newuser->setCni($json->user[$i]->cni);
                $newuser->setPhone($json->user[$i]->phone);
                $newuser->setArchivage($json->user[$i] = false);
                $newuser->setProfils($json->user[$i] = `/api/profils/10`);
                $this->em->persist($newuser);
                $newagence->setUser($newuser);

            }
            $this->em->persist($newagence);
        }
        //validation groupe competences


        //$this->em->persist($agences);


        $this->em->flush();
        return $this->json('added succesfully', Response::HTTP_OK);
    }
}


