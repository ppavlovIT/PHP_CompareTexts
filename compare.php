<?php
	header('Content-Type: text/html; charset=utf-8');
	// break word on matrix pairs
	function breakToPairs ($Word) {
        $ReturnArray = array();
        // return word in matrix if length is 1  
        if(strlen($Word)==1)
            return array($Word);
        for($j=0;$j< strlen($Word)-1;$j++){
            // go thru word and save group of 2 letters  
            $ReturnArray[]= substr($Word,$j,2);
        }
        return $ReturnArray;
    }

	function comparePairs ($First, $Second) {
        // create pairs matrix for every word  
        $FirstArray= breakToPairs(strtolower($First));
        $SecondArray= breakToPairs(strtolower($Second));
        // check first pair
        if($FirstArray[0]!=$SecondArray[0])
            return 0;
        // total pairs number in both words 
        $Count12= count($FirstArray)+ count($SecondArray);
        $IntArr=array();
        // check every pair of both matrix  
        foreach ($FirstArray as $Key1=>$Item1){
            foreach ($SecondArray as $Key2=>$Item2){
                // if two pairs are equal 
                if($Item1==$Item2){
                    // add pair in matrix of similarity  
                    $IntArr[]=$Item1;
                    // delete both pairs from matrix  
                    // (to avoid repeating)
                    unset($SecondArray[$Key2]);
                    unset($FirstArray[$Key1]);
                    break;
                }
            }
        }
        // similarity number
        return 2* count($IntArr)/$Count12;
    }

	// intersect function
	function smartArrayIntersect($Array1,$Array2,$MinGrade=0.5,$CompareFun="comparePairs") {
        $RetArray=array();
        // go thru first matrix
        foreach($Array1 as $Key1=>$Item1){
            // go thru second matrix
            foreach($Array2 as $Key2=>$Item2){
                // for every words pair call function comparePairs  
                // if similarity number is enough go in  
                if($CompareFun($Item1,$Item2)>=$MinGrade){
                    // add words in intersect matrix 
                    $RetArray[]=$Item1;
                }
            }
        }
        return ($RetArray);
    }
	
	// Function that takes 2 texts, compare them and return 0 or 1 (0 for not similar and 1 for similar)
	function compareFinal($Txt1, $Txt2) {
		
		// Define array with useless strings		
		$ReplaceStrings=array("!",",","\"","'",".","?","-",";",":","`","\\n","\\t", "+", "*");
		$del_val = array("u","i","je","na","se","su","što","zbog","do","te","samo","jer","već","za","da","s","od","a","će",
						"iz","koji","ne","kako","o","nije","bi","to","ali","još","sa","kao","koja","sve","biti","po","koje","ga","bio","sam",
						"bez","no","dok","mu","pa","li","oko");
	
		// Remove useless string so we can compare text with only important words
		$Txt1 = mb_strtolower($Txt1, mb_detect_encoding($Txt1));
		$output1  = str_replace($ReplaceStrings, " ", $Txt1);
		$part1 = explode(" ", $output1);
		$part1 = array_unique($part1);
		$arr1 = array_filter($part1, create_function('$a','return preg_match("#\S#", $a);'));
		
		foreach($del_val as $nesto){
			if(($key = array_search($nesto, $arr1)) !== false) {
			unset($arr1[$key]);
			}
		 }
		
		$Txt2 = mb_strtolower($Txt2, mb_detect_encoding($Txt2));
		$output2  = str_replace($ReplaceStrings, " ", $Txt2);
		$part2 = explode(" ", $output2);
		$part2 = array_unique($part2);
		$arr2 = array_filter($part2, create_function('$a','return preg_match("#\S#", $a);'));

		foreach($del_val as $nesto){
			if(($key = array_search($nesto, $arr2)) !== false) {
				unset($arr2[$key]);
			}
		}

		// Check similarity and number of similar words
		$br1=count($arr1);
		$br2=count($arr2);
		$br3=count(smartArrayIntersect($arr1,$arr2));
		$broj= 2*$br3/($br1+$br2);
		
		// Return 1 for similar texts or 0 for different texts
		if($broj>0.4){
			return 1;
		} else {
			return 0;
		}
	}
	
?>