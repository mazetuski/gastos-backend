<?php
/**
 * Created by PhpStorm.
 * User: mazetuski
 * Date: 20/05/18
 * Time: 20:39
 */

namespace App\Controller;


use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * @Route("/api")
 * Class ApiController
 * @package App\Controller
 */
class ApiController extends FOSRestController
{
    /**
    * @Get("/users")
    *
    */
    public function getUsersAction(Request $request){
        $serializer = $this->get('jms_serializer');
        return new JsonResponse($serializer->serialize($this->getDoctrine()->getManager()->getRepository('App:User')->findAll(), 'json'));
    }
}