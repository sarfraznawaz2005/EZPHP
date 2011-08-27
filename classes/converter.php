<?php

	/*
		-----------------------------------------------------------
		Purpose: Misc conversion functions
		-----------------------------------------------------------
	
		-----------------------------------------------------------
		Author/Source: Provided within functions
		-----------------------------------------------------------
		
		-----------------------------------------------------------
		Example Use: Provided within functions
		-----------------------------------------------------------
	*/

class converter
{
	/*
	
    // This code is copyrighted and has limited warranties.
    // http://www.Planet-Source-Code.com/vb/scripts/ShowCode.asp?txtCodeId=1902&lngWId=8
	// supports 'only' up to 4999
	*/
    
    function dec2roman($valor,$toupper=true){
	$r1 = null;
	$r2 = null;
	$r3 = null;
	$r4 = null;
	
    //returns a string as a roman numeral
    if (($valor >= 5000) || ($valor < 1)) return "?"; //supports 'only' up to 4999
    $aux = (int)($valor/1000);
    if ($aux!==0)
    {
    $valor %= 1000;
    while($aux!==0)
    {
    	$r1 .= "M";
    	$aux--;
    }
    }
    $aux = (int)($valor/100);
    if ($aux!==0)
    {
    $valor %= 100;
    switch($aux){
    	case 3: $r2="C";
    	case 2: $r2.="C";
    	case 1: $r2.="C"; break;
    	 case 9: $r2="CM"; break;
    	 case 8: $r2="C";
    	 case 7: $r2.="C";
    	case 6: $r2.="C";
    case 5: $r2="D".$r2; break;
    case 4: $r2="CD"; break;
    default: break;
    	 }
    }
    $aux = (int)($valor/10);
    if ($aux!==0)
    {
    $valor %= 10;
    switch($aux){
    	case 3: $r3="X";
    	case 2: $r3.="X";
    	case 1: $r3.="X"; break;
    	case 9: $r3="XC"; break;
    	case 8: $r3="X";
    	case 7: $r3.="X";
    	 case 6: $r3.="X";
    case 5: $r3="L".$r3; break;
    case 4: $r3="XL"; break;
    default: break;
    }
    }
    switch($valor){
    	case 3: $r4="I";
    	case 2: $r4.="I";
    	case 1: $r4.="I"; break;
    	case 9: $r4="IX"; break;
    	case 8: $r4="I";
    case 7: $r4.="I";
    case 6: $r4.="I";
    case 5: $r4="V".$r4; break;
    case 4: $r4="IV"; break;
    default: break;
    }
    $roman = $r1.$r2.$r3.$r4;
    if (!$toupper) $roman = strtolower($roman);
    return $roman;
    }	


}

?>
