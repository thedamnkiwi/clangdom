<?php

namespace BBL\WebBundle\Controller;

use BBL\WebBundle\Entity\Profil;

use BBL\WebBundle\Entity\Event;

use BBL\WebBundle\Exception\EntityNotFoundClangdomException;

use BBL\WebBundle\Exception\UnauthorizedClangdomException;

use BBL\WebBundle\Entity\Video;

use BBL\WebBundle\Entity\Music;

use BBL\WebBundle\Entity\Post;

use BBL\WebBundle\Exception\WrongParamsClangdomException;

use BBL\WebBundle\Entity\File;

use BBL\WebBundle\Entity\Picture;
use BBL\WebBundle\Utilities\Helper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BBL\WebBundle\Entity\User;
use BBL\WebBundle\Entity\Konto;

class FileController extends Controller
{
	public function createEventAction()
	{	
		$session = $this->get('session');
		if($session->get('state') != 'logged') throw new UnauthorizedClangdomException();
		$em = $this->getDoctrine()->getManager();
		$kontoRepo = $this->getDoctrine()->getRepository('BBLWebBundle:Konto');
		$konto = $kontoRepo->findOneByIdkonto($session->get('konto'));
		
		$artistRepo = $this->getDoctrine()->getRepository('BBLWebBundle:Artist');
		$profilRepo = $this->getDoctrine()->getRepository('BBLWebBundle:Profil');
		$name = $this->getRequest()->get("Name");
		$info = $this->getRequest()->get("Info");
		$artNames = json_decode($this->getRequest()->get("Artists"));
		$artIds   = json_decode($this->getRequest()->get("id"));
		$date = $this->getRequest()->get("Date");
		$date = $date = date("Y-m-d",strtotime($date));
		$date = new \DateTime($date);
		$time = new \DateTime($this->getRequest()->get("Time"));
		if($name == null) return new Response("The Event needs a Name", 405);
		
		$post = new Post();
		$post->setName($name);
		$post->setKonto($konto);
		$event = new Event();
		$event->setPost($post);
		$profil = new Profil();
		$str = ("/".$name);
    	$str = mb_strtolower(preg_replace('/\s+/', '', $str), 'UTF-8'); //remove all whitespaces and set to lower case
    	$link = $str;
    	for($i = 1; $profilRepo->findOneByLink($link) != null; $i++)
    	{
    		
    		$link = ($str.$i);
    	}
    	$profil->setLink($link);
		$event->setProfil($profil);
		if($info != null) $event->setInfo($info);
		if($date != null) $event->setStartdate($date);
		if($time != null) $event->setTime($time);
		if($artIds != null){
			foreach($artIds as $id)
			{
				$artistEnti = $artistRepo->findOneByIdartist($id);  //must get better
				$event->addKonto($artistEnti->getKonto());
			}
		}
	
		$em->persist($event);
		$em->persist($post);
		$em->persist($profil);
		$em->persist($konto);
		$em->flush();
		$response = new Response();
		$response->setStatusCode(200);
		return $response;
		
	}
 
    public function testAction()
    {
    	$session = $this->get('session');
    	if($session->get('state') == 'logged') return $this->render('BBLWebBundle:User:test.html.twig');
    	else return new Response('<html> <body>Not checked in </body> </html>');
    }
    
    public function musicTestAction()
    {
    	$session = $this->get('session');
    	if($session->get('state') == 'logged') return $this->render('BBLWebBundle:User:testM.html.twig');
    	else return new Response('<html> <body>Not checked in </body> </html>');
    }
    
	public function videoTestAction()
    {
    	$session = $this->get('session');
    	if($session->get('state') == 'logged') return $this->render('BBLWebBundle:User:testV.html.twig');
    	else return new Response('<html> <body>Not checked in </body> </html>');
    } 
    
