<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }



    public function getAllPanier() {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.quantite', 'p.prix', 'p.dateAjoutPanier', 'p.user_id','p.produit_id','pr.nom','p.commande_id')
            ->from('paniers', 'p')
            ->innerJoin('p', 'users', 'u', 'p.user_id=u.id')
            ->innerJoin('p', 'produits', 'pr', 'p.produit_id=pr.id')
            //->innerJoin('p', 'commandes', 'c', 'p.commande_id=c.id')
            ->addOrderBy('p.id', 'ASC');
        return $queryBuilder->execute()->fetchAll();



    }
    public function getPanier($login) {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.quantite', 'p.prix', 'p.dateAjoutPanier', 'p.user_id','p.produit_id','pr.nom', 'pr.photo',  'commande_id')
            ->from('paniers', 'p')
            ->innerJoin('p', 'users', 'u', 'p.user_id=u.id')
            ->innerJoin('p', 'produits', 'pr', 'p.produit_id=pr.id')
            //->innerJoin('p', 'commandes', 'c', 'p.commande_id=c.id')
            ->where('u.login=?')
            ->setParameter(0,$login);
        return $queryBuilder->execute()->fetchAll();

    }
    public function addPanier($donnees) {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('paniers')
            ->values([
                'quantite' => '?',
                'prix' => '?',
                'user_id' => '?',
                'produit_id' => '?',
                'commande_id' => '?'
            ])


            ->setParameter(0, $donnees['quantite'])
            ->setParameter(1, $donnees['prix']*$donnees['quantite'])

            ->setParameter(2, $donnees['user_id'])
            ->setParameter(3, $donnees['id'])
            ->setParameter(4, 1)

        ;
        return $queryBuilder->execute();
    }
    public function deletePanier($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('paniers')
            ->where('id = :id')
            ->setParameter('id',(int)$id)
        ;
        return $queryBuilder->execute();
    }




}