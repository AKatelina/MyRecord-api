<?php

namespace App\Controller;

use App\Service\Emails;
use App\Service\HashResetPassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('main');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     *
     * @Route("/lk/password/reset", name="resetPassword")
     *
     */
    public function resetPassword(\App\Service\User $user, Emails $emails, Request $request, HashResetPassword $hashResetPassword,SerializerInterface $serializer): Response
    {
        $data = $request->getContent();

        $temp = json_decode($data, true);
        $json = $serializer->serialize(['error'=>'1','status'=>'noContent'], 'json');
        if($temp){
            //        $statusUser = $user->searchUser('katelinskiidmitrii98@gmail.com');
            $statusUser = $user->searchUser($temp['email']);
            if ($statusUser!=null) {
                $hash = $hashResetPassword->add($statusUser);

                $emails->emailResetPassword($statusUser->getEmail(), $hash);
                $json = $serializer->serialize(['error'=>'0','status'=>'ok'], 'json');

            }
            else{
                $json = $serializer->serialize(['error'=>'1','status'=>'bad'], 'json');
            }
        }


        $response = new Response();
        $response->setContent(
            $json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;





    }
    /**
     * @Route("/lk/password/reset/email/{email}/{hash}", name="passwordResetForm")
     */
    public function passwordResetForm(Request $request, Emails $emails, \App\Service\User $user, HashResetPassword $hashResetPassword, $email, $hash)
    {

        $statusUser = $user->searchUser($email);
        if ($statusUser) {

            if($hashResetPassword->checkHash($hash,$email)){
                if ($request->isMethod('POST')) {

                    $data = $request->getContent();

                    $temp = json_decode($data, true);


                        $user->updatePassword($email,$temp['password']);
                        $hashResetPassword->checkHashFalse($statusUser->getId());
                    return $this->json(['error'=>'0', 'status'=>'ok']);

                }
                else{
                    return $this->json(['error'=>'1','status'=>'No method POST']);

                }

            }
            else{

                return $this->json(['error'=>'3','status'=>'Token invalid']);
            }

        }
        else{
            return $this->json(['error'=>'4','status'=>'Error']);
        }



    }

    /**
     * @Route("/test", name="test")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function test(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser()) {
           print_r($this->getUser());
        }

        // get the login error if there is one
//        $error = $authenticationUtils->getLastAuthenticationError();
//        // last username entered by the user
//        $lastUsername = $authenticationUtils->getLastUsername();

        return 0;
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

        unset($_COOKIE['jwt_hp']);
        setcookie('jwt_hp', null, -1, '/');

        unset($_COOKIE['jwt_s']);
        setcookie('jwt_s', null, -1, '/');

        unset($_COOKIE['bearer']);
        setcookie('bearer', null, -1, '/');

        unset($_COOKIE['refresh_token']);
        setcookie('refresh_token', null, -1, '/');
//        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        return $this->json(['status'=>'ok']);
    }

    /**
     * @Route("/registration", name="app_registration")
     */
    public function registration(Request $request, UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $em)
    {
        $response = new Response();
        $data = $request->getContent();

            $temp = json_decode($data, true);

            $user = new User();

            $user->setUsername('');
//            $user->setInfoUser($temp['infoUser']);
//            $user->setStatus($temp['status']);
            $user->setPhone($temp['phone']);
//            $user->setAddress($temp['address']);
            $user->setEmail('');
            $user->setRoles([]);
            $user->setPassword($passwordHasher->hashPassword(
                $user, $temp['password']
            ));



            $em->persist($user);

            $em->flush();
            return $this->json(['idUser'=>$user->getId()]);


    }
    /**
     * @Route("/admin/change/user/{id}", name="changeInfoUser")
     */
    public function changeInfoUser($id,Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $response = new Response();
        $data = $request->getContent();

        $roles = $this->getUser()->getRoles()['0'];
        if ($roles ==='ROLE_ADMIN') {
            $temp = json_decode($data, true);

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findBy(array('id' => $id))[0];

            $user->setUsername($temp['name']);
            $user->setInn($temp['inn']);
            $user->setInfoUser($temp['infoUser']);
            $user->setStatus($temp['status']);
            $user->setPhone($temp['phone']);
            $user->setManager($temp['manager']);
            $user->setAddress($temp['address']);
            $user->setEmail($temp['email']);
            $user->setFuncWork($temp['funcwork']);
            $user->setBalance($temp['balance']);



            $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            $em->flush();
            return $this->json(['idUser'=>$user->getId()]);
        }

    }
    /**
     * @Route("/admin/change/password", name="changeUserPassword")
     */
    public function changeUserPassword(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $response = new Response();
        $data = $request->getContent();

        $roles = $this->getUser()->getRoles()['0'];
//        if ($roles ==='ROLE_ADMIN') {
            $temp = json_decode($data, true);

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findBy(array('id' => $temp['idUser']))[0];
            $user->setPassword($passwordHasher->hashPassword(
                $user, $temp['password']
            ));






            $em->persist($user);

            $em->flush();
            return $this->json(['idUser'=>$user->getId()]);
//        }

    }
    /**
     * @Route("/user/showAll", name="showAll")
     */
    public function showAll(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $response = new Response();
        $data = $request->getContent();

        $roles = $this->getUser()->getRoles()['0'];
        if ($roles ==='ROLE_ADMIN') {
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findAll();



            return $this->json(['Users'=>$user]);
        }

    }
    /**
     * @Route("/user/show/{id}", name="showItem")
     */
    public function showItem($id,Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $response = new Response();
        $data = $request->getContent();

        $roles = $this->getUser()->getRoles()['0'];
        if ($roles ==='ROLE_ADMIN') {
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findBy(array('id' => $id));



            return $this->json(['users'=>$user]);
        }

    }
    /**
     * @Route("/login_check", name="login_check" )
     */
    public function login_check(Request $request)
    {

        if ($this->getUser()) {
            print_r($this->getUser());
        }
//        if ($this->getUser()) {
//            if ($request->query->get('url')) {
//                $this->redirect($request->query->get('url'));
//            }
//
//
//        }

        return $this->json(['status'=>'ok']);
    }
    /**
     * @Route("/mobile/login", name="loginMobile" )
     */
    public function loginMobile(Request $request)
    {

        if ($this->getUser()) {
//            print_r($this->getUser());
        }
//        if ($this->getUser()) {
//            if ($request->query->get('url')) {
//                $this->redirect($request->query->get('url'));
//            }
//
//
//        }

        return $this->json(['users'=>$data = $request->getContent()]);
    }



}
