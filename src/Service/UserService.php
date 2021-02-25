<?php
namespace App\Service;




use App\Entity\User;
use App\Repository\ProfilsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService

{
    /**
     * @var SerializerInterface
     */
    private $serialize;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    private $encoder;
    private $profileRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PostController constructor.
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em,
                                ValidatorInterface $validator, ProfilsRepository  $profileRepository, UserPasswordEncoderInterface $encoder )
    {
        $this->serialize = $serializer ;
        $this->validator = $validator ;
        $this->encoder = $encoder ;
        $this->em = $em ;
        $this->profileRepository = $profileRepository ;
    }

    /**
     * put image of user
     * @param Request $request
     * @param string|null $fileName
     * @return array
     */
    public function PutUser(Request $request, string $fileName = null)
    {
        $row = $request->getContent();
        $delimitor = "multipart/form-data; boundary=";
        $boundary = "--".explode($delimitor, $request->headers->get("content-type"))[1];
        $elements = str_replace([$boundary,'Content-Disposition: form-data;',"name="],"",$row);
        //dd($elements);
        $tabElements = explode("\r\n\r\n", $elements);
        //dd($tabElements);
        $data = [];

        for ($i = 0; isset($tabElements[$i+1]); $i++)
        {
            $key = str_replace(["\r\n",' "','"'],'',$tabElements[$i]);
            //dd($key);
            if (strchr($key, $fileName))
            {
                $file = fopen('php://memory', 'r+');
                fwrite($file, $tabElements[$i+1]);
                rewind($file);
                $data[$fileName] = $file;
            }else {
                $val = str_replace(["\r\n",'--'], '', $tabElements[$i+1]);
                $data[$key] = $val;
            }
        }
        //dd($data);
        return $data;
    }
    /**
     *  cette fonction gere le fichier excel
     */

}

?>
