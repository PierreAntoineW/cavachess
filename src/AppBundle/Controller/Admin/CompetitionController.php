<?php


namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Competition;
use AppBundle\Entity\GameMode;
use AppBundle\Entity\TypeOfGame;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\CompetitionType;


/**
 * Description of CompetitionController
 *
 * 
 */
class CompetitionController extends Controller
{
    /**
     * 
     * @Route("/competition")
     */
    public function gestionCompetitionsAction() 
    {
        return $this->render('admin/competition/gestion.html.twig');
    }
    
    
    public function listCompetitionsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $competitions = $em->getRepository('AppBundle:Competition')->findAll();
        $columns = $em->getClassMetadata('AppBundle:Competition')->getFieldNames();
        
        return $this->render('admin/competition/list.html.twig', 
        [
            'competitions' => $competitions,
            'columns' => $columns,
        ]);
    }
    
    /**
     * @param int $id
     * @Route("/modif/{id}", defaults={"id":null})
     * 
     */
    public function editCompetitionAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        if(is_null($id)) // création d'une nouvelle instance de Competition
        {
            $new = true;
            $competition = new Competition(); 
        }
        else // modification d'une instance de Competition
        {
            $new = false;
            // Raccourci pour $em->getReposiroty('AppBundle:Competition', $id)->find($id)
            $competition = $em->find('AppBundle:Competition', $id);
                
            if(is_null($competition))
            {
                // On redirige vers la page de gestion de compétition
                return $this->redirectToRoute('app_admin_competition');
            }
        }

        // Création du formulaire associé à l'instance de Competition
        $form = $this->createForm(CompetitionType::class, $competition);

        //
        $form->handleRequest($request);
        
        // Soumission formulaire
        if($form->isSubmitted())
        {
            if($form->isValid()) // No error
            {
                $em->persist($competition); // 
                $em->flush(); // Execute()
                
                // Forme ternaire. Si new existe => ? sinon :
                $msg = ($new)
                        ? 'La compétition a bien été crée'
                        : 'La compétition a bien été modifiée'
                      ;
                $this->addFlash('success', $msg);
                return $this->redirectToRoute('competition');
            }
            else
            {
                // Ajout le message flash
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }
        
        // Render
        return $this->render('admin/competition/edit.html.twig',
        [
           'new' => $new,
           'form' => $form->createView(),
        ]);         
    }
}
