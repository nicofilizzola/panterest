<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PinController extends AbstractController
{
    //public function index(EntityManagerInterface $em): Response
    public function index(PinRepository $repo) : Response
    {
        //$repo = $em->getRepository(Pin::class); // if injected entity manager

        // $repo = $em->getRepository('App:Pin');
        $allPins = $repo->findAll();
        return $this->render('pin/index.html.twig', compact('allPins'));
        // compact('allPins'); == ['allPins' => $allPins]ù
        // or just
        //return $this->render('pin/index.html.twig', ['allPins' => $allPins->findAll()]);
    }

    public function create(Request $req, EntityManagerInterface $em) : Response
    {
        $pin = new pin;

        /* for default input fill
        $pin->setTitle('placeholder title');
        $pin->setDescription('placeholder description');
        */  

        /* or
        $pin = ['title' => 'whatever', ...]
        */

        $form = $this->createFormBuilder($pin)
            ->add('title', null, [
                'attr' => ['autofocus' => true, 'class' => 'active input', 'placeholder' => 'title'],
                'required' => true
                ])
            // ->add('description', TextareaType::class) // define input type not recommended
            ->add('description', null)
            // ->add('submit', SubmitType::class, ['label' => 'Create pin']) // submit not recommended
            ->getForm()
        ;
        $form->handleRequest($req);
        //handle for GET
        if($form->isSubmitted() && $form->isValid()){

            // get data of single input:
            // $title = $form['title']->getData();

            /* use this if no object argument for createFormBuilder() function
            $data = $form->getData();
            $pin = new Pin;
            $pin->setTitle($data['title']);
            $pin->setDescription($data['description']);
            */

            // argument in createFormBuilder set attributes automatically
            $em->persist($pin);
            $em->flush();
            
            return $this->redirectToRoute('index');
        }

        $formView = $form->createView();
        return $this->render('pin/create.html.twig', compact('formView'));





        /*********
        FORM MANAGER (NAIVE APPROACH)
        *********/
        
        // if post request was sent...
        /*if($req->isMethod('POST')){

            //dd($req->request->all());
            //dd($req->request->get('title'));
            //dd($req->request->has('title'));
            //return 'el pepe';
    
            $data = $req->request->all(); // data is an associative array with all POS inputs. query instead of request for GET
            $pin = new Pin;
            $pin->setTitle($data['title']);
            $pin->setDescription($data['description']);

            if($this->isCsrfTokenValid('pin_create', $data['_token'])){
                $em->persist($pin);
                $em->flush();
                //return $this->redirect($this->generateUrl('index'));
                return $this->redirectToRoute('index');
            }else{
                // error msg
            }

        }else{
            return $this->render('pin/create.html.twig');
        } */
    }
}
