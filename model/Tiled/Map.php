<?php
namespace Edisom\App\map\model\Tiled;

DEFINE("FLIPPED_HORIZONTALLY_FLAG", 0x80000000);
DEFINE("FLIPPED_VERTICALLY_FLAG", 0x40000000);
DEFINE("FLIPPED_DIAGONALLY_FLAG", 0x20000000);
	
#[\Attribute]
class Map extends Loader
{	
	const TABLE = 'map';	

	public ?string $version = null;
	public ?string $tiledversion = null;
	public ?string $orientation = null;
	public ?string $renderorder = null;
	
	public ?int $width = null;
	public ?int $height = null;
	public ?int $tilewidth = null;
	public ?int $tileheight = null;		

	//public int $infinite = null;
	//public int $nextlayerid = null;
	//public int $nextobjectid = null;	
	
	#[MapProperty]
	public array $property = array();		// пользовательские свойство из настроек карты		
	#[Tileset]
	public array $tileset = array();		// все наборы тайлов (tilemaps или спрайты, могут быть в виде ссылки на фил .tsx)	
	#[Layer]
	public array $layer = array();			// слои тайлов в корне
		
	function __construct (public int $map_id)
	{
		if((static::$folder = SITE_PATH."/data/".static::app().'/'.$map_id.'/') && !file_exists(static::$folder))
			throw new \Exception('На найден каталог для работы с картой '.static::$folder);	

		parent::__construct();		
	}
	
	// тк карта на вершине цепочки то ее метод загрузки отчличается (надо сначала впервый раз откуда то получить данные)
	function load():static
	{
		if(($fileds = array_filter(array_intersect_key((array)$this, array_flip(static::$keys[static::class]['primary'])))) && $data = end(static::get($fileds)))
		{
			foreach($data as $key=>$value)
			{
				if(property_exists($this, $key))
				{
					// данные карты видны глобально из любой точки
					// тк ее данные требуются в разных моделях
					static::$keys[static::class][$key] = $this->$key = $value;	
				}
			}			
		}
		else
			throw new \Exception("Невозможно загрузить карту ".implode(',', $fileds));
		
		static::$keys[static::class]['columns'] = round($this->width * $this->tilewidth / $this->tilewidth);
		
		parent::load();
		
		// если скрыт верхний слой то и нижнее надо скрыть
		
		$visible = [];
		foreach($this->layer as &$layer)
		{
			if(!$layer->visible)
				$visible[$layer->layer_id] = $layer->visible;
			elseif($layer->parent_id)
				$layer->visible = $visible[$layer->layer_id] = isset($visible[$layer->parent_id])?$visible[$layer->parent_id]:1;
		}
		
		return $this;
	}
	
	function save():void
	{
		static::transaction_start();
			if($this->map_id)
				$this->query('DELETE FROM '.static::TABLE.' WHERE map_id = '.$this->map_id);
			parent::save();			
		static::transaction_stop();
	}
}