<?php
namespace Edisom\App\map\controller;

class BackendController extends \Edisom\Core\Backend
{	
	function index()
	{		
		$this->view->assign('maps', $this->model->get());
		$this->view->display('main.html');
	}		
	
	function preview()
	{
		if($resource = (new \Edisom\App\map\model\Tiled\GD\Map($this->id))->load()->resource)
		{
			header('Content-Type: image/png');
			imagepng($resource);
			imagedestroy($resource); 		
		}
	}
	
	function online()
	{	

		//die(print_r((new \Edisom\App\map\model\Tiled\Json\Map($this->id))->json()));

		//$this->view->assign('map', json_encode((new \Edisom\App\map\model\Tiled\MapXml($this->id))->load(true)));
		//$this->view->display('online.html');
		
		$this->view->display('online.html');
	}	
	
	function json()
	{	
		die(json_encode((new \Edisom\App\map\model\Tiled\Json\Map(1))->json()));
	}	
	
	function replace()
	{	
		if($_FILES){
			$this->model->replace($this->id);
			$this->redirect();
		}
		else{
			if($this->id){
				$this->view->assign('map', $this->model->get(['map_id'=>$this->id])[0]);
			}
			$this->view->display('map_edit.html');
		}
	}											
}