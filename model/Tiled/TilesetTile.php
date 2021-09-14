<?php
namespace Edisom\App\map\model\Tiled;

// координаты с изоюбражения tilemap или отдельное изображение (спрайт) с координатами 
#[\Attribute]
class TilesetTile extends Loader 
{
	const TABLE = 'map__tileset__tile';

	// коллайдеры
	#[TilesetTileObject]
	public array $object = array();
	
	// анимация
	#[TilesetTileFrame]
	public array $frame = array();	

	// пользоваптельские поля отдельного спрайта или tilemap 
	#[TilesetTileProperty]
	public array $property = array();	
			

	function __construct
	(		
		public ?int $tile_id = null,
		public ?int $tileset_id  = null,
		
		public ?string $type = null,		// при редакторировании тайлов и спрайтов отдельного тайла есть поле тип (зачем - хз, произвольное значение)
		public ?float $probability = null, 	// при редакторировании тайлов и спрайтов отдельного тайла есть поле вероятностьь (полагаю для рандомного заполнения земли цветочками и тп)
		
		#[Image]
		public ?string $image = null, 		// спрайт (те не в виде tilemaps тут а отд. изображение)
		public ?int $sort  = null,
	){
		parent::__construct();
	}
}