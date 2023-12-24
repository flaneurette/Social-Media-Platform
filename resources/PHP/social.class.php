<?php

class social {

	public function __construct() {

	}
	
	public function prepareLINK($value) {
		
		$regexlink = "/[a-zA-Z0-9]+[.?][a-zA-Z0-9]+[.?][a-zA-Z0-9]+[\/?]?[?a-zA-Z0-9]+/i";
		$find = ['http://','https://'];
		$replace = ['',''];
		$value = str_replace($find,$replace,$value);
		$value = $this->clean($value,'encode');
		
		if(preg_match_all($regexlink,$value,$matches)) {
			// $value = '<a href="'.$this->clean($value,'encode').'" target="_blank">'.$value.'</a>';
			$value = preg_replace("/".preg_quote($this->clean($matches[0][0],'encode'),'/')."/",'<a href="https://'.$this->clean($matches[0][0],'encode').'" target="_blank">'.$matches[0][0].'</a>',$matches[0][0]);
		}	
		
		return $value;
	}
	
	public function prepareMixedMedia($value) {
		
		if(($value != '') or ($value !=NULL)) { 
		
			if(stristr(strtolower($value),'.mp3')) {
				return "<br /><audio controls><source src=\"".$this->clean($value,'encode')."\" type=\"audio/mpeg\"></audio>";
				} elseif(stristr(strtolower($value),'.ogg') || stristr(strtolower($value),'.opus')) {
				return "<br /><audio controls><source src=\"".$this->clean($value,'encode')."\" type=\"audio/ogg\"></audio>";
				} else {
				return  "<img src='https://www.twigpage.com".$this->clean($value,'encode')."' class='twig-image' />"; 
			}
		}
	}
	
	public function fetchHTML($url) {
		$channel = curl_init();
		curl_setopt($channel, CURLOPT_URL, $url);
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($channel);
		curl_close($channel);
		if($result) {
			return $result;
			} else {
			return false;
		}
	}
	