    public function picUpAction()
    {
    	$upFile =  $this->getRequest()->files->get("datei");
    	if(!ValueCheck::checkExtension($upFile, array("jpg", "jpeg", "gif", "png"))) throw new WrongParamsClangdomException(); //another exception pls
    	$session = $this->get('session');
    	if($session->get('state') == 'logged') throw new UnauthorizedClangdomException();
    	$em = $this->getDoctrine()->getManager();
    	$kontoRepo = $this->getDoctrine()->getRepository('BBLWebBundle:Konto');
    	$konto = $kontoRepo->findOneByIdkonto($session->get('konto'));
    	$profil = $konto->getProfil();
    	if($konto == null) throw new EntityNotFoundClangdomException();
    	
    	$oldPic = $konto->getProfil()->getPicture();
    	if($oldPic == null){
    		$fileEntity = new File();
    		$fileEntity->setFile($upFile);
    		$fileEntity->upload('picture'.'.'.$upFile->guessExtension(), $profil->getLink());
    		$newPic = new Picture();
    		$newPic->setFile($fileEntity);
    		$profil->setPicture($newPic);
    		
    		$em->persist($fileEntity);
    		$em->persist($newPic);
    		$em->persist($profil);
    		$em->flush();
    	}
    	else {
    		$fileEntity = $oldPic->getFile();
    		$fileEntity->setPath('');
    		$fileEntity->setFile($upFile);
    		$fileEntity->upload('picture'.'.'.$upFile->guessExtension(), $profil->getLink());
    		$em->persist($fileEntity);
    		$em->flush();
    	}
    	return new Response("Thats just for testing");
    	
    }
    
    public function musicUpAction()
    {
    	$name = $this->getRequest()->get("name");
    	if(trim($name) == "") throw new WrongParamsClangdomException();
    	$upFile = $this->getRequest()->files->get("datei");
    	$session = $this->get('session');
    	if($session->get('state') != 'logged') throw new UnauthorizedClangdomException();
    	$em = $this->getDoctrine()->getManager();
    	$kontoRepo = $this->getDoctrine()->getRepository('BBLWebBundle:Konto');
    	$konto = $kontoRepo->findOneByIdkonto($session->get('konto'));
    	$post = new Post();
    	$post->setName($name);
    	$post->setKonto($konto);
    	$music = new Music();
    	$music->setPost($post);
    	$file = new File();
    	$file->setFile($upFile);
    	$file->upload(uniqid().'.'.'mp3', $session->get('link'));
    	$music->setFile($file);
    	$em->persist($file);
    	$em->persist($music);
    	$em->persist($post);
    	$em->persist($konto);
    	$em->flush();
    	return new Response();
    }
   
    
    public function videoUpAction()
    {
    	$session = $this->get('session');
    	//if session not allowed
    	$name = $this->getRequest()->get("name");
    	if(trim($name) == "") throw new WrongParamsClangdomException();
    	$link = $this->getRequest()->get("link");
    	if(trim($link) == "") throw new WrongParamsClangdomException();
    	
    	$em = $this->getDoctrine()->getManager();
    	$kontoRepo = $this->getDoctrine()->getRepository('BBLWebBundle:Konto');
    	$konto = $kontoRepo->findOneByIdkonto($session->get('konto'));
    	//Taghandling
    	$tag = $tagRepo->findOneByName(mb_strtolower($konto->getName()));
    	$tag2 = $tagRepo->findOneByName(mb_strtolower($name));
    	if($tag2 == null){
    		$tag2 = new Tags();
    		$tag2->setName(mb_strtolower($name));
    		$em->persist($tag2);
    	}
    	
    	$post = new Post();
    	$post->setName($name);
    	$post->setKonto($konto);
    	$post->addTagstag($tag);
    	$post->addTagstag($tag2);
    	$video = new Video();
    	$video->setUrl($link);
    	$video->setPost($post);
    	$em->persist($video);
    	$em->persist($post);
    	$em->persist($konto);
    	$em->flush();
    	return new Response("Thats just for testing");
    }
}
