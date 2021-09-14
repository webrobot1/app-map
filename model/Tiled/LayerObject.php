<?php
namespace Edisom\App\map\model\Tiled;

// колайдеры созданные каждому объекту на непосрведсвенно карте 
// todo  - сделать обработку gid что бы ссылаться на tiled с учетом их поворота

#[\Attribute]
class LayerObject extends Loader
{
	const TABLE = 'map__layer__object';
				
	#[LayerObjectProperty]
	public array $property = array();	// можно дать пользовательские поля объектв из Слоя объектов

	// объект - текст
	function __construct
	(
		public ?int $object_id = null,
		public ?int $layer_id = null,
		
		public ?int $tile_id = null,
		public ?int $horizontal = null,	
		public ?int $vertical = null,
		
		public ?float $x = null,
		public ?float $y = null,
		public ?float $width = null, 
		public ?float $height = null,
		public ?int $visible = 1,		
		
		#[Ellipse]
		public ?string $ellipse = null,
		#[Polygon]
		public $polygon = null,
		
		public ?float $rotation = null, // поворот
		
		#[LayerObjectText]
		public $text = null
	){
		parent::__construct();
		if($this->polygon){
			$this->polygon = $this->query('SELECT REPLACE(REPLACE(ST_AsWKT(`polygon`), "))", ""), "POLYGON((", "") as polygon FROM '.static::TABLE.' WHERE object_id = '.$this->object_id)[0]['polygon'];	
		}
	}
	
	function save():void
	{
		// просто так полигон как текст не сохранить без функция mysql
		if($polygon = $this->polygon){
			$this->width = 0;
			$this->height = 0;	
			unset($this->polygon);
		}

		if($this->rotation)
			$this->rotation = $this->rotation%360;
		
		if(!is_null($this->width) && !is_null($this->height))
			parent::save();
		
		if($polygon)
		{
			$this->query('UPDATE '.static::TABLE.' SET polygon = ST_GeomFromText("POLYGON(('.$polygon.'))") WHERE object_id = '.$this->object_id);
		}
	}
}