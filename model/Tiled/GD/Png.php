<?php
namespace Edisom\App\map\model\Tiled\Gd;

abstract class Png
{	
	//  можно IMG_NEAREST_NEIGHBOUR, но он чуть уменьшает изображение		
	private CONST INTER = IMG_BOX;	
		
	public static function createEmpty(int $width,int $height):\GdImage
	{
		if($resource = imagecreatetruecolor($width, $height))
		{	
			// Сделаем фон прозрачным
			//imagealphablending ($resource, true );
			if (function_exists ( 'imageantialias' )) 
			  imageantialias ( $resource, false );
		  
			imagesetinterpolation($resource, self::INTER);
			
			$transparent = imagecolorallocatealpha($resource, 0, 0, 0, 127);
		  
			imagesavealpha ($resource, true );
			imagefill($resource, 0, 0, $transparent);
			imagecolortransparent($resource, $transparent);
			
			return $resource;
		}
	}

	public static function createFrom(string $file):\GdImage
	{
		if(file_exists($file))
		{
			switch(pathinfo($file, PATHINFO_EXTENSION))
			{
				case 'png' :
					$resource = imagecreatefrompng ( $file );
				break;
				case 'jpg' :
				case 'jpe' :
				case 'jpeg' :
					$resource = imagecreatefromjpeg ( $file );
				break;
				case 'gif' :
					$resource = imagecreatefromgif ( $file );
				break;					
				default :
					throw new \Exception("Неизвестный тип изображения ".$file);
				break;	
			}
			if (function_exists ( 'imageantialias' )) 
			  imageantialias ( $resource, false );
		  
			imagesetinterpolation($resource, self::INTER);
			
			imagecolortransparent($resource, 0);	
			return $resource;
		}
		else
			throw new \Exception("не найден фаил ".$file);
	}
}