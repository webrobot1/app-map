<?php
namespace Edisom\App\map\model\Tiled\Xml;

class LayerObject extends \Edisom\App\map\model\Tiled\LayerObject
{
	public ?int $gid = null;			// за этим индентификатором скрывается ссылка на объект из набора тайлов
	
	function parse():static
	{
		Xml::deserialize($this);

		if($this->gid)
		{
			$this->horizontal 	= ($this->gid & FLIPPED_HORIZONTALLY_FLAG?1:null);
			$this->vertical 	= ($this->gid & FLIPPED_VERTICALLY_FLAG?1:null);
		}
		
		return $this;
	}
	
	function save():void
	{
		if($this->gid && ($id = $this->gid & ~(FLIPPED_HORIZONTALLY_FLAG | FLIPPED_VERTICALLY_FLAG)))
		{
			if(!$this->tile_id = TilesetTile::$tileid[$id])
			{
				throw new \Exception("не найден tile_id ".$id);	
			}
		}
		
		parent::save();
	}
}