	public function prepareHTML($result) {
		
		if(strlen($result) > 1900) {
			
			return $this->clean(substr($value,'encode'),0,1900);
			
		} else {
			
			$value = $this->clean($result,'encode');
		
			// bandcamp integration
			$bandcamp_url = 'https://www.twigpage.com/resources/PHP/bandcamp.php?embed=';
			$bandcamp_error = 'Bandcamp failed to load. Reload the page, or please contact support.';
			
			// initialize an append value.
			$value_add  = '';
			
			if(preg_match_all('/https:\/\/[a-z0-9]+.bandcamp.com\/track\/[a-z0-9-_]+/i', $value, $matches)) {
				
				$gethtml = $this->fetchHTML($bandcamp_url . $this->clean($matches[0][0],'encode'));
				
				if($gethtml != false) {
					$value_add .= PHP_EOL;
					$value_add .= $gethtml;
					} else {
					$value_add .= '<div id="bandcamp-error">'.$bandcamp_error.'</div>';
				}
				
			} elseif (preg_match_all("/https:\/\/[a-z0-9]+.bandcamp.com\/album\/[a-z0-9-_]+/i",$value,$matches)) {
				
				$gethtml = $this->fetchHTML($bandcamp_url . $this->clean($matches[0][0],'encode'));
				
				if($gethtml != false) {
					$value_add .= PHP_EOL;
					$value_add .= $gethtml;
					} else {
					$value_add .= '<div id="bandcamp-error">'.$bandcamp_error.'</div>';
				}
				
			} else {}
			
			if(stristr($value,'http://') || stristr($value,'https://') ) {
				
				if(stristr(strtolower($value),'.gif') || stristr(strtolower($value),'.png') || stristr(strtolower($value),'.jpg') || stristr(strtolower($value),'.jpeg')) {
					// inline image processing
					$regex_image = "/\b(?:(?:https?)):\/\/[.a-zA-Z0-9]+[\/?][=_-~!@#$%^&*()_+a-zA-Z-0-9]+(.png|.jpg|.gif|.jpeg)/i";
					if(preg_match_all($regex_image,$value,$matches)) {
						$value = preg_replace("/".preg_quote($this->clean($matches[0][0],'encode'),'/')."/", '<img src="'.$this->clean($matches[0][0],'encode').'" class="twig-image" "/>', $value, count($matches[0]) );
					}
				}
				
				// youtube matching
				// $regex_yt = "/.*youtube.com\/watch\?v=[a-z0-9]+/i"; // full url
				$regex_yt = "/https?:\/\/www\.youtube\.[a-z]+\/watch\?v=([a-z0-9-\_\-]+)/i";
				if(preg_match_all($regex_yt,$value,$matches)) {
					$value = preg_replace("/".preg_quote($this->clean($matches[0][0],'encode'),'/')."/", '<iframe style="width:100%;height:315px;border-radius:5px;" src="https://www.youtube.com/embed/'.$this->clean($matches[1][0],'encode').'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $value, count($matches[0]));
				}

				$regex_yt = "/https?:\/\/youtu\.be\/([a-z0-9-\_\-]+)/i";
				if(preg_match_all($regex_yt,$value,$matches)) {
					$value = preg_replace("/".preg_quote($this->clean($matches[0][0],'encode'),'/')."/", '<iframe style="width:100%;height:315px;border-radius:5px;" src="https://www.youtube.com/embed/'.$this->clean($matches[1][0],'encode').'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $value, count($matches[0]));
				}
				
				if(strstr($value,'<iframe') || strstr($value,'<img')) {
					
				} else {
				 
					// inline url processing.
					$regex = "/\b(?:(?:https?)):\/\/[.a-zA-Z0-9-]+[\/?]{0,10}[=_-~!@#$%^&*()\/_+a-zA-Z-0-9]+/i";
					if(preg_match_all($regex,$value,$matches) ) {
						
						for($i=0;$i<count($matches[0]); $i++) {
							if(!preg_match_all($regex_image,$value,$matches)) {
								$value = preg_replace("/".preg_quote($this->clean($matches[0][$i],'encode'),'/')."/", '<a href="'.$this->clean($matches[0][$i],'encode').'" target="_blank">'.$this->clean($matches[0][$i],'encode').'</a>', $value, count($matches[0]) );	
								} else {
								// process both image and links here				
								if(!preg_match("/(?:src=\"https?):\/\/[.a-zA-Z0-9]+[\/?][=_-~!@#$%^&*()_+a-zA-Z-0-9]+/i",$value,$matchurl)) {
									 $value = preg_replace("/".preg_quote($this->clean($matches[0][$i],'encode'),'/')."/", '<a href="'.$this->clean($matches[0][$i],'encode').'" target="_blank">'.$this->clean($matches[0][$i],'encode').'</a>', $value, count($matches[0]) );	
								}
							}
						}					
					}
				}
			}
		
		// hashtag integration
		
		$regex = "/\#[a-zA-Z0-9.]+[\s]{0,3}\s/i";
		
		if(preg_match_all($regex,$value,$matches)) {
			$value = preg_replace('/\#[a-zA-Z0-9.]+[\s]{0,3}/i', '<a href="https://www.twigpage.com/'.$this->clean('$0','encode').'">'.$this->clean('$0','encode').'</a>', $value);			
		}


		if(!preg_match_all("/\/\@[a-zA-Z0-9.]+[\s]{0,2}/i",$value)) { 

			$regex = '/\@[a-zA-Z0-9.]+[\s]{0,2}/i';
			if(preg_match_all($regex,$value,$matches)) {
				$cnt = count($matches[0]);
				
				$value = preg_replace('/\@[a-zA-Z0-9.]+[\s]{0,2}/i', '<a class="corn" href="https://www.twigpage.com/'.$this->clean('$0','encode').'">'.$this->clean('$0','encode').'</a>', $value);			

			}
		}
		
		$find 		= ['&amp;#039;','&amp;quot;','&#039;','&quot;','&amp;lt;','&amp;gt;','&lt;','&gt;','&amp;amp;','*','?','(',')','+','&amp;nbsp;','&#38;nbsp;'];
		$replace 	= ['&#8217;','&#34;','&#8217;','&#34;','&#60;','&#62;','&#60;','&#62;','&#38;','&#42;','&#63;','&#40;','&#41;','&#43;',' ',' '];
		
		$value = str_replace($find,$replace,$value);
		$value = str_replace('&#38;#039;','&#8217;',$value);


		$findHTML = ['&#60;br&#62;','&#60;br/&#62;','&#60;b&#62;','&#60;/b&#62;','&#60;em&#62;','&#60;/em&#62;','&#60;code&#62;','&#60;/code&#62;','&#60;blockquote&#62;','&#60;/blockquote&#62;'];
		$replaceHTML = ['<br>','<br />','<b>','</b>','<em>','</em>','<code>','</code>','<blockquote>','</blockquote>'];
		$value = str_replace($findHTML,$replaceHTML,$value);

		$value .= $value_add;
			
		return utf8_decode($value);
		}
	}	

	public function clean($string,$method='') {
		
		$dataresult = '';
		
		switch($method) {
			case 'alpha':
				$dataresult =  preg_replace('/[^a-zA-Z]/','', $string);
			break;
			case 'num':
				$dataresult =  preg_replace('/[^0-9]/','', $string);
			break;
			case 'unicode':
				$dataresult =  preg_replace("/[^[:alnum:][:space:]]/u", '', $string);
			break;
			case 'user':
				$dataresult =  preg_replace("/[^[:alnum:]]/u", '', $string);
			break;
			case 'encode':
			if(is_null($string)) { 
				$dataresult =  htmlspecialchars($string,ENT_QUOTES,'UTF-8');
				} else {
				$dataresult =  htmlspecialchars($string,ENT_QUOTES,'UTF-8');
			}
			break;
			case 'query':
				$search  = ['`','"','\'',';'];
				$replace = ['','','',''];
				$dataresult = str_replace($search,$replace,$string);
			break;
			case 'cols':
				// comma is allowed for selecting multiple columns.
				$search  = ['`','"','\'',';'];
				$replace = ['','','',''];
				$dataresult = str_replace($search,$replace,$string);
			break;
			case 'dir':
				$search  = ['`','"',',','\'',';','..','../','.php','.css'];
				$replace = ['','','','','','','','',''];
				$dataresult = str_replace($search,$replace,$string);
			break;
			case 'table':
				$search  = ['`','"',',','\'',';','.','$','%'];
				$replace = ['','','','','','','',''];
				$dataresult = str_replace($search,$replace,$string);
			break;
			case 'search':
			$search  = ['`','"',',','\'',';','.','$','%'];
			$replace = ['','','','','','','',''];
			$string = str_replace($search,$replace,$string);
			$dataresult =  preg_replace("/[^[:alnum:][:space:]]/u", '', $string);
			break;
			default:
			return $dataresult;
			}
		return $dataresult;
	}
	
	/**
	* name
	* @param
	* @return
	*/
	public function css($body,$text,$background=false) 
	{
		$string = "";
		
			$bodycolor = substr($body,0,7);
			$textcolor = substr($text,0,7);
			
			if($bodycolor == $textcolor) {
				$textcolor = '#fff';
			}				
			
			$dec = hexdec(str_replace('#','',$bodycolor));
			
			if($dec > 0) {
				$div  = (int)($dec / 2);
				$dec  = (int)($dec + $div);
				$linecolor = '#';
				$linecolor .= dechex($dec);
			} else {
				$linecolor = $bodycolor;
			}
			
			$string .= PHP_EOL;
			$string .="<style>";
			if($background != false) {
				$string .="body { background: url('".$this->clean($background,'encode')."')!important; background-repeat: no-repeat!important; background-position: right!important; background-size: cover!important; background-color:".$this->clean($bodycolor,'encode')."; color:".$this->clean($textcolor,'encode')."; } " . PHP_EOL;
				} else {
				$string .="body { background-color:".$this->clean($bodycolor,'encode')."; color:".$this->clean($textcolor,'encode')."; } " . PHP_EOL;
			}
			$string .="* { color:".$this->clean($textcolor,'encode')."; } " . PHP_EOL;
			$string .="a:link, a:visited { color:".$this->clean($textcolor,'encode')."; } " . PHP_EOL;
			if($bodycolor != '#ffffff') {
				$string .=".timeline-post { background-color:".$this->clean($bodycolor,'encode')."; border: 0px solid ".$this->clean($bodycolor,'encode')."; box-shadow: 0px 0px 0px 0px ".$this->clean($textcolor,'encode')."10;  }" . PHP_EOL;
				$string .=" #nav-search { box-shadow: 0px 0px 0px 0px #000!important; } " . PHP_EOL;
				$string .=" textarea, input, #selector { box-shadow: 0px 0px 0px 0px #000!important; } " . PHP_EOL;
			}
			$string .="#timeline-profile-picture { border: 2px solid ".$this->clean($bodycolor,'encode')."; }" . PHP_EOL;
			$string .=".timeline-photo { border: 5px solid ".$this->clean($bodycolor,'encode')."; }" . PHP_EOL;
			$string .=".timeline-post:hover { background-color:".$this->clean($bodycolor,'encode')."80; }" . PHP_EOL;
			$string .=" input, textarea, #timelinepost-textarea, .timelinepost-textarea { background-color:".$this->clean($bodycolor,'encode')."40!important; color:".$textcolor."!important; border: 1px solid ".$linecolor."!important; }" . PHP_EOL;
			$string .="#nav-left, #nav-left li a:link, #nav-left li a:visited { color: ".$this->clean($textcolor,'encode')."; }  " . PHP_EOL;
			$string .="#nav-left, #nav-left li a:hover {color: ".$this->clean($textcolor,'encode')."40; } " . PHP_EOL;
			$string .="#post-timeline-link {border: 1px solid #666; color: #000;}" . PHP_EOL;
			$string .="#float { background-color: ".$this->clean($bodycolor,'encode')."; border: 1px solid ".$this->clean($textcolor,'encode')."40; } " . PHP_EOL;
			$string .= ".tl-opt-num { color: ".$this->clean($textcolor,'encode')."!important; } ". PHP_EOL;
			$string .= "#selector { background-color: ".$this->clean($bodycolor,'encode')."!important; } ". PHP_EOL;
			$string .= ".settings-form input { background-color: ".$this->clean($bodycolor,'encode')."!important; } ". PHP_EOL;

		
		
			$string .="</style>";
			$string .= PHP_EOL;
			
			
				
				
		return $string;
	}	
}

?>