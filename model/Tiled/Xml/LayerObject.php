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
			$this->horizontal 	= ($this->gid & Xml::FLIPPED_HORIZONTALLY_FLAG?1:null);
			$this->vertical 	= ($this->gid & Xml::FLIPPED_VERTICALLY_FLAG?1:null);
		}
		
		return $this;
	}
	
	function save():void
	{
		if($this->gid && ($id = $this->gid & ~(Xml::FLIPPED_HORIZONTALLY_FLAG | Xml::FLIPPED_VERTICALLY_FLAG)))
		{
			if(!$this->tile_id = TilesetTile::$tileid[$id])
			{
				throw new \Exception("не найден tile_id ".$id);	
			}
		}
		
		parent::save();
	}
}