<?php

namespace App\Controller;

use App\Entity\Clirnts;
use App\Entity\Comptes;
use App\Entity\Tarifs;
use App\Entity\Transactions;
use App\Entity\TypeTransaction;
use App\Repository\ClirntsRepository;
use App\Repository\ComptesRepository;
use App\Repository\TransactionsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function Symfony\Component\String\s;

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
        $code = rand(4,1000000000);
        // var_dump($gneneCode);die();
        $date = new \DateTime('now');
        //$code = $gneneCode . date_format($date, 'YmdHi');
        //dd($code);
        $users = $this->getUser();


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

        if ($transaction['compte']) {
            $cmop = $transaction['compte'];

            if ($comptesRepository->find((int)$cmop)) {
                $objet = ($comptesRepository->find((int)$cmop));
                //dd($objet->getSolde());
                if ($objet->getSolde() <= $transaction['montant']) {
                    $data = [
                        'status' => 500,
                        'message' => ('L\'etat de votre Compte ne vous permet d\'effectue cette transaction votre solde est: ' .
                            $objet->getSolde() . ' et le montant de la transaction est ' .
                            $transaction['montant'] . ' Desolé !!!!§§§§')
                    ];

                }else{
                    $newtransac = new Transactions();
                    $newtransac->setDateDepot($date);
                    $newtransac->setComission($fr);
                    $newtransac->setCodeTransaction($code);
                    $newtransac->setFraisEtat($partEta);
                    $newtransac->setFraisSysteme($partSys);

                    $newtransac->setMontant($transaction['montant']);
                    $entityManager->persist($newtransac);

                    // creation de nouveau type transsaction;
                      $newtype = new TypeTransaction();
                    $newtype->setLibelle("depot")
                          ->setFrais($partDep)
                        ->setDateTransaction(new \DateTime());
                    $newtransac->setTypeTransaction($newtype);
                    $entityManager->persist($newtype);

                    //$mypart=$transaction->getTarifs()-$transaction->getPartDep();
                    if ($transaction['compte']) {
                        // dd($transaction['compte']);
                        // dd($competencerepo->findBy(['id'=>(int)$json['competence']]));
                        $cmop = $transaction['compte'];
                        //dd($cmop);
                        // for ($i = 0; $i < count($cmop); $i++) {
                        if ($comptesRepository->find((int)$cmop)) {
                            $objet = ($comptesRepository->find((int)$cmop));
                            // dd($objet);
                            $objet->setSolde($objet->getSolde() - $transaction['montant']);
                            $objet->addTransaction($newtransac);

                        }
                        //}
                        $entityManager->persist($objet);
                    }
                    if ($transaction['user']) {
                        // dd($competencerepo->findBy(['id'=>(int)$json['competence']]));
                        $cmop = $transaction['user'];
                        //dd($cmop);
                        //for ($i = 0; $i < count($cmop); $i++) {
                        if ($userRepository->find((int)$cmop)) {
                            $objet = ($userRepository->find((int)$cmop));
                            $newtransac->addUser($objet);
                            $entityManager->persist($newtransac);
                        }
                        //}

                    }
                    //dd($transaction['client']['nomComplet']);
                    for ($i = 0; $i < count($transaction['client']); $i++) {
                        //ccreation du client a envoie
                        $newclient = new Clirnts();
                        $newclient->setNomComplet($transaction['client'][$i]['nomComplet']);
                        $newclient->setPhone($transaction['client'][$i]['phone']);
                        $newclient->setCni($transaction['client'][$i]['cni']);
                        $newtransac->setClientEnvoie($newclient);
                        $entityManager->persist($newclient);
                    }
                    for ($i = 0; $i < count($transaction['client_recu']); $i++) {
                        //ccreation du client a envoie
                        $newclient = new Clirnts();
                        $newclient->setNomComplet($transaction['client_recu'][$i]['nomComplet']);
                        $newclient->setPhone($transaction['client_recu'][$i]['phone']);
                       // $newclient->setCni($transaction['client'][$i]['cni']);
                        $newtransac->setClientRecu($newclient);
                        $entityManager->persist($newclient);
                    }
                    // dd($mypart);
                    //mis a jour Du Compte

                    $entityManager->persist($newclient);
                    $entityManager->flush();
                    $data = [
                        'status' => 200,
                        'message' => 'Vous Avez Efeectue Une Operation de Transaction  De ' . $transaction['montant'] . ' Frais: ' . $fr .
                            ' Voici Le Code De La Transaction ' . $code . ':'
                    ];
                }
            }
            //}
            $entityManager->persist($objet);
        }else{
            $data = [
                'status' => 200,
                'message' => 'Le compte n existe pas'
            ];
        }


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
                           ClirntsRepository $clirntsRepository,ComptesRepository $comptesRepository, SerializerInterface $serializer)
    {
        $transation = $transactionsRepository->findTransaction($code);
      // dd($transation);
        //dd($transation->getMontant());
        $fr = $this->frais($transation->getMontant());
        //dd($fr);
        $partRet = (20 * $fr) / 100;
        if ($transation) {

            if ($transation->getStatut() === true) {
                $data = [
                    'status' => 200,
                    'message' => 'Cete transation est est deja retire!!!! '
                ];
            } else {
                $transation->setDateRetrait(new \DateTime());
                $this->manage->persist($transation);
                //dd($transation);
                $newtype = new TypeTransaction();
                $newtype->setLibelle("retrait")
                    ->setFrais($partRet)
                    ->setDateTransaction(new \DateTime());
                $transation->setTypeTransaction($newtype);
                $manage = $this->getDoctrine()->getManager();
                $manage->persist($newtype);
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
         // dd($doonneClient['client']);
                if ($doonneClient['client']) {
                    $objet = ($clirntsRepository->find((int)$doonneClient['client']));
                           //dd($objet);
//                    for ($i = 0; $i < count($doonneClient['client']); $i++) {
//                       //dd( $doonneClient['client'][$i]['cni']);
//                        $newclient=$transation->getClientRecu()->getCni($doonneClient['client'][$i]['cni']);
//                        //ccreation du client a envoie
//                       // $newclient = new Clirnts();
//                        //$newclient->setNomComplet($doonneClient['client'][$i]['nomComplet']);
//                       // $newclient->setPhone($doonneClient['client'][$i]['phone']);
//                       // $newclient->$transation->getClientRecu()->getCni($newclient->setCni($doonneClient['client'][$i]['cni']));
//                        $transation->ad($newclient);
//                       $manage->persist($transation);
                   //}
                }
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

}

