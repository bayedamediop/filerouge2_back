<?php

namespace App\Controller;

use App\Entity\Tarifs;
use App\Entity\Clirnts;
use App\Entity\Comptes;
use App\Entity\Transactions;
use App\Entity\TypeTransaction;
use App\Repository\UserRepository;
use App\Repository\ClirntsRepository;
use App\Repository\ComptesRepository;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\s;
use App\Controller\TransactionController;
use App\Repository\TransactionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionController extends AbstractController
{

    private $manage;
    private $transactionsRepository;
    private $serializer;
    private $tokenInterface;

    public function __construct(EntityManagerInterface $manage, 
    TransactionsRepository $transactionsRepository)
    {
        $this->manage = $manage;
       // $this->tokenInterface = $tokenInterface;
        $this->transactionsRepository = $transactionsRepository;
    }

    public function Code($code)
    {
        $retrait = $this->getDoctrine()->getRepository(Transactions::class);
        $rett = $retrait->findAll();
        //    var_dump($all); die;
        foreach ($rett as $value) {
            if ($code == $value->getCode()) {
                return true;
            }
        }
    }

    /**
     * @Route (
     *     name="creatTransaction",
     *      path="/api/admin/transactions",
     *      methods={"POST"},
     *     defaults={
     *           "__controller"="App\Controller\TransactionController::creatTransaction",
     *           "__api_ressource_class"=Transactions::class,
     *           "__api_collection_operation_name"="add_transaction"
     *         }
     * )
     */
    public function creatTransaction(Request $request, SerializerInterface $serializer,
                                     EntityManagerInterface $entityManager, UserRepository $userRepository,
                                     ComptesRepository $comptesRepository, ValidatorInterface $validator,TokenStorageInterface $token
                                 ): JsonResponse
    {
        //$userConnecte = $this->getUser();
        $userConnecte = $token->getToken()->getUser();
        //$userId = $this->get('security.context')->getToken()->getUser()->getId();
        //dd($userConnect);

       //dd($userConnecte->getTransactions()); die;
        // $transaction =$serializer->deserialize($request->getContent(), Transactions::class, 'json');
        $transaction = json_decode($request->getContent(), 'json');
        //dd($transaction);
        //dd($transaction['nomComplet']);
        $code = rand(4,1000000000);
        // var_dump($gneneCode);die();
        $date = new \DateTime('now');
        //$code = $gneneCode . date_format($date, 'YmdHi');
        //dd($code);
    

        //appele fonction frais

        $fr = $this->frais($transaction['montant']);
        //dd($fr);
        $partEta = (40 * $fr) / 100;
        $partSys = (30 * $fr) / 100;
        $partDep = (10 * $fr) / 100;
        $partRet = (20 * $fr) / 100;
        //dd($partEta);
        //dd($transaction);
        $slode = $this->getDoctrine()->getRepository(Comptes::class);
        $all = $slode->findAll();
        //dd($userConnecte->getAgences()[0])->getId();
        $compt = ($comptesRepository->find((int)$userConnecte->getAgences()[0]->getCompte()->getId()));
        //dd($compt);
       // if ($transaction['compte']) {
            //$cmop = $transaction['compte'];

           // if ($comptesRepository->find((int)$cmop)) {
                //$objet = ($comptesRepository->find((int)$cmop));
                //dd($objet->getSolde());
                if ($compt->getSolde() <= $transaction['montant']) {
                    $data = [
                        'status' => 500,
                        'message' => ('L\'etat de votre Compte ne vous permet d\'effectue cette transaction votre solde est: ' .
                            $compt->getSolde() . ' et le montant de la transaction est ' .
                            $transaction['montant'] . ' Desolé !!!!§§§§')
                    ];

                }else{
                    $newtransac = new Transactions();
                    $newtransac->setDateDepot($date);
                    // $Transaction->setUserTransaction($users);//le user qui a fais le depot
                    $newtransac->setComission($fr);
                    // dd($fr);
                    $newtransac->setComission($fr);
                    $newtransac->setCodeTransaction($code);
                    $newtransac->setFraisEtat($partEta);
                    $newtransac->setFraisSysteme($partSys);
                    $newtransac->setFraisEnvoie($partDep);
                    $newtransac->setFraisRetrait($partRet);
                    $newtransac->setMontant($transaction['montant']);
                    $entityManager->persist($newtransac);

                            $objet = ($comptesRepository->find((int)$userConnecte->getAgences()[0]->getCompte()->getId()));
                          // dd($objet->getSolde());
                           $so= $objet->setSolde($objet->getSolde() - $transaction['montant'] + $partDep);
                            //dd($so);
                            $objet->addTransaction($newtransac);
                        $entityManager->persist($objet);
                
                 //effectation de user qui fait la deposition de la transaction
                
                 if ($userRepository->find((int)$userConnecte->getId())) {

                    $objet = ($userRepository->find((int)$userConnecte->getId()));
                   // dd($objet);
                    $newtransac->setUserDepot($objet);
                    $entityManager->persist($newtransac);
                    //}
                }
                    $newclient = $serializer->denormalize($transaction['client'], Clirnts::class);
                    $newclient1 = $serializer->denormalize($transaction['client_recu'], Clirnts::class);
                    $entityManager->persist($newclient);
                    $entityManager->persist($newclient1);
                    $newtransac->setClientEnvoie($newclient);
                    $newtransac->setClientRecu($newclient1);
              
                    // for ($i = 0; $i < count($transaction['client']); $i++) {
                    //     //ccreation du client a envoie
                    //     $newclient = new Clirnts();
                    //     $newclient->setNomComplet($transaction['client'][$i]['nomComplet']);
                    //     $newclient->setPhone($transaction['client'][$i]['phone']);
                    //     $newclient->setCni($transaction['client'][$i]['cni']);
                    //     $newtransac->setClientEnvoie($newclient);
                    //     $entityManager->persist($newclient);
                    // }
                    // for ($i = 0; $i < count($transaction['client_recu']); $i++) {
                    //     //ccreation du client a envoie
                    //     $newclient = new Clirnts();
                    //     $newclient->setNomComplet($transaction['client_recu'][$i]['nomComplet']);
                    //     $newclient->setPhone($transaction['client_recu'][$i]['phone']);
                    //    // $newclient->setCni($transaction['client'][$i]['cni']);
                    //     $newtransac->setClientRecu($newclient);
                    //     $entityManager->persist($newclient);
                    // }
                    // dd($mypart);
                    //mis a jour Du Compte

                    $entityManager->persist($newclient);

                    $entityManager->flush();
                    $data = [
                        'status' => 200,
                        'message' => 'Vous Avez Efeectue Une Operation de Transaction  De ' . $transaction['montant'] . ' Frais: ' . $fr .
                            ' Voici Le Code De La Transaction ' . $code . ': '
                    ];
                }
           // }
            //}
            //$entityManager->persist($objet);


        return new JsonResponse($data, 200);

    }
    // ___________________________calcule frais__________________________
    
 // ___________________________calcule frais__________________________

    /**
     * @Route (
     *     name="calculerfrais",
     *      path="/api/admin/frais",
     *      methods={"GET"},
     *     defaults={
     *           "__controller"="App\Controller\TransactionController::calculerfrais",
     *           "__api_ressource_class"=Transactions::class,
     *           "__api_collection_operation_name"="calculer_frais"
     *         }
     * )
     */
    public function calculerfrais( Request $request)
    {
        $objet = json_decode($request->getContent(), 'json');
        
        return $this->json($this->frais($objet['montant']),200);
    }




    public function frais($montant)
    {
        $frai = $this->getDoctrine()->getRepository(Tarifs::class);
        $all = $frai->findAll();
        //var_dump($all); die;
        foreach ($all as $val) {

            if ($val->getBornInf() <= $montant && $val->getBornsupp() >= $montant) {
                return $val->getFrais();
            }
        }
    }

     //_________________ recherche un transaction via code de transaction_______________________________

    /**
     * @Route (
     *     name="recherchetransaction",
     *      path="/api/admin/transactions/{code}",
     *      methods={"GET"},
     *     defaults={
     *           "__controller"="App\Controller\TransactionController::recherchetransaction",
     *           "__api_ressource_class"=Transactions::class,
     *           "__api_collection_operation_name"="recherche_transaction"
     *         }
     * )
     */
    public function recherchetransaction(Request $request,$code,TransactionsRepository $transactionsRepository,
                                UserRepository $userRepository,
                           ClirntsRepository $clirntsRepository,ComptesRepository $comptesRepository,
                            SerializerInterface $serializer,TokenStorageInterface $token)
    {
        //dd('oki');
       //dd($rett);
        $transation = $transactionsRepository->findTransaction($code);
        if ($transation) {

            if ($transation->getDateRetrait() !== null) {
                return $this->json('cette transaction est deja retiret',400);
            } else {
                return $this->json($transation,200);
             }
        }else {
            return $this->json('ce code de transaction n est pas correcte !!!!',400);
         }
        
    }

    //_________________ recupre un transaction via code de transaction_______________________________

    /**
     * @Route (
     *     name="retiret",
     *      path="/api/admin/transactions/{code}",
     *      methods={"PUT"},
     *     defaults={
     *           "__controller"="App\Controller\TransactionController::retiret",
     *           "__api_ressource_class"=Transactions::class,
     *           "__api_collection_operation_name"="retiret_Transaction"
     *         }
     * )
     */
    public function retiret(Request $request,$code,TransactionsRepository $transactionsRepository,
                                UserRepository $userRepository,
                           ClirntsRepository $clirntsRepository,ComptesRepository $comptesRepository,
                            SerializerInterface $serializer,TokenStorageInterface $token)
    {
        //dd('oki');
       //dd($rett);
        $transation = $transactionsRepository->findTransaction($code);
       //dd($transation->getClientRecu()->getCni());
        //dd($transation);
           

       
        $userConnecte = $token->getToken()->getUser();
        //dd($userConnecte);
        if ($transation) {
            $fr = $this->frais($transation->getMontant());
            $partRet = (20 * $fr) / 100;
            if ($transation->getDateRetrait() !== null) {
                $data = [
                    'status' => 200,
                    'message' => 'Cete transation est est deja retire!!!! '
                ];
            } else {
                $transation->setDateRetrait(new \DateTime());
                $this->manage->persist($transation);
                // user qui retire de l' argen
               // dd($userConnecte->getId());
                if ($userRepository->find((int)$userConnecte->getId())) {

                    $objet = ($userRepository->find((int)$userConnecte->getId()));
                   // dd($objet);
                    $transation->setUserRetrait($objet);
                    $manage = $this->getDoctrine()->getManager();
                    $manage->persist($transation);
                    //}
                }
                $objet = ($comptesRepository->find((int)$userConnecte->getAgences()[0]->getCompte()->getId()));
                //dd($objet);
                 $solde = $objet->setSolde($objet->getSolde() + $transation->getMontant() +$partRet);
                  //dd($so);
                  $solde->addTransaction($transation);
            
                $manage = $this->getDoctrine()->getManager();
                $manage->persist($solde);
                $doonne = json_decode($request->getContent());
                
                    $manage = $this->getDoctrine()->getManager();
                    $manage->persist($transation);
              //  }
                $doonneClient = json_decode($request->getContent(),'json');
             //dd($doonneClient);
               $newclient = $serializer->denormalize($doonneClient, Clirnts::class);
               //$newclient1 = $serializer->denormalize($transaction['client_recu'], Clirnts::class);
               //$entityManager->persist($newclient);
              // $entityManager->persist($newclient1);
               $manage = $this->getDoctrine()->getManager();
                  $manage->persist($newclient);
               //$newtransac->setClientEnvoie($newclient);
              // $newtransac->setClientRecu($newclient1);
              //  if ($doonneClient['client']) {
                //   for ($i=0; $i < count($doonneClient['client']); $i++) { 
                //    // dd();
                //     $objet = $transation->getClientRecu();
                //     $objet->setCni($doonneClient['client'][$i]['cni']);
                //   }
                   
                           
              //  }
                $data = [
                    'status' => 200,
                    'message' => 'Vous Avez Efeectue Une Operation de retire '

                ];
                $this->manage->flush();
            }
        } else {
            return $this->json("Cette code de Transaction n'est pas bonne!!!");
        }
        return new JsonResponse($data, 200);
    }


    //_________________ annuler un transaction_______________________________


    /**
     * @Route (
     *     name="deletTransaction",
     *      path="/api/admin/transactions/{code}",
     *      methods={"GET"},
     *     defaults={
     *           "__controller"="App\Controller\TransactionController::deletTransaction",
     *           "__api_ressource_class"=Transactions::class,
     *           "__api_collection_operation_name"="delet_Transaction"
     *         }
     * )
     */
    // public function deletTransaction(Request $request, $code,  TransactionsRepository $transactionsRepository,
    //                 EntityManagerInterface $manage, ComptesRepository $comptesRepository){
    //     $transation = $transactionsRepository->findTransaction($code);
    //     //dd($transaction);
    //     $usedepot=$transation->getUserDepot()->getId();
    //     dd($userdepot);
    //     $userConnect=$this->getUser()->getId();
    //    // dd($transation->getDateRetrait());
    //      //dd($transation->getCopmte()->getSolde());

    //    //$ $transation->getMontant()[0];
    //     //dd($transation->getMontant());
    //     if ($userConnect === $usedepot ) {
    //         if ($transation->getDateRetrait() !== null){
    //           // dd( $transation->setFraisRetrait());
    //             return $this->json("Cette code de Transaction est deja retiret!!!");
    //         }else{
    //             $transation->getCopmte()->setSolde($transation->getCopmte()->getSolde()-$transation->getMontant());
    //             $transation->setFraisEtat(0.0) ;
    //             $transation->setDateRetrait(new \DateTime()) ;
    //             $transation->setFraisRetrait(0) ;
    //             $transation->setFraisSysteme(0.0) ;
    //              $manage->persist($transation);
                
    //         }
    //         $manage->flush();
    //         $data = [
    //             'status' => 200,
    //             'message' => 'Cete transation est annuler avec success!!!! '
    //         ];
    //     }else {
    //         $data = [
    //             'status' => 200,
    //             'message' => 'Desole vous ne pouvez pas cette transactio!!! '
    //         ];
    //     }
    // //    $delete=($transation->getMontantDepot());
    // //       $solde= ($transation->getCompte()->getSolde());
    // //       $result= $transation->getCompte()->setSolde($solde - $delete) ;
    // //        $manage->persist($result);
    // //        $transation->getCompte()->removeDepot($transation);
    // //      $transation->getUsers()->removeDepot($transation);
    // //     // $transation->removeDepot($transation);
    // //        $manage->remove($transation);

    // //        $manage->flush();
    //     // dd($transation);
    //     return new JsonResponse($data, 200);
    // }

    //_________________ les  transactions d un compte_______________________________

    /**
     * @Route (
     *     name="transationduncomte",
     *      path="/api/admin/transactions/{id}",
     *      methods={"GET"},
     *     defaults={
     *           "__controller"="App\Controller\TransactionController::transationduncomte",
     *           "__api_ressource_class"=Transactions::class,
     *           "__api_collection_operation_name"="transationd_uncomte"
     *         }
     * )
     */
    public function transationduncomte(Request $request,$code,TransactionsRepository $transactionsRepository,
                                UserRepository $userRepository,
                          TokenStorageInterface $token)
    {
        //dd('oki');
       //dd($rett);
       $userConnecte = $token->getToken()->getUser()->getId();
       $objettransaction = ($userRepository->find((int)$userConnecte));
       dd($objettransaction);
       //dd($transation->getClientRecu()->getCni());
        //dd($transation);
           

       
        
        //dd($userConnecte);
    }

}

