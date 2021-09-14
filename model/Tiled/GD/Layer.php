<?php
namespace Edisom\App\map\model\Tiled\GD;

class Layer extends \Edisom\App\map\model\Tiled\Layer
{ 	
	function load():static
	{
		// для отрисовки НЕ нужны данные по данынм свйоствам этого класса где указаны атрибуты Reflection
		unset(static::$keys[static::class]['property']);
		
		parent::load();	
		
		$this->resource = Png::createEmpty(static::$keys[Map::class]['width']*static::$keys[Map::class]['tilewidth'], static::$keys[Map::class]['height']*static::$keys[Map::class]['tileheight']);
		if($this->image)							// слой изображений
		{
			imagecopy(
				$this->resource, 
				Png::createFrom(static::$folder.$this->image), 
				(float)$this->offsetx, 
				(float)$this->offsety, 
				0, 
				0, 
				static::$keys[Map::class]['width']*static::$keys[Map::class]['tilewidth'], 
				static::$keys[Map::class]['height']*static::$keys[Map::class]['tileheight']
			);
		}
		elseif($this->data->tiles)					// слой тайлов 
		{
			foreach($this->data->tiles as $count=>$tile)
			{
				// если в данной точки не пусто
				if($tile->tile_id)
				{
					if(!$image = static::$keys[Tileset::class]['tiles'][$tile->tile_id])
						throw new \Exception('Не нейдено изображение для tile_id '.$tile->tile_id);

					imagecopy(
						$this->resource,
						$image, 
						($count%static::$keys[Map::class]['columns']*static::$keys[Map::class]['tilewidth'])+$this->offsetx, 
						(ceil(($count+1)/static::$keys[Map::class]['columns'])-1)*static::$keys[Map::class]['tileheight']+$this->offsety-(imagesy($image)-static::$keys[Map::class]['tileheight']), // в Tiled отрисовка идет вверх от точки старта. в PHP - вниз
						0, 
						0, 
						imagesx($image), 
						imagesy($image)
					);	
				}
			}
		}
		elseif($this->object)						// слой объектов
		{
			foreach($this->object as $count=>$object)
			{
				if(!$object->visible) continue;
				
				if($object->resource)
				{		
					imagecopy(
						$this->resource,
						$object->resource, 
						$object->x+$this->offsetx,  
						$object->y+$this->offsety,            
						0, 
						0, 
						imagesx($object->resource), 
						imagesy($object->resource)
					);
				}
			}
		}
		
		return $this;
	}
}