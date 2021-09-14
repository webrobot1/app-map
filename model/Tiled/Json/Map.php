<?php
namespace Edisom\App\map\model\Tiled\Json;

class Map extends \Edisom\App\map\model\Tiled\Map
{	
	function json():array
	{
		$array = json_decode(json_encode(parent::load()), true);
		
		if($array['tilesets'] = $array['tileset'])
		{
			unset($array['tileset']);
			foreach($array['tilesets'] as &$tileset)
			{
				if($tileset['tiles'] = $tileset['tile'])
					unset($tileset['tile']);
				
				foreach($tileset['tiles'] as $key=>&$tile)
				{
					$tile['id'] = $key;
					
					if($tile['object'])
					{
						$tile['objectgroup']['objects'] = $tile['object'];
						unset($tile['object']);
					}					
					if($tile['animation'] = $tile['frame'])
					{
						unset($tile['frame']);
					}	
				}
			}
		}
		
		if($array['layers'] = $array['layer']){
			unset($array['layer']);
			
			foreach($array['layers'] as &$layer)
			{
				if($layer['data']['tiles'])
					$layer['data'] = array_column($layer['data']['tiles'], 'tile_id');		
				else
					unset($layer['data']);
				
				if($layer['objects'] = $layer['object'])
					unset($layer['object']);
				
				$layer['width'] = $this->width;
				$layer['height'] = $this->height;
				
				// необязательно?
				$layer['tilewidth'] = $this->tilewidth;
				$layer['tileheight'] = $this->tileheight;
			}
		}
		
		return $array;
	}
}