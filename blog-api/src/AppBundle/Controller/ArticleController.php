<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller
{
    /**
     * @Route("/articles/{id}", name="article_show", methods={"GET"})
     */
    public function showAction(Article $article)
    {
        // grâce au ParamConverter de Doctrine, Doctrine comprend que l'id passé dans l'url est la clé primaire d'un objet de type Article qu'il va automatiquement chercher en base via la méthode find(). Si trouvé en base, Doctrine hydrate cet objet dans la variable $article

        // dump($article);die();

        $data = $this->get('jms_serializer')->serialize($article, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json'); 

        return $response;
    }

    /**
     * @Route("/articles", name="article_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        // on récupère les datas reçues en POST
        $data = $request->getContent();

        // on désérialise les datas en hydratant un objet de l'entité Article
        $article = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Article', 'json');

        // on récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // on persist puis on flush le nouvel article
        $em->persist($article);
        $em->flush();

        // on retourne une réponse
        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/articles", name="article_list", methods={"GET"})
     */
    public function listAction()
    {
        // on récupère tous les articles en base à partir du repository de l'entité Article
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();

        // on sérialise les datas à partir des objets de l'entité Article
        $data = $this->get('jms_serializer')->serialize($articles, 'json');

        // on retourne une réponse
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
