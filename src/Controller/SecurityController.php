<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
     public function logout()
     {

     }

     /**
	     * @Route("/inscription", name="app_inscription")
	     */
	    public function inscription(Request $request, ObjectManager $manager,UserPasswordEncoderInterface $encoder)
	    {
	        //Créer un utilisateur vide
	        $utilisateur = new User();

	        // Création du formulaire permettant de saisir un utilisateur
	        $formulaireUtilisateur = $this->createForm(UserType::class, $utilisateur);


	        $formulaireUtilisateur->handleRequest($request);

	         if ($formulaireUtilisateur->isSubmitted() && $formulaireUtilisateur->isValid())
	         {
						 // Attribuer un rôle à l'utilisateur
	            $utilisateur->setRoles(['ROLE_USER']);

	            //Encoder le mot de passe de l'utilisateur
	            $encodagePassword = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
	            $utilisateur->setPassword($encodagePassword);

	            // Enregistrer l'utilisateur en base de donnéelse
	            $manager->persist($utilisateur);
	            $manager->flush();

	            // Rediriger l'utilisateur vers la page d'accueil
	            return $this->redirectToRoute('app_login');
	         }

	        // Afficher la page présentant le formulaire d'inscription
	        return $this->render('security/inscription.html.twig',['vueFormulaire' => $formulaireUtilisateur->createView()]);
	    }
}
