<?php

namespace App\Controller;
use App\Entity\Comptes;
use App\Repository\ComptesRepository;
use App\Repository\DepotsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompteController extends AbstractController
{
    /**
     * @Route("/compte", name="compte")
     */
    public function index(): Response
    {
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
        ]);
    }

    /**
     * @Route (
     *     name="getCoompteByNumero",
     *      path="/api/admin/comptes/{id}/transactions",
     *      methods={"GET"},
     *     defaults={
     *           "__controller"="App\Controller\CompteController::getCoompteByNumero",
     *           "__api_ressource_class"=Comptes::class,
     *           "__api_collection_operation_name"="get_CoompteByNumero"
     *         }
     * )
     */
    public function getCoompteByNumero($id, ComptesRepository $comptesRepository)
    {
        $test = $comptesRepository->find($id);
       //dd($test->getTransactions()[0])-;
        if ($test) {
            return $this->json($test,200,[],["groups"=>"mestransactions:read"]);
        }else{
            return $this->json(" Le nu numero de ce  Comtpt inexistant");
        }
    }
    // _______________________________archiver un agence est ces utilisateurs-------------------------
    /**
     * @Route (
     *     name="archivage",
     *      path="/api/admin/comptes/{id}",
     *      methods={"GET"},
     *     defaults={
     *           "__controller"="App\Controller\CompteController::archivage",
     *           "__api_ressource_class"=Comptes::class,
     *           "__api_collection_operation_name"="archivage_agence"
     *         }
     * )
     */
    public function archivage($id,UserRepository $userRepository,EntityManagerInterface $manager)
    {
        $user = $userRepository->find($id);
        $user->setArchivage(true);
        $manager->flush();
        return new JsonResponse("User Archivé",200,[],true);

    }
    


    // /**
    //  * @Route (
    //  *     name="deletlastDepot",
    //  *      path="/api/admin/depots/{num}",
    //  *      methods={"PUT"},
    //  *     defaults={
    //  *           "__controller"="App\Controller\CompteController::archivage",
    //  *           "__api_ressource_class"=Comptes::class,
    //  *           "__api_collection_operation_name"="delet_lastDepot"
    //  *         }
    //  * )
    //  * 
    //  */
    // public function deletlastDepot(Request $request, $num, DepotsRepository $depots, ComptesRepository $comptesRepository){
    //     $transation = $comptesRepository->findBy($num, 'DESC');
    //     dd($transation);
    //     $this->getUser()->getId();
    //      //dd($transation);

    // }

}
