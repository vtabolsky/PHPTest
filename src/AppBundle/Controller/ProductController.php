<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Document\Product;
use AppBundle\Form\ProductType;
use Doctrine\ODM\MongoDB\DocumentManager;

class ProductController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {
		$repository = $this->get('doctrine_mongodb.odm.document_manager')
			->getRepository('AppBundle:Product');
			
		if ($repository == null){
			$this->addFlash('error',"Can't create repository");
		}
		 
		$searchForm = $this->createFormBuilder()->add('tags')->getForm();
				
		$searchForm -> handleRequest($request);
		
		if ($searchForm->isSubmitted() &&  $searchForm->isValid()) {

			$products = $repository->findByTags(array('$regex' => $searchForm->getData()['tags']));
		} else {
			
			$products = $repository->findAll();
		}
		
        return $this->render('product/index.html.twig', array("products"=>$products, "form"=>$searchForm->createView()));
    }
	
	/**
     * @Route("/modify/{id}")
	 * @ParamConverter("prod", class="AppBundle\Document\Product")
     */
    public function modifyAction(Product $prod, Request $request)
    {	

		$form = $this->createForm(ProductType::class, $prod, []);

		$form->handleRequest($request);
		
		if ($form->isSubmitted() &&  $form->isValid()) {
			$prod=$form->getData();
			
			$file = $prod->getFile();

			self::performUpload($file, $prod);
			
			$this->addFlash('notice',"Product ".$prod->getName(). " was updated");

			$dm = $this->get('doctrine_mongodb.odm.document_manager');
			
			$dm->persist($prod);
			$dm->flush();
			
			return $this->redirectToRoute('index');
		} 
		
		return $this->render('product/modify.html.twig', array('form'=>$form->createView(), 'title'=>'Modify product'));
    }
	
	
	/**
     * @Route("/delete/{id}", name="delete")
	 * @ParamConverter("prod", class="AppBundle\Document\Product")
     */
    public function deleteAction(Product $prod)
    {
		$dm = $this->get('doctrine_mongodb.odm.document_manager');
		
		$dirUpload = $this->container->getParameter('kernel.root_dir').'/../web/uploads/'.$prod->getId();
		
		// if Product has images, let's remove them and related directory too
		if (file_exists($dirUpload)){
				
			$files = glob($dirUpload."/*"); // get all file names
			foreach($files as $f){ // iterate files
			  if(is_file($f))
				unlink($f); // delete file
			}
			rmdir($dirUpload);
		}
		
		$dm->remove($prod);
		$dm->flush();
		
		$this->addFlash(
            'notice',
            "Product ".$prod->getName(). " was deleted"
        );
		return $this->redirectToRoute('index');
    }
	
	/**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
		
		$prod = new Product();

		
		$form = $this->createForm(ProductType::class, $prod, []);
			
		$form->handleRequest($request);
		
		if ($form->isSubmitted() &&  $form->isValid()) {

		
			$prod=$form->getData();

			$this->addFlash('notice',"Product ".$prod->getName(). " was created");

			$dm = $this->get('doctrine_mongodb.odm.document_manager');
			
			$dm->persist($prod);
			
			
			$file = $prod->getFile();

			self::performUpload($file, $prod);
			$dm->flush();
			
			return $this->redirectToRoute('index');
		} 
		
		return $this->render('product/modify.html.twig', array('form'=>$form->createView(), 'title'=>'Create product'));
    }
	
	
	/**
     * @param UploadedFile $file
	 * @param Product $prod
     */
	 
	private function performUpload($file, $prod){
		if (!empty ($file)) {
			$dirUpload = $this->container->getParameter('kernel.root_dir').'/../web/uploads/'.$prod->getId();
			// first file upload, create directory named as product ID
			if (!file_exists($dirUpload) ){
				$mkdir = mkdir($dirUpload);
				if ($mkdir === false){
					$this->addFlash(
						'error',
						"Failed to create directory for images"
					);
				}
			} else {
				// directory existed before, let's first empty it
				$files = glob($dirUpload."/*"); // get all file names
				foreach($files as $f){ // iterate files
				  if(is_file($f))
					unlink($f); // delete file
				}
			}
			
			$file->move($dirUpload, $file->getClientOriginalName());
			$prod->setImage($file->getClientOriginalName());
			$file=null;
		}
		
	}
}
