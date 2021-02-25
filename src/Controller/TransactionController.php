<?php

namespace App\Controller;

use App\Entity\Clirnts;
use App\Entity\Comptes;
use App\Entity\Tarifs;
use App\Entity\Transactions;
use App\Repository\ComptesRepository;
use App\Repository\TransactionsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransactionController extends AbstractController
{

    private $manage;
    private $transactionsRepository;
    private $serializer;

    public function __construct(EntityManagerInterface $manage, TransactionsRepository $transactionsRepository)
    {
        $this->manage = $manage;
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
                                     ComptesRepository $comptesRepository, ValidatorInterface $validator): JsonResponse
    {
        // $transaction =$serializer->deserialize($request->getContent(), Transactions::class, 'json');
        $transaction = json_decode($request->getContent(), 'json');
        //dd($transaction);
        //dd($transaction['nomComplet']);
        $gneneCode = rand(2, 100);
        // var_dump($gneneCode);die();
        $date = new \DateTime('now');
        $code = $gneneCode . date_format($date, 'YmdHi');
        //dd($code);
        $users = $this->getUser();


        //appele fonction frais

        $fr = $this->frais($transaction['montant']);
        //dd($fr);
        $partEta = (40 * $fr) / 100;
        $partSys = (30 * $fr) / 100;
        $partDep = (20 * $fr) / 100;
        $partRet = (10 * $fr) / 100;
        //dd($partEta);
        //dd($transaction);
        $slode = $this->getDoctrine()->getRepository(Comptes::class);
        $all = $slode->findAll();
//
        foreach ($all as $val) {
            if ($val->getSolde() <= $transaction['montant']) {
                $data = [
                    'status' => 500,
                    'message' => ('L\'etat de votre Compte ne vous permet d\'effectue cette transaction votre solde est: ' .
                        $val->getSolde() . ' et le montant de la transaction est ' .
                        $transaction['montant'] . ' Desolé !!!!')
                ];
            } else {


                $newtransac = new Transactions();
                $newtransac->setDateDepot($date);
                // $Transaction->setUserTransaction($users);//le user qui a fais le depot
                //$newtransac->setComission($fr);
                // dd($fr);
                $newtransac->setComission($fr);
                $newtransac->setCodeTransaction($code);
                $newtransac->setFraisEtat($partEta);
                $newtransac->setFraisSysteme($partSys);
                $newtransac->setFraisEnvoie($partDep);
                $newtransac->setFraisRetrait($partRet);
                $newtransac->setMontant($transaction['montant']);
                $entityManager->persist($newtransac);

                //$mypart=$transaction->getTarifs()-$transaction->getPartDep();
                if ($transaction['compte']) {
                    // dd($competencerepo->findBy(['id'=>(int)$json['competence']]));
                    $cmop = $transaction['compte'];
                    //dd($cmop);
                    for ($i = 0; $i < count($cmop); $i++) {
                        if ($comptesRepository->find((int)$cmop[$i])) {
                            $objet = ($comptesRepository->find((int)$cmop[$i]));
                            // dd($objet->setSolde());
                            $objet->setSolde($objet->getSolde() - $transaction['montant']);
                            $objet->addTransaction($newtransac);

                        }
                    }
                    $entityManager->persist($objet);
                }
                if ($transaction['user']) {
                    // dd($competencerepo->findBy(['id'=>(int)$json['competence']]));
                    $cmop = $transaction['user'];
                    //dd($cmop);
                    //for ($i = 0; $i < count($cmop); $i++) {
                        if ($userRepository->find((int)$cmop)) {
                            $objet = ($userRepository->find((int)$cmop));
                            $objet->addTransaction($newtransac);

                        }
                    //}
                    $entityManager->persist($objet);
                }
                //dd($transaction['client']->nomComplet);
                for ($i = 0; $i < count($transaction['client']); $i++) {
                    //ccreation du client a envoie
                    $newclient = new Clirnts();
                    $newclient->setNomComplet($transaction['client'][$i]['nomComplet']);
                    $newclient->setPhone($transaction['client'][$i]['phone']);
                    $newclient->setCni($transaction['client'][$i]['cni']);
                    $newtransac->addClient($newclient);
                    $entityManager->persist($newclient);
                }
                // dd($mypart);
                //mis a jour Du Compte
                $solde = ($val->getSolde());
                //dd($solde);
                $newsolde = $solde - $transaction['montant'];
                // dd($newsolde);
                $solde = ($val->getSolde($newsolde));
                $entityManager->persist($newclient);

                // $entityManager->flush();
                $data = [
                    'status' => 200,
                    'message' => 'Vous Avez Efeectue Une Operation de Transaction  De ' . $transaction['montant'] . ' Frais: ' . $fr .
                        'le montant total esr de :' . $transaction['montant'] . +$fr . 'FRCFA' .
                        ' Voici Le Code De La Transaction ' . $code . ': '
//.
//                ' Votre Nouvou Solde pour le compte Numero '.$repCompt->getNumero().' Est : '.$repCompt->getSolde()
                ];
            }
        }

        $entityManager->flush();
        return new JsonResponse($data, 200);

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


    //_________________ recupre un transaction via code de transaction_______________________________

    /**
     * @Route (
     *     name="transaction",
     *      path="/api/admin/transactions/{codet}",
     *      methods={"PUT"},
     *     defaults={
     *           "__controller"="App\Controller\TransactionController::transaction",
     *           "__api_ressource_class"=Transactions::class,
     *           "__api_collection_operation_name"="recupere_Transaction"
     *         }
     * )
     */
    public function transaction(Request $request,$codet,ComptesRepository $comptesRepository, SerializerInterface $serializer)
    {
        //dd('oki');

        $transation = $this->transactionsRepository->findTransactionBycode($codet);
       //dd($transation);

        if ($transation) {
            if ($transation->getDateRetrait() !== null) {
                return $this->json("Cette Transaction est déja retire ");
            } else {
                $transation->setDateRetrait(new \DateTime());
                $this->manage->persist($transation);
                //dd($transation);
                $doonne = json_decode($request->getContent());
                //dd($doonne);
                if ($doonne->compte) {
                    //dd($doonne->compte);
                    //for ($i = 0; $i < count($cmop); $i++) {
                        if ($comptesRepository->find((int)$doonne->compte)) {
                            $objet = ($comptesRepository->find((int)$doonne->compte));
                            //dd($objet->getSolde());
                            $objet->setSolde($objet->getSolde() + $transation->getMontant());
                            //dd($objet);
                            $transation->setCopmte($objet);
                        }
                   // }
                    $manage = $this->getDoctrine()->getManager();
                    $manage->persist($transation);
                }
                $doonneClient = json_decode($request->getContent(),'json');

                if ($doonneClient['client']) {
                    for ($i = 0; $i < count($doonneClient['client']); $i++) {
                        //ccreation du client a envoie
                        $newclient = new Clirnts();
                        $newclient->setNomComplet($doonneClient['client'][$i]['nomComplet']);
                        $newclient->setPhone($doonneClient['client'][$i]['phone']);
                        $newclient->setCni($doonneClient['client'][$i]['cni']);
                        $transation->addClient($newclient);
                        $manage->persist($newclient);
                    }
                }
                $this->manage->flush();
            }
        } else {
            return $this->json("Cette code de Transaction n'est pas bonne!!! ");
        }
    }

}

