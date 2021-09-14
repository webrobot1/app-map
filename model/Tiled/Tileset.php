<?php
namespace Edisom\App\map\model\Tiled;

// набор тайловых изображений или спрайтов (отд. изображений)
#[\Attribute]
class Tileset extends Loader 
{	
	const TABLE = 'map__tileset';
		
	########авто подсчет тайлов#####	
	public ?int $firstgid = 1;
	public ?int $columns = null;
	public ?int $tilecount = null;	
	###############################
	
	#[TilesetTile]
	public array $tile = array();
	  
	function __construct
	(
		public ?int $tileset_id = null, 		
		public ?int $map_id = null, 		
		

		public ?string $name = null,		
		public ?int $tilewidth = null,
		public ?int $tileheight = null,
		public ?int $spacing = null,
		public ?int $margin = null,		
		
		// изображение тайлов (tilemaps)
		#[Image]
		public ?string $image = null,					
		public ?string $trans = null,	
	){
		parent::__construct();
	}		

	function load():static
	{
		parent::load();
		$this->firstgid = $this->tile[0]->tile_id;
		
		if($this->image)
		{
			if(($filename = static::$folder.$this->image) && !file_exists($filename))
				throw new \Exception("не найден фаил ".$filename);
						
			if($image = getimagesize($filename))
			{
				$this->imagewidth = $image[0];
				$this->imageheight  = $image[1];
				
				// todo учитывать margin и spacing
				$this->columns = round($this->imagewidth / $this->tilewidth);
				$this->tilecount = round($this->imageheight / $this->tileheight) * $this->columns;
			}
		}
		
		return $this;
	}
}