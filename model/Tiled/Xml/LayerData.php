<?php
namespace Edisom\App\map\model\Tiled\Xml;

class LayerData extends \Edisom\App\map\model\Tiled\LayerData
{
	public ?string $text = null;		
	public ?string $encoding = 'csv';
	public ?string $compression = null;
	
	public function parse():static
	{
		$this->text = Xml::text();
		Xml::deserialize($this);

		switch($this->encoding)
		{
			case 'base64':
				$this->text = base64_decode($this->text);
				switch($this->compression)
				{
					case 'zlib' :
						$this->text = gzuncompress ( $this->text );
					break;
					case 'gzip' :
						$this->text = gzdecode ( $this->text );
					break;
					case 'bzip2' :
					case 'bz2' :
						$this->text = bzdecompress ( $this->text );
					break;
				}
			break;			
			case 'csv':
				//$this->text = base64_decode($this->text);
			break;
		}

		$explode = explode(',', $this->text);
		
		for($i=0; $i<count($explode); $i++)
		{
			$this->tiles[] = new LayerTile(
				($explode[$i] & ~(FLIPPED_HORIZONTALLY_FLAG | FLIPPED_VERTICALLY_FLAG | FLIPPED_DIAGONALLY_FLAG)),
				($explode[$i] & FLIPPED_HORIZONTALLY_FLAG?1:null),
				($explode[$i] & FLIPPED_VERTICALLY_FLAG?1:null),
				($explode[$i] & FLIPPED_DIAGONALLY_FLAG?1:null)
			);
			
		} 		
		return $this;
	}
}