<?php

namespace AppBundle\Repository;

/**
 * MusicAppRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MusicAppRepository extends \Doctrine\ORM\EntityRepository
{
    
    // THIS FUNCTION RETURNS the number of songs per page in the pagination component.
    public function paginationElements($pageNum = 1 , $numMaxSongs=3){

        
        
        var_dump("var dump = ".$numMaxSongs);
        

        
        $query = $this->createQueryBuilder('s')
        ->where('s.topFavorite = 1')
        ->setFirstResult($numMaxSongs*($pageNum-1))
        ->setMaxResults($numMaxSongs)
        ->getQuery();

        return $query->getResult();
      
    }


}
