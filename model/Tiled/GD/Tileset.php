<?php
namespace Edisom\App\map\model\Tiled\GD;

class Tileset extends \Edisom\App\map\model\Tiled\Tileset
{	
	function load():static
	{
		parent::load();	
		
		// если тайл не является спрайтом (те тайловый набор состоит из плиток) заполним каждый тайил как кусок изображения
		if($this->image)
			$resource = Png::createFrom(static::$folder.$this->image);

		foreach($this->tile as $count=>$tile)
		{
			if($tile->image)	//если тайил - это спрайт (обычное изображение)
			{
				static::$keys[static::class]['tiles'][$tile->tile_id] = Png::createFrom(static::$folder.$tile->image);
			}
			elseif(static::$keys[static::class]['tiles'][$tile->tile_id] = Png::createEmpty($this->tilewidth , $this->tileheight))
			{
				imagecopy
				(
					static::$keys[static::class]['tiles'][$tile->tile_id], 
					$resource, 
					($this->margin*-1), 
					($this->margin*-1), 
					($count%$this->columns*($this->tilewidth+$this->spacing)), 
					(ceil(($count+1)/$this->columns)-1)*($this->tileheight+$this->spacing), 
					$this->tilewidth+$this->margin, 
					$this->tileheight+$this->margin
				);
			}
		}
		
		return $this;
	}
}