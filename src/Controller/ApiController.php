<?php
/**
 * Created by PhpStorm.
 * User: mazetuski
 * Date: 20/05/18
 * Time: 20:39
 */

namespace App\Controller;


use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * @Route("/api")
 * Class ApiController
 * @package App\Controller
 */
class ApiController extends FOSRestController
{

    /**
     *  @Get("/expenses")
     *
     */
    public function getExpensesByUser(Request $request){
        $response = new JsonResponse();
        $status = 200;
        // get user loged with jwt token
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $expenseRepo = $em->getRepository('App:Expense');
        // get all expenses by user
        $expensesByUser = $expenseRepo->findByUser($user);
        if(!$expensesByUser){
            // Not found
            $status = 404;
        }
        $serializer = $this->get('jms_serializer');
        $response->setStatusCode($status);
        $response->setContent($serializer->serialize($expensesByUser, 'json'));
        return $response;
    }

    /**
     *  @Get("/expensesByTime")
     *  @QueryParam(name="dateStart", requirements="\d\d\d\d-\d\d-\d\d", default="2017-01-01", description="First date of expenses filtered")
     *  @QueryParam(name="dateEnd", requirements="\d\d\d\d-\d\d-\d\d", default="2018-01-01", description="Last date of expenses filtered")
     *
     */
    public function getExpensesByTimeAndUser(Request $request){
        $response = new JsonResponse();
        $status = 200;
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');
        $dateStart = new \DateTime($dateStart);
        $dateEnd = new \DateTime($dateEnd);
        // get user loged with jwt token
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        /** @var ExpenseRepository $expenseRepo */
        $expenseRepo = $em->getRepository('App:Expense');
        // get all expenses by user
        $expensesByUser = $expenseRepo->getExpenseByTimeAndUser($user, $dateStart, $dateEnd);
        if(!$expensesByUser){
            // Not found
            $status = 404;
        }
        $serializer = $this->get('jms_serializer');
        $response->setStatusCode($status);
        $response->setContent($serializer->serialize($expensesByUser, 'json'));
        return $response;
    }
}