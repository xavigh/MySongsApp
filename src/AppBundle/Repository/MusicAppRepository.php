<?php

namespace AppBundle\Repository;



use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\MusicApp;

/**
 * MusicAppRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MusicAppRepository extends \Doctrine\ORM\EntityRepository
{
    
    // THIS FUNCTION RETURNS the number of songs per page in the pagination component.
    public function paginationElements($pageNum = 1 , $numMaxSongs){

        //var_dump("var dump = ".$numMaxSongs);
        $em = $this->getEntityManager()->getRepository(MusicApp::class);
        $query = $em->createQueryBuilder('s')
        ->where('s.topFavorite = 1')
        ->setFirstResult($numMaxSongs*($pageNum-1))
        ->setMaxResults($numMaxSongs)
        ->getQuery();

        return $query->getResult();
      
    }



    // this function returns the search results to the action handleSearch
    public function findAllWithSearch($term)
    {
       
        var_dump("i am inside function findAllWithSearch ". $term);
       
        $em = $this->getEntityManager()->getRepository(MusicApp::class);
        $qb = $em->createQueryBuilder('p');       


        $songs = $qb->select('p')   
        
        ->where($qb->expr()->like('p.trackName', ':term'))
        ->andWhere($qb->expr()->like('p.artistName', ':term'))
        ->andWhere($qb->expr()->like('p.albumName', ':term'))           
        ->setParameter('term', '%' .$term. '%');
    

        return $songs
        ->orderBy('p.creationDate', 'DESC')
        ->getQuery()
        ->getResult();
        ;
      
       
   }


}//end class repository