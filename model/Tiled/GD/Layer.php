<?php
namespace Edisom\App\map\model\Tiled\GD;

class Layer extends \Edisom\App\map\model\Tiled\Layer
{ 	
	public \GdImage $resource;
	
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

					// если tile поернут то надо скопировать его в новый GD объект и вставить его (типа как объект)
					if($tile->horizontal || $tile->vertical)
					{
						$new_image = Png::createEmpty(imagesx($image), imagesy($image));
						imagecopy(
							$new_image,
							$image,
							0,
							0,
							0,
							0,
							imagesx($image),
							imagesy($image)
						);
						
						if($tile->horizontal){
							if(!imageflip($new_image, IMG_FLIP_HORIZONTAL))
								throw new \Exception('не удается отразить по горизонтали tile '.$tile->tile_id);
						}						
						
						if($tile->vertical)
						{
							if(!imageflip($new_image, IMG_FLIP_VERTICAL))
								throw new \Exception('не удается отразить по горизонтали tile '.$tile->tile_id);
						}
						
						$image = $new_image;
					}

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
				
				if(!empty($object->resource))
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
		
		if($this->opacity < 1)
		{
			imagealphablending($this->resource, false); // imagesavealpha can only be used by doing this for some reason
			imagesavealpha($this->resource, true); 		// this one helps you keep the alpha. 		
			imagefilter($this->resource, IMG_FILTER_COLORIZE, 0, 0, 0, 127*(1 - $this->opacity)); // the fourth parameter is alpha
		}
		
		return $this;
	}
}