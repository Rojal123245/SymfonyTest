<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SignUpFormType;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignUpController extends AbstractController
{
    /**
     * @Route("/signup", name="signup"), methods={"GET", "POST"}
     */
    public function signUp(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Create a new User object
        $user = new User();

        // Create the signup form and associate it with the User object
        $form = $this->createForm(SignUpFormType::class, $user);

        // Handle form submission
        $form->handleRequest($request);

        // Check if the form has been submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data
            $formData = $form->getData();

            // Set the user's properties from the form data
            $user->setFirstName($formData->getFirstName());
            $user->setLastName($formData->getLastName());
            $user->setEmail($formData->getEmail());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $formData->getPassword())
            );

            // Save the user to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect the user to the login page or any other page
            return $this->redirectToRoute('app_contact_index');
        }

        // Render the signup form
        return $this->render('user/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
