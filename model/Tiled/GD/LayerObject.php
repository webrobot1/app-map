<?php
namespace Edisom\App\map\model\Tiled\GD;

class LayerObject extends \Edisom\App\map\model\Tiled\LayerObject
{
	public \GdImage $resource;
	
	function load():static
	{
		parent::load();	
		
		if($this->tile_id)
		{
			if(!static::$keys[Tileset::class]['tiles'][$this->tile_id])
				throw new \Exception('Не найдено изображение для tile_id '.$this->tile_id);
				
			//$this->resource = static::$keys[TilesetXml::class]['tiles'][$this->tile_id];		
			$this->resource = Png::createEmpty($this->width, $this->height);
			imagecopyresized(
				$this->resource, 
				static::$keys[Tileset::class]['tiles'][$this->tile_id], 
				0, 
				0, 
				0, 
				0, 
				$this->width, 
				$this->height, 
				imagesx(static::$keys[Tileset::class]['tiles'][$this->tile_id]), 
				imagesy(static::$keys[Tileset::class]['tiles'][$this->tile_id])
			); 
			
			if($this->rotation)
			{
				
				$transparent = imagecolorallocatealpha($this->resource, 0, 0, 0, 127);
				if(
					!$this->resource = imagerotate(
						$this->resource,
						$this->rotation*-1,
						$transparent
					)
				)
					throw new \Exception('не удается повернуть объект '.$this->object_id);
				imagecolortransparent($this->resource, $transparent);
	
				
				/* 	
					в Tiled отрисовка  идет от левого нижнего угла в верх (плюс она сначала накладыается на координаты потом поворачивается уже на фоне). 
					в PHP от верхнего левого вниз (и сначала надо повернуть потом наложить)	
					Код вносит корректировки в координаты X и Y на PHP
				*/
				
				if($this->rotation>90 && $this->rotation<180)
				{					
					$this->x -= imagesx($this->resource) * sin(deg2rad($this->rotation));
				}			
				elseif($this->rotation>=180 && $this->rotation<=270)
				{					
					$this->x -= imagesx($this->resource);
				}
				elseif($this->rotation>270)
				{
					$this->x -= imagesx($this->resource) - imagesy($this->resource)*cos(deg2rad($this->rotation));
				}
				
				if($this->rotation<90)
				{
					$this->y -= $this->height*cos(deg2rad($this->rotation));
				}
				elseif($this->rotation>180 && $this->rotation<=270)
				{
					$this->y -= imagesy($this->resource) + $this->height*(cos(deg2rad($this->rotation)));
				}	
				elseif($this->rotation>270)
				{
					$this->y -= imagesy($this->resource);
				}					
			}
			else	
				$this->y -= $this->height;
			
			if($this->horizontal){
				if(!imageflip($this->resource, IMG_FLIP_HORIZONTAL))
					throw new \Exception('не удается отразить по горизонтали объект '.$this->object_id);
			}						
			
			if($this->vertical)
			{
				if(!imageflip($this->resource, IMG_FLIP_VERTICAL))
					throw new \Exception('не удается отразить по горизонтали объект '.$this->object_id);
			}						
		}
		elseif($this->text)		// слой текст
		{
			$this->resource = Png::createEmpty($this->width, $this->height);
			$property = array_column($this->property, 'name');
			$color = str_pad(ltrim($this->property[array_search('color', $property)]->value, '#'), 6, 0);
			$size = $this->property[array_search('pixelsize', $property)]->value;
			
			if(
				!imagettftext(
					$this->resource,
					$size, 							
					(float)$this->rotation,			// поворот
					0,
					$size,							// в PHP с нижнего левого угла первого символа идет отрисовка. В tiled сверху
					imagecolorallocate($this->resource, hexdec(substr($color,0,2)), hexdec(substr($color,2,2)), hexdec(substr($color,4,2))),
					(substr(php_uname(), 0, 7) == "Windows"?'C:/Windows/Fonts/':'/usr/share/fonts/truetype/msttcorefonts/').$this->property[array_search('fontfamily', $property)]->value.'.ttf',
					$this->text
				)
			)
				throw new \Exception('не удается наложить текст: '.$this->text); 		
		}
		
		return $this;
	}
}