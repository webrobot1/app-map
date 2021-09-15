<?php
namespace Edisom\App\map\model\Tiled\Xml;

abstract class Xml 
{	
	public static \XMLReader $reader;

	const "FLIPPED_HORIZONTALLY_FLAG" = 0x80000000;
	const "FLIPPED_VERTICALLY_FLAG" = 0x40000000;
	const "FLIPPED_DIAGONALLY_FLAG" = 0x20000000;

	final public static function deserialize(object &$object, string $source = null):void
	{
		if($source)
		{
			if(isset(static::$reader))
				$reader = static::$reader;
			
			if(!static::$reader = \XMLReader::XML($source))
				throw new \Exception("не удается распарсить ".$source);	
		}
	
		if(static::$reader->nodeType == \XMLReader::NONE)
			static::$reader->read();
	
		$root = static::$reader->name;	
		$empty = static::$reader->isEmptyElement;
		
		if(static::$reader->hasAttributes)
			static::attrubute($object);		
	
		// если у элемента нет вложенных элементов то нет и закрывающего тега то вернем сразу 
		if($empty)
		{
			if($source)
			{
				static::$reader->close();
				if($reader)
					static::$reader = $reader;
			}
			return;
		}
		
		while(static::$reader->read())
		{
			if (static::$reader->nodeType == \XMLReader::END_ELEMENT) 
			{
				if(static::$reader->name == $root)
					break;
				
				continue; 
			}
			
			if (static::$reader->nodeType == \XMLReader::ELEMENT) 
			{ 
				static::element($object);
			}			
		}
		
		if($source)
		{
			static::$reader->close();
			if($reader)
				static::$reader = $reader;
		}
	}
	
	// парсим атрибуты
	private static function attrubute(object &$object) :void
	{
		while(static::$reader->moveToNextAttribute())
		{
			if(($property = static::$reader->name) && (property_exists($object, $property)))
			{
				$object->$property = static::$reader->value; 
			}
		}
		static::$reader->read();
	}	
	
	// парсим элемент
	private static function element(object &$object)
	{
		if(
			($property = static::$reader->name) 
				&& 
			(\Edisom\App\map\model\Tiled\Loader::$keys[$object::class]['objects'][$property]) 
		)
		{
			if($next_object = new \Edisom\App\map\model\Tiled\Loader::$keys[$object::class]['objects'][$property])
			{
				// добавим в класс родитель на метод deserialize  если таковой отствует что бы он парсил шел дальше по цепочке
				if(method_exists($next_object, 'parse'))
				{
					$next_object->parse();
				}
				else
					Xml::deserialize($next_object);
								
				if(gettype($object->$property) == 'array')
					$object->$property[] = $next_object; 
				else
					$object->$property = $next_object;	
			}			
		}
	}
		
	final public static function text():string
	{
		return trim(static::$reader->readString()); 
	}	
}