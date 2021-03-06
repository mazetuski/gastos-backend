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
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api")
 * Class ApiController
 * @package App\Controller
 */
class ApiController extends FOSRestController
{


    /**
     * @Get("/expenses")
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the expenses of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=App\Entity\Expense::class))
     *     )
     * )
     */
    public function getExpensesByUser(Request $request)
    {
        $response = new JsonResponse();
        $status = 200;
        // get user loged with jwt token
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $expenseRepo = $em->getRepository('App:Expense');
        // get all expenses by user
        $expensesByUser = $expenseRepo->findByUser($user);
        if (!$expensesByUser) {
            // Not found
            $status = 404;
        }
        $serializer = $this->get('jms_serializer');
        $response->setStatusCode($status);
        $response->setContent($serializer->serialize($expensesByUser, 'json'));
        return $response;
    }

    /**
     * @Get("/expensesByTime")
     * @QueryParam(name="dateStart", requirements="\d\d\d\d-\d\d-\d\d", default="2017-01-01", description="First date of expenses filtered")
     * @QueryParam(name="dateEnd", requirements="\d\d\d\d-\d\d-\d\d", default="2018-01-01", description="Last date of expenses filtered")
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the expenses of an user by time",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=App\Entity\Expense::class))
     *     )
     * )
     *
     */
    public function getExpensesByTimeAndUser(Request $request)
    {
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
        if (!$expensesByUser) {
            // Not found
            $status = 404;
        }
        $serializer = $this->get('jms_serializer');
        $response->setStatusCode($status);
        $response->setContent($serializer->serialize($expensesByUser, 'json'));
        return $response;
    }

    /**
     * @Rest\Post("/expenses")
     * @SWG\Parameter( name="Authorization", in="header", required=true, type="string", default="Bearer TOKEN", description="Authorization" )
     * @Rest\RequestParam(name="Name")
     * @Rest\RequestParam(name="Price")
     * @SWG\Response(
     *     response=200,
     *     description="Add new expense, returns the expense object created",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=App\Entity\Expense::class))
     *     )
     * )
     */
    public function postExpense(Request $request)
    {
        // Inicializate
        $response = new JsonResponse();
        $status = 200;
        $data = null;

        //Check if parameters exists
        if(!$request->get('Name') || !$request->get('Price')){
            $status = 404;
        }else {

            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            /** @var ExpenseRepository $repositoryExpense */
            $repositoryExpense = $em->getRepository('App:Expense');
            // Create expense with user auth and params
            $expense = $repositoryExpense->createExpense($user, $request->get('Name'), $request->get('Price'));
            // Flush expense
            $em->persist($expense);
            $em->flush();
            // Serialize expense and show
            $serializer = $this->get('jms_serializer');
            $data = $serializer->serialize($expense, 'json');
        }
        $response->setStatusCode($status);
        $response->setContent($data);
        return $response;
    }
}