<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    /**
     * @Route("/api/serts", name="api_get_serts")
     */
    public function getSertsAction(Request $request)
    {
        //Prepairing the query
        $manager = $this->getDoctrine()->getManager();
        $query_text = "SELECT s
                       FROM AppBundle:Sertificate s";
        $query = $manager->createQuery($query_text);

        //Setting up the query
        if ($offset = $request->query->get("offset")){
            $query->setFirstResult($offset);
        }

        if ($amount = $request->query->get("amount")){
            $query->setMaxResults($amount);
        }

        $q_result = $query->getResult();
        $result = array();
        foreach ($q_result as $sert){
            array_push($result, $sert->toArray());
        }

        return new Response(json_encode($result));
    }

    /**
     * @Route("/api/sert/{sert_id}", name="api_get_sert")
     */
    public function getSertAction($sert_id, Request $request)
    {
        //Fetching sertificate
        $doctrine = $this->getDoctrine();
        $sert_rep = $doctrine->getRepository("AppBundle:Sertificate");
        $sert = $sert_rep->find($sert_id);

        $result = array();

        if (!$sert){
            $result["id"] = $sert_id;
            $result["error"] = "invalid_id";
            $result = json_encode($result);
        } else{
            $result = $sert->serialize();
        }

        return new Response("<html><body>".$result."</body></html>");
    }

    /**
     * @Route("/api/user/{user_id}", name="api_get_user")
     */
    public function getUserAction($user_id, Request $request)
    {
        //Fetching users
        $doctrine = $this->getDoctrine();
        $user_rep = $doctrine->getRepository("AppBundle:User");
        $user = $user_rep->find($user_id);

        $result = array();

        if (!$user){
            $result["id"] = $user_id;
            $result["error"] = "invalid_id";
            $result = json_encode($result);
        } else{
            $result = json_encode($user->toArray());
        }

        return new Response("<html><body>".$result."</body></html>");
    }
}