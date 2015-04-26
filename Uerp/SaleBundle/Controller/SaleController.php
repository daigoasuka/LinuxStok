<?php

namespace Uerp\SaleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Uerp\SaleBundle\Entity\Sale;
use Uerp\tpaymentBundle\Entity\tpayment;
use Uerp\SaleBundle\Form\SaleType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * Sale controller.
 *
 * @Route("/sale")
 */
class SaleController extends Controller
{

    /**
     * Lists all Sale entities.
     *
     * @Route("/", name="sale")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('UerpSaleBundle:Sale')->findAll();

        return array(
            'entities' => $entities,
        );
    }



  /**
     * Lists all Saleitems from the sale id entities.
     *
     * @Route("/closeendsale", name="closeendsale")
     * @Method("POST")
     * 
     */
    public function closeendsaleAction(Request $request)
    {
        $saleclose = $this->container->getParameter('cod.saleclose');//3
        $id = $this->get('request')->request->get('saleid');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);
        // $entity = $em->getRepository('UerpIncomesBundle:incomes')->findBySaleid($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }
        $entitystatus = $em->getRepository('UerpStatusBundle:Status')->find($saleclose);

        $entity->setStatus($entitystatus);

        $em->flush();

        // dump($entity); die();
            $response = new Response();
        $response->setContent(json_encode(array(
            'id' => $entity->getId(),
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;


    }








/**
     * Displays a form to edit an existing Sale entity.
     *
     * @Route("/{id}/close", name="sale_close")
     * @Method("GET")
     * @Template("UerpSaleBundle:Sale:saleclose.html.twig")
     */
    public function salecloseAction($id)
    {








        $datea = new \Datetime('now');

        $form = $this->createFormBuilder()
                ->setMethod('GET')
                ->setAction($this->generateUrl('sale'))
                ->add('datai','date', array(
                    'input'  => 'datetime',
                    'widget' => 'single_text',
                    'data' => $datea
                    ))
                ->add('tpay','entity',array(
                        'class' => 'UerptpaymentBundle:tpayment',
                        'property' => 'name',
                    )                 )
                ->add('value','number')
                ->add('parc','text')
                ->add('Add','button',array( 'attr' => array('id' => 'addpay')) )
                ->getForm();


        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }

        $editForm = $this->createCloseForm($entity);
 
        return array(
            'entity'      => $entity,
            'formsale'   => $editForm->createView(),
            'form' => $form->createView(),
            // 'delete_form' => $deleteForm->createView(),
        );




    }


    /**
     * Displays a form to edit an existing Sale entity.
     *
     * @Route("/reloadcloseinfo", name="sale_reloadcloseinfo")
     * @Method("POST")
     * 
     */
    public function reloadcloseinfoAction(Request $request)
    {
        $id = $this->get('request')->request->get('id');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);
        // /@Template("UerpSaleBundle:Sale:selling.html.twig")
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }

        $editForm = $this->createEditForm($entity);
        // $deleteForm = $this->createDeleteForm($id);
return $this->render(
            'UerpSaleBundle:Sale:menusale.html.twig',
            array( 'entity'      => $entity,
            'form'   => $editForm->createView(),)
        );

}










    /**
     * Displays a form to edit an existing Sale entity.
     *
     * @Route("/reloadmenu", name="sale_reloadmenu")
     * @Method("POST")
     * 
     */
    public function reloadmenuAction(Request $request)
    {
        $id = $this->get('request')->request->get('id');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);
        // /@Template("UerpSaleBundle:Sale:selling.html.twig")
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }

        $editForm = $this->createEditForm($entity);
        // $deleteForm = $this->createDeleteForm($id);
return $this->render(
            'UerpSaleBundle:Sale:menusale.html.twig',
            array( 'entity'      => $entity,
            'form'   => $editForm->createView(),)
        );

}



    /**
     * Creates a new Sale entity.
     *
     * @Route("/", name="sale_create")
     * @Method("POST")
     * @Template("UerpSaleBundle:Sale:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Sale();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sale_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }



    /**
     * Creates a new Sale entity.
     *
     * @Route("/newinline", name="sale_newinline")
     * @Method("POST")
     * @Template("UerpSaleBundle:Sale:new.html.twig")
     */
    public function createinlineAction(Request $request)
    {
        $entity = new Sale();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $entity->settotalcost('0');
        $entity->settotalsale('0');
        $entity->setdiscount('0');
        $entity->setnitems('0');
        $entity->settax('0');

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sale_selling', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Sale entity.
     *
     * @Route("/{id}/selling", name="sale_selling")
     * @Method("GET")
     * @Template("UerpSaleBundle:Sale:selling.html.twig")
     */
    public function editsellingAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }

        $editForm = $this->createEditForm($entity);
        // $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            // 'delete_form' => $deleteForm->createView(),
        );




    }







    /**
     * Creates a form to create a Sale entity.
     *
     * @param Sale $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Sale $entity)
    {
        $form = $this->createForm(new SaleType(), $entity, array(
            'action' => $this->generateUrl('sale_newinline'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Sale entity.
     * @Route("/new", name="sale_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
      
        $dat = date('Y-m-d');
        
        return $this->redirect($this->generateUrl('sale_newd', array('date' => $dat)));
        
    }



    /**
     * Lists all Saleitems from the sale id entities.
     *
     * @Route("/closs", name="closs")
     * @Method("POST")
     * 
     */
    public function clossAction(Request $request)
    {
        $date = $this->get('request')->request->get('date');
  

        
        return $this->redirect($this->generateUrl('sale_newd', array('date' => $date)));



    }



    /**
     * Displays a form to create a new Sale entity.
     * @Route("/newd/{date}", name="sale_newd")
     * @Method("GET")
     * @Template()
     */
    public function newdAction($date)
    {

        $dat = new \DateTime($date);


        $entity = new Sale();
        $entity->setDate($dat);

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Sale entity.
     *
     * @Route("/{id}", name="sale_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Sale entity.
     *
     * @Route("/{id}/edit", name="sale_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Sale entity.
    *
    * @param Sale $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Sale $entity)
    {
        $form = $this->createForm(new SaleType(), $entity, array(
            'action' => $this->generateUrl('sale_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
    * Creates a form to edit a Sale entity.
    *
    * @param Sale $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCloseForm(Sale $entity)
    {
        $form = $this->createForm(new SaleType(), $entity, array(
            'action' => $this->generateUrl('sale_closeup', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Sale entity.
     *
     * @Route("/{id}", name="sale_update")
     * @Method("PUT")
     * @Template("UerpSaleBundle:Sale:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('sale_selling', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Sale entity.
     *
     * @Route("/closeup/{id}", name="sale_closeup")
     * @Method("PUT")
     * @Template("UerpSaleBundle:Sale:edit.html.twig")
     */
    public function sale_closeupAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);
        dump($entity);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sale entity.');
        }

        $editForm = $this->createCloseForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('sale_close', array('id' => $id)));
        }
            return $this->redirect($this->generateUrl('sale_close', array('id' => $id)));
        // return array(
        //     'entity'      => $entity,
        //     'edit_form'   => $editForm->createView(),
            
        // );
    }
    /**
     * Deletes a Sale entity.
     *
     * @Route("/{id}", name="sale_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UerpSaleBundle:Sale')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sale entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sale'));
    }

    /**
     * Creates a form to delete a Sale entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sale_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
