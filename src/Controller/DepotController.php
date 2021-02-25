<?php

namespace App\Controller;

use App\Entity\Comptes;
use App\Entity\Depots;
use ApiPlatform\Core\Filter\Validator\ValidatorInterface;
use App\Entity\Transactions;
use App\Repository\ComptesRepository;
use App\Repository\DepotsRepository;
use App\Repository\TransactionsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\VarDumper\Cloner\Data;

class DepotController extends AbstractController
{
    private $manage;
    private $userRepository;
    private $serializer;

    public function __construct(EntityManagerInterface $manage, SerializerInterface $serializer)
    {
        $this->manage = $manage;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     name="creatdepot",
     *      path="/api/admin/depots",
     *      methods={"POST"},
     *     defaults={
     *           "__controller"="App\Controller\DepotsController::creatdepot",
     *           "__api_ressource_class"=Depots::class,
     *           "__api_collection_operation_name"="add_depot"
     *         }
     * )
     */
    public function creatdepot(Request $request, ComptesRepository $comRepository,
                               ComptesRepository $comptesRepository, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $json = json_decode($request->getContent(), 'json');

        $slode = $this->getDoctrine()->getRepository(Comptes::class);
        //dd($slode);
        $all = $slode->findAll();
        // dd($all);
        $ref = new Depots();
        $ref->setDateDepot(new \DateTime());
        $ref->setMontantDepot($json['montantDept']);
        if ($json['user']) {
            $cmop = $json['user'];
            //dd($cmop);
            // for ($i=0; $i <count($cmop); $i++) {
            if ($userRepository->find((int)$cmop)) {
                $objet = ($userRepository->find((int)$cmop));
                $ref->setUsers($objet);
                $manage = $this->getDoctrine()->getManager();
                $manage->persist($ref);
                //}
            }
            $manage = $this->getDoctrine()->getManager();
            $manage->persist($ref);
        }
        if ($json['compte']) {
            $cmop = $json['compte'];
            for ($i = 0; $i < count($cmop); $i++) {
                if ($comRepository->find((int)$cmop[$i])) {
                    $objet = ($comRepository->find((int)$cmop[$i]));
                    //dd($objet->getSolde());
                    $objet->setSolde($objet->getSolde() + $json['montantDept']);
                    //dd($objet);
                    $ref->setCompte($objet);
                }
            }
            $manage = $this->getDoctrine()->getManager();
            $manage->persist($ref);
        }
        $manage->flush();
        return $this->json("success", 201);
    }


    /**
     * @Route (
     *     name="getuserandcopmt",
     *      path="/api/admin/depot/{idd}/user/{idu}",
     *      methods={"GET"},
     *     defaults={
     *           "__controller"="App\Controller\DepotsController::getuserandcopmt",
     *           "__api_ressource_class"=Depots::class,
     *           "__api_collection_operation_name"="get_userandcopmt"
     *         }
     * )
     */
    public function getuserandcopmt(DepotsRepository $depotsRepository,$idd,$idu)
    {
        $test = $depotsRepository->ifuserAndCompteInDepot($idd,$idu);
        if ($test) {
            return $this->json($test,200,[],["groups"=>"usercompte:read"]);
        }else{
            return $this->json("User ou Comtpt competence inexistant");
        }
    }
}
