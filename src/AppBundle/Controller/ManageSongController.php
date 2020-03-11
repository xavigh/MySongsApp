<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\MusicApp;
use AppBundle\Form\MusicType;
use AppBundle\Form\CategoryType;
use AppBundle\Entity\Category;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextArea;


/**
* @Route("/managesongs")
*/
class ManageSongController extends Controller
{
    /**
     * @Route("/addsong/{id}", name="addsong", defaults={"id": null})
     */
    public function addSongAction($id=null, Request $request)
    {
        // if(!is_null($request)){
        //     $reqData = $request->request->all();
        //     var_dump($reqData);
        // }
        $song = null;
        $em = $this->getDoctrine()->getManager();
                if ($id) {
                    $song = $em->getRepository(MusicApp::class)->findOneById($id);
                }
                // if no song create song object
                if (!$song){
                        $song = new MusicApp();         
                }
         
         $form = $this->createForm(MusicType::class, $song);
         
         $form->handleRequest($request);  
         
            if ($form->isSubmitted() && $form->isValid()) {
            
                $song = $form->getData();
                
                $song->setCreationDate(new \DateTime());
            
                // ... perform some action, such as saving the task to the database
                // for example, if Task is a Doctrine entity, save it!
                
                $em->persist($song);
                $em->flush();
        
                return $this->redirectToRoute('songSelected',array( 'id'=>$song->getId() ));
            }
           
       //if no form submitted to Request then show form to fill in the user  
         return $this->render('manageSongs/newSong.html.twig', array(  'form' => $form->createView()  ));
        

    }

     /**
     * @Route("/deletesong/{id}", name="deletesong")
     */
    public function deleteSongAction(Request $request, $id=null)
    {
        // if(!is_null($request)){
        //     $reqData = $request->request->all();
        //     var_dump($reqData);
        // }

        
        $entityManager = $this->getDoctrine()->getManager();
        $song = $entityManager->getRepository(MusicApp::class)->find($id);
        
            if (!$id) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }
        if ($song){
            $entityManager->remove($song);
            $entityManager->flush();
        }
        return $this->redirectToRoute('homepage');
      
    }


    /**
     * @Route("/newCategory/{id}", name="newCategory", defaults={"id": null}))
     */
    public function newCategoryAction(Request $request, $id = null)
    {

        $category = null;
        $em = $this->getDoctrine()->getManager();
        
        if ($id) {
            $category = $em->getRepository(Category::class)->findOneById($id);
        }
        // if no song create song object
        if (!$category){
                $category = new Category();         
        }
 
          
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
        
                    if ($form->isSubmitted() && $form->isValid()) {
                    
                    $category = $form->getData();
           
                    // ... perform some action, such as saving the task to the database
                    // for example, if Task is a Doctrine entity, save it!
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($category);
                    $em->flush();
            
                    return $this->redirectToRoute('categorySelected',array( 'id'=>$category->getId() ));

                }

           return $this->render('manageSongs/newCategory.html.twig', array(  'form' => $form->createView()  ));
    }


     /**
     * @Route("/searchRoute", name="searchRoute")
     */
    public function searchSongsAction()
    {
       
        
        $form = $this->createFormBuilder(null)
        ->add('searchWord', TextType::class)        
       ->getForm();
  
        return $this->render('frontal/searchBar.html.twig',
                    array( 'form'=> $form->createView())
        
        );

    }


    /**
     * @Route("/search", name="handleSearch")
     * @param Request $request
    */
   public function handleSearch(Request $request){
   
    //Recoger POST
    $searchWord =  dump($request->request->get('form')['searchWord']);    
    var_dump("POST:..". $searchWord);       



    //var_dump($request->request);
    
    $searchRepo = $this->getDoctrine()->getRepository(MusicApp::class);
    $songs = $searchRepo->findAllWithSearch($searchWord); 
      
     
    return $this->render('frontal/searchPage.html.twig', array('songs'=>$songs));
    


   }

     
   
    
    
   

    
}