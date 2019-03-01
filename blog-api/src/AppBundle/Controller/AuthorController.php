<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AuthorController extends Controller
{
    /**
     * @Route("/authors/{id}", name="author_show", methods={"GET"})
     */
    public function showAction(Author $author)
    {
        $data = $this->get('serializer')->serialize($author, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/authors", name="author_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        // on récupère les datas reçues en POST
        $data = $request->getContent();

        // on désérialise les datas en hydratant un objet de l'entité Author
        $author = $this->get('serializer')->deserialize($data, 'AppBundle\Entity\Author', 'json');

        // on récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // on persist puis on flush le nouvel auteur
        $em->persist($author);
        $em->flush();

        $response = new Response('', Response::HTTP_CREATED);

        return $response;
    }
}