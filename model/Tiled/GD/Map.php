<?php
namespace Edisom\App\map\model\Tiled\GD;

class Map extends \Edisom\App\map\model\Tiled\Map
{		

	function load():static
	{
		parent::load();		
		$this->resource = Png::createEmpty($this->width*$this->tilewidth, $this->height*$this->tileheight);

		foreach($this->layer as $layer)
		{
			if(!$layer->visible) continue;
		
			imagecopy(
				$this->resource,
				$layer->resource, 
				0, 
				0,
				0, 
				0, 
				$this->width*$this->tilewidth, 
				$this->height*$this->tileheight
			);	
		}
		
		return $this;
	}
}