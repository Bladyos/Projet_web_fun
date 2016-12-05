<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\PanierModel;
use App\Model\ProduitModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

class PanierController implements ControllerProviderInterface
{
    private $panierModel;
    private $produitModel;

    public function __construct()
    {
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->panierModel = new PanierModel($app);
        $login = $app['session']->get('login');
        $panier = $this->panierModel->getPanier($login);
        return $app["twig"]->render('backOff/Panier/show.html.twig',['panier'=>$panier]);
    }

    public function delete(Application $app, $id) {
        $this->panierModel = new panierModel($app);
        $login = $app['session']->get('login');
        $donnees = $this->panierModel->getPanier($login);

        return $app["twig"]->render('backOff/Panier/delete.html.twig',['donnees'=>$donnees, 'id'=>$id]);

    }

    public function addPanier(Application $app, $id) {

        $this->produitModel = new produitModel($app);
        $donnees = $this->produitModel->getProduit($id);
        $donnees['id_user'] = $id;
        $data=$this->produitModel->getAllProduits();
        $this->panierModel=new PanierModel($app);
        $panier=$this->panierModel->getAllPanier();
        //$this->panierModel->addPanier($donnees);

        $donnees['user_id'] = $app['session']->get('id_user');
        return $app["twig"]->render('backOff/Panier/add.html.twig',['donnees'=>$donnees,'id'=>$id]);
    }

    public function validFormAddPanier(Application $app, Request $req) {
        // var_dump($app['request']->attributes);
        /*if (isset($_POST['nom']) && isset($_POST['typeProduit_id']) and isset($_POST['nom']) and isset($_POST['photo'])) {

            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if(! is_numeric($donnees['typeProduit_id']))$erreurs['typeProduit_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['prix']))$erreurs['prix']='saisir une valeur numérique';
            if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';

            if(! empty($erreurs))
            {
                $this->typeProduitModel = new TypeProduitModel($app);
                $typeProduits = $this->typeProduitModel->getAllTypeProduits();
                return $app["twig"]->render('backOff/Produit/add.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'typeProduits'=>$typeProduits]);
            }*/

        $donnees = [
            'id' => htmlspecialchars($_POST['id']),                    // echapper les entrées
            'nom' => htmlspecialchars($_POST['nom']),
            'prix' => htmlspecialchars($_POST['prix']),
            'quantite' => htmlspecialchars($_POST['quantite']),
            'typeProduit_id' => htmlspecialchars($_POST['typeProduit_id']),  //$req->query->get('photo')
            'user_id' => htmlspecialchars($_POST['user_id'])
        ];

                $this->PanierModel = new PanierModel($app);
                $this->PanierModel->addPanier($donnees);
        var_dump($donnees);
                return $app->redirect($app["url_generator"]->generate("produit.show"));




    }
    public function validFormDelete(Application $app, Request $req) {
        $id=$app->escape($req->get('id'));
        if (is_numeric($id)) {
            $this->panierModel = new PanierModel($app);
            $this->panierModel->deletePanier($id);
            return $app->redirect($app["url_generator"]->generate("panier.show"));
        }
        else
            return $app->abort(404, 'error Pb id form Delete');
    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\panierController::index')->bind('panier.index');
        $controllers->get('/show', 'App\Controller\panierController::show')->bind('panier.show');

        $controllers->get('/add', 'App\Controller\panierController::add')->bind('panier.add');
        $controllers->post('/add', 'App\Controller\panierController::validFormAdd')->bind('panier.validFormAdd');

        $controllers->get('/addPanier/{id}', 'App\Controller\panierController::addPanier')->bind('panier.addPanier');
        $controllers->post('/validFormAddPanier', 'App\Controller\panierController::validFormAddPanier')->bind('panier.validFormAddPanier');


        $controllers->get('/delete/{id}', 'App\Controller\panierController::delete')->bind('panier.delete');
        $controllers->post('/delete', 'App\Controller\panierController::validFormDelete')->bind('panier.validFormDelete');

        return $controllers;
    }


}