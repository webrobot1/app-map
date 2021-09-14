<?php
namespace Edisom\App\map\model\Tiled;

#[\Attribute]
class Layer extends Loader
{ 
	const TABLE = 'map__layer';
	private static $count = 0;

	#[LayerData]
	public ?object $data = null;   					// тип не ставим тк у нас метод load(true)  может вернуть его как массив
			
	#[LayerProperty]
	public array $property = array();   

	#[LayerObject]
	public array $object = array();	
	
	// тк у нас карты фиксированного размера тут эти данные не нужны
	//public ?float $width = null,
	//public ?float $height = null,
	// есть еще Parallax Scrolling Factor это типа небо как быстро скролится с движением камеры (по умолчанию любой слой движется синхронно с ней)	
	
	
	protected static function get(array $where)
	{
		return static::query('SELECT * FROM '.static::TABLE.' WHERE '.static::explode($where, 'AND').' ORDER BY sort ASC');
	}	

	function __construct
	(	
		public ?int $layer_id = null,		
		public ?int $map_id = null,	
		public ?int $parent_id = null,	
		public string $type = "tiles",		
		
		public ?string $name = null,
		
		public ?float $offsetx = null,	
		public ?float $offsety = null,
		public ?int $visible = 1,
		public ?float $opacity = 1,
				
		#[Image]
		public ?string $image = null,		// оно имеет картинку (те оно вне tileset должно быть иначе не имеет смысла тк проще из тайлов добавить)	
		public ?int $sort = null
	){
		if($sort === null)
			$this->sort = static::$count;
		
		static::$count++;
						
		parent::__construct();
	}
}