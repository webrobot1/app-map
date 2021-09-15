<?php
namespace Edisom\App\map\model\Tiled\Xml;

class Tileset extends \Edisom\App\map\model\Tiled\Tileset
{
	// фаил .tsx
	public ?string $source = null;

	function parse():static
	{
		Xml::deserialize($this);

		// если набор тайлов вынесен во внешний tsx фаил
		if ($this->source && ($filename = static::$folder.$this->source))
		{
			if(!file_exists($filename))
				throw new \Exception("не найден фаил набора ".$filename);

			if(($source = file_get_contents($filename)) && ($folder = static::$folder))
			{
				static::$folder = dirname($filename).'/';
				Xml::deserialize($this, $source);
				
				// не обязательно но раз везде относительные то и тут пусть будет
				// и это полезно когда на локале тест идет (там же абсолютный путь не как у сервера)
				if($this->image)
				{
					$this->image = ltrim(str_replace($folder, '', static::$folder.$this->image), '/');
				}
				
				static::$folder = $folder;
				unlink($filename); //удалим его за ненадобностью (все выгрузки обратно в tmx будут без внешних фаилов tsx)
			}
			else
				throw new \Exception("не могу прочитать содержимое фаила ".$filename);
		} 
		
		// заберем из объекта картинки ссылку на картинку и цвет прозрачности 
		// обязательно абсолютный путь тк если это tsx фаил то относитьно него путь будет другим
		// и если внутри этого класса будут наследники с адресами картинок и тп - тоже сохранять асболютный пусть
		if($this->image && $this->image->trans)
		{
			$this->trans = $this->image->trans;
		}

		if(!$this->tilecount)
			throw new \Exception("количество в наборе tileset ".$this->name." равно 0");	

		// изменим индекс тайла (а тут лишь тайлы с колайдерами или другими не дефолтными параметрами)  на его порядкоывй номер (может как то проще можно но  таку делаю)
		// а вместе с тем всех анимаций так же номер откоректируем
		$tiles = array();
		foreach($this->tile as $key=>$tile)
		{
			foreach($tile->frame as &$frame)
			{
				$frame->tileid = ($frame->tileid + $this->firstgid);
			}
			$tiles[$tile->id] = $tile;
			$tiles[$tile->id]->id = $tiles[$tile->id]->id + $this->firstgid;
		}
		$this->tile = $tiles;

		// принудительно создадим все остальные тайлы (пусть и с дефолтными параметрами) которыйх нет
		for($i = 0, $j = $this->firstgid; $i<$this->tilecount; $i++, $j++)
		{	
			if(empty($this->tile[$i]))
			{
				$this->tile[$i] = new TilesetTile();
				$this->tile[$i]->id = $j;
			}
			$this->tile[$i]->sort = $i;			
		}				
		ksort($this->tile);
	
		return $this;		
	} 
}