<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\MusicApp;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Repository\MusicAppRepository;

class DefaultController extends Controller
{
    /**
     * @Route("/{pageNum}", name="homepage")
     */
    public function homeAction(Request $request, $pageNum = 1)
    {
            

            // form without data class to manage the numMaxSongs per page
            $defaultData = ['numMaxSongs' => 0];
            $form = $this->createFormBuilder($defaultData)
                ->add('numMaxSongs', IntegerType::class, array('label' => 'Songs per page'))            
                ->add('submit', SubmitType::class)
                ->getForm();
          
            $form->handleRequest($request);

            // if form is submitted and valie proceed
            if ($form->isSubmitted() && $form->isValid()) {
               
                // data is an array with "name", "email", and "message" keys
                $data = $form->getData();
                // get the numMaxSongs from the array of $data sent through the POST method
               
                $numMaxSongs = $data['numMaxSongs'];
                // to avoid error if user inputs a negative number or zero.
                ( $numMaxSongs <= 0 )? $numMaxSongs = 3: $numMaxSongs; // Ternary conditional
               
                var_dump($numMaxSongs);
               
            }
            else{
                
                $numMaxSongs = 3;
            }

            
            // search bar
       
            // calling the repository function pageSongs to get the songs elements per page to do pagination
                            
            $musicRepo = $this->getDoctrine()->getRepository(MusicApp::class);                
            $songs = $musicRepo->paginationElements($pageNum, $numMaxSongs);
            return $this->render('frontal/index.html.twig', array( 'songs'=>$songs, 'numMaxSongs'=>$numMaxSongs, 'currentPage'=>$pageNum, 'form' => $form->createView() ) );
         
    }

    

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction( Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('frontal/about.html.twig' );
    }

    /**
     * @Route("/playlist/{numPlayList}", name="playlist" )
     */
    public function playlistAction(Request $request, $numPlayList="show_all")
    {
        // replace this example code with whatever you need
        return $this->render('frontal/playlist.html.twig', array('numPlayList'=>$numPlayList) );
    }

    
    /**
     * @Route("/home/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('frontal/contact.html.twig' );
    }

    /**
     * @Route("/songSelected/{id}", name="songSelected")
     */
    public function songSelectedAction(Request $request,$id=null)
    {
        if( $id != null){
             // replace this example code with whatever you need
            $songsRepo = $this->getDoctrine()->getRepository(MusicApp::class);
            // finds *all* products
            $songId = $songsRepo->find($id);
            // var_dump($songId);
           
            return $this->render( 'frontal/songSelected.html.twig', array('song'=>$songId ) );
            

        }else{
            return $this->redirectToRoute('homepage');
        }

    }

        
    /**
     * @Route("/categorySelected/{id}", name="categorySelected")
     */
    public function categorySelectedAction(Request $request,$id=null)
    {
        if( $id != null){
             // replace this example code with whatever you need
            $categoryRepo = $this->getDoctrine()->getRepository(Category::class);
            // finds *all* products
            $categoryId = $categoryRepo->find($id);
            // var_dump($songId);
           
            return $this->render( 'frontal/categorySelected.html.twig', array('category'=>$categoryId ) );
            

        }else{
            
            return $this->redirectToRoute('homepage');
        }
    }


     /**
     * @Route("/register/", name="register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // if(!is_null($request)){
        //     $reqData = $request->request->all();
        //     var_dump($reqData);
        // }


         $user = new User();         
         $form = $this->createForm(UserType::class, $user);
         
         $form->handleRequest($request);
         
         if ($form->isSubmitted() && $form->isValid()) {
          
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            // 3b) username == email to register
            $user->setUsername($user->getEmail());
            $user->setRoles(array('ROLE_USER'));

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

    
            return $this->redirectToRoute('login');
        }
           
       //if no form submitted to Request then show form to fill in the user  
         return $this->render('frontal/register.html.twig', array(  'form' => $form->createView()  ));
        

        }

    /**
     * @Route("/login/", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('frontal/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);

    }

   

}
