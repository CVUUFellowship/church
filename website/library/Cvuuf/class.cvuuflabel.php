<?php

function db_neighborhood_dot($hood) {
//echo "HOOD: $hood <br>";
	global	$db;
	global	$Dot_table;
	$dQuery="SELECT RecordID, Dot FROM hoods";
//echo "$dQuery <br>";
	$dots=$db->get_results("$dQuery");
	unset($Dot_Table);
	$nDot=0;
	foreach($dots as $dot){
    $Dot_Table[$dot->RecordID]=$dot->Dot.'.png';  
  }
	return $Dot_Table[$hood];
}

include_once('class.ezpdf.php');

class Clabel extends Cezpdf {
//==============================================================================
// This class extends the ezpdf class to specifically create labels
// this specific implementation will be used to create labels for 
// the Cougar implementation for MSBOA
// 
// version 002
//
// Craig Heydenburg Jan 06, 02
// modified April 18, 2002 to make it more modular
//==============================================================================

// declaring some variables - no need to change anything here
var $size; //string, stores page size (e.g. 'letter')
var $orientation; //string, store page orientation (e.g. 'portrait')
var $margin_l; var $margin_r; var $margin_t; var $margin_b; // int, used to store margin vals
var $label=array('width'=>0, 'height'=>0, 'per_row'=>0, 'per_page'=>0, 'type'=>"Av5160", 'textsize'=>10); //array used to store label size and info

// declaring variables with values - change stuff as neccessary
var $ourname="MSBOA";
var $image=array('width'=>72, 'height'=>0, 'loc'=>"./images/msboa_logo.jpg"); //image used on nametag
var $font_dir="./fonts/"; // location of fonts - be sure to include the slash at the end
			
## need to recreate the constructor of the Cezpdf because constructors are not inherited

function Clabel($type="Av5160"){
	// the type variable defaults to Avery 5160

	$this->setLabelType($type);
	$this->Cezpdf($this->size,$this->orientation);
  $this->ezSetMargins($this->margin_t,$this->margin_b,$this->margin_l,$this->margin_r); // T,B,L,R

} //end constructor

function setLabelType($type) {
	switch ($type) {
		case "regular": //regular name tag
		case "newfriend":
		case "visitor":
    	// these labels are actually signs/posters
			// page size: letter and portrait orientation
			// create a new page referring to the parent class
			$this->size="letter";
			$this->orientation="portrait";
			// set default for the margins
			// top and left to writing point
			// bottom and right margins are adjusted to allow the write point to move freely (touchy)
			$this->margin_t=$this->u2px(4/10);
			$this->margin_b=$this->u2px(4/10);
			$this->margin_l=$this->u2px(4/10);
			$this->margin_r=$this->u2px(4/10);
			// set the labels width & height
			$this->label['width']=$this->u2px(3.725);
			$this->label['height']=$this->u2px(2.625);
			// set number of lables per row
			$this->label['per_row']=2;
			// set number of lables per page
			$this->label['per_page']=6;
			// set size of text for label
			$this->label['textsize']=18;
			break;
	} // end switch
	// set type variable
	$this->label['type']=$type;
} // end  setLabelType function

function formatLabel($type, $names, $labelX, $labelY, $FillBlanks) {
	// $type is the label type, $add_arr is an array containing the label text ('line1', 'line2')
	switch ($type) {
		case "regular":

		/*************************************************************
		* array ('FirstName'=> first name
		*        'LastName'=> last name
		*       )
		**************************************************************/		

			if ($names->FirstName == '' && $FillBlanks=='no')
				break;

//echo "FirstName:", $names->FirstName, "<br>";
			// need to set positions
			$x=$labelX+$this->margin_l;
			$y=$labelY-$this->margin_t;
//echo $x,' ',$y,'<br>';
			$line=array('x1'=>$x, 'y1'=>$y-$this->label['height'],
				'x2'=>$x+$this->label['width'],
				'y2'=>$y);

			// print border
			$this->setLineStyle(1);
			$this->setStrokeColor(0,0,0,1);
//echo $line["x1"],' ',$line["y1"],' ',$line["x2"],' ',$line["y2"],'<BR>';
			$this->rectangle($line["x1"],$line["y1"],$this->label['width'],
				$this->label['height']);

      // print the dot
      $dot=db_neighborhood_dot($names->HoodID);
//echo $dot, ' ', 'images/'.$dot, '<br>'; break;      
      $this->addPngFromFile('images/'.$dot,
        $this->margin_l+$labelX+200,$labelY-100,60);
			$this->ezSetY($labelY);

      // print the chalice
      $this->addPngFromFile('images/chalice.png',
        $this->margin_l+$labelX+35,$labelY-210,200);
//      $this->addPngFromFile('images/chaliceLogo.png',
//        $this->margin_l+$labelX+10,$labelY-100,70);

      
			//move writing point back to the top
			$this->ezSetY($labelY);

			// determine font size and print first name
			$this->selectFont($this->font_dir.'verdanab.afm');
			$thesize=36;
			$FirstName=strtoupper($names->FirstName);
			$howwide=$this->getTextWidth(36, $FirstName);
//echo $this->label['width'],' ',$howwide, '<br>';
			if ($howwide>$this->label['width']-9)
				$thesize=7020/$howwide;
			$this->addText($this->centerText($FirstName,
			  $this->margin_l+$labelX+$this->label['width']/2,$thesize),
			  $labelY-100,$thesize,$FirstName);
			  
			// determine font size and print last name
			$this->selectFont($this->font_dir.'verdana.afm');
			$thesize=20;
			$LastName=strtoupper($names->LastName);
			$howwide=$this->getTextWidth(20, $LastName);
//echo $this->label['width'],' ',$howwide, '<br>';
			if ($howwide>$this->label['width']-9)
				$thesize=7056/$howwide;
			$this->addText($this->centerText($LastName,
				$this->margin_l+$labelX+$this->label['width']/2,$thesize),
				$labelY-125,$thesize,$LastName);
			
      if (isset($names->Title)) if ($theTitle=$names->Title) {
  			$this->selectFont($this->font_dir.'verdanab.afm');
	   		$thesize=16;
			 $howwide=$this->getTextWidth(16, $theTitle);
//echo $this->label['width'],' ',$howwide, '<br>';
			 if ($howwide>$this->label['width']-9)
			 	$thesize=4160/$howwide;
//echo $thesize, '<br>';
			$this->addText($this->centerText($theTitle,
				$this->margin_l+$labelX+$this->label['width']/2,$thesize),
				$labelY-210,$thesize,$theTitle);
      }
			//move writing point back to the top
			$this->ezSetY($labelY);

			break;

		case "newfriend":

		/*************************************************************
		* array ('FirstName'=> first name
		*        'LastName'=> last name
		*       )
		**************************************************************/		

			if ($names->FirstName == '' && $FillBlanks=='no')
				break;

//echo "FirstName:", $names->FirstName, "<br>";
			// need to set positions
			$x=$labelX+$this->margin_l;
			$y=$labelY-$this->margin_t;
//echo $x,' ',$y,'<br>';
			$line=array('x1'=>$x, 'y1'=>$y-$this->label['height'],
				'x2'=>$x+$this->label['width'],
				'y2'=>$y);

			// print border
			$this->setLineStyle(1);
			$this->setStrokeColor(0,0,0,1);
//echo $line["x1"],' ',$line["y1"],' ',$line["x2"],' ',$line["y2"],'<BR>';
			$this->rectangle($line["x1"],$line["y1"],$this->label['width'],
				$this->label['height']);

      // print the dot
      $dot=db_neighborhood_dot($names->HoodID);
//echo $dot, ' ', 'images/'.$dot, '<br>'; break;      
      $this->addPngFromFile('images/'.$dot,
        $this->margin_l+$labelX+200,$labelY-100,60);
			$this->ezSetY($labelY);

      // print the chalice
      $this->addPngFromFile('images/graychalice.png',
        $this->margin_l+$labelX+35,$labelY-210,200);
//      $this->addPngFromFile('images/chaliceLogo.png',
//        $this->margin_l+$labelX+10,$labelY-100,70);

      
			//move writing point back to the top
			$this->ezSetY($labelY);

			// determine font size and print first name
			$this->setColor(0,.4,0,1);
			$this->selectFont($this->font_dir.'verdanab.afm');
			$thesize=36;
			$FirstName=strtoupper($names->FirstName);
			$howwide=$this->getTextWidth(36, $FirstName);
//echo $this->label['width'],' ',$howwide, '<br>';
			if ($howwide>$this->label['width']-9)
				$thesize=7020/$howwide;
			$this->addText($this->centerText($FirstName,
			  $this->margin_l+$labelX+$this->label['width']/2,$thesize),
			  $labelY-100,$thesize,$FirstName);
			  
			// determine font size and print last name
			$this->selectFont($this->font_dir.'verdana.afm');
			$thesize=20;
			$LastName=strtoupper($names->LastName);
			$howwide=$this->getTextWidth(20, $LastName);
//echo $this->label['width'],' ',$howwide, '<br>';
			if ($howwide>$this->label['width']-9)
				$thesize=7056/$howwide;
			$this->addText($this->centerText($LastName,
				$this->margin_l+$labelX+$this->label['width']/2,$thesize),
				$labelY-125,$thesize,$LastName);
			
			//move writing point back to the top
			$this->ezSetY($labelY);

			break;

		case "spouse":

		/*************************************************************
		* array ('FirstName'=> first name
		*        'LastName'=> last name
		*       )
		**************************************************************/		

			if ($names->FirstName == '')
				break;

//echo "FirstName:", $names->FirstName, "<br>";
			// need to set positions
			$x=$labelX+$this->margin_l;
			$y=$labelY-$this->margin_t;
//echo $x,' ',$y,'<br>';
			$line=array('x1'=>$x, 'y1'=>$y-$this->label['height'],
				'x2'=>$x+$this->label['width'],
				'y2'=>$y);

			// print border
			$this->setLineStyle(2);
			$this->setStrokeColor(0,0,0,1);
//echo $line["x1"],' ',$line["y1"],' ',$line["x2"],' ',$line["y2"],'<BR>';
			$this->rectangle($line["x1"],$line["y1"],$this->label['width']-2,
				$this->label['height']-2);

			// determine font size and print first name
			$this->setColor(0,.6,0,1);
			$this->selectFont($this->font_dir.'verdanab.afm');
			$thesize=36;
			$howwide=$this->getTextWidth(36, $names->FirstName);
//echo $this->label['width'],' ',$howwide, '<br>';
			if ($howwide>$this->label['width']-9)
				$thesize=7056/$howwide;
			$this->addText($this->centerText($names->FirstName,
			  $this->margin_l+$labelX+$this->label['width']/2,$thesize),
			  $labelY-90,$thesize,$names->FirstName);
			  
			// determine font size and print last name
			$thesize=36;
			$howwide=$this->getTextWidth(36, $names->LastName);
//echo $this->label['width'],' ',$howwide, '<br>';
			if ($howwide>$this->label['width']-9)
				$thesize=7056/$howwide;
			$this->addText($this->centerText($names->LastName,
				$this->margin_l+$labelX+$this->label['width']/2,$thesize),
				$labelY-130,$thesize,$names->LastName);
			
			//move writing point back to the top
			$this->ezSetY($labelY);

			break;

				
		case "visitor":
		/*************************************************************
		* The sign is expecting an array of three values
		* array ('line1'=> top line of sign
		*        'line2'=> main line of sign
		*        'line3'=> bottom line of sign
		*       )
		* any of the three lines may be left null if desired
		**************************************************************/		

			if ($names->FirstName == '' && $FillBlanks=='no')
				break;

//echo "FirstName:", $names->FirstName, "<br>";
			// need to set positions
			$x=$labelX+$this->margin_l;
			$y=$labelY-$this->margin_t;
//echo $x,' ',$y,'<br>';
			$line=array('x1'=>$x, 'y1'=>$y-$this->label['height'],
				'x2'=>$x+$this->label['width'],
				'y2'=>$y);

			// print border
			$this->setLineStyle(1);
			$this->setStrokeColor(0,0,0,1);
//echo $line["x1"],' ',$line["y1"],' ',$line["x2"],' ',$line["y2"],'<BR>';
			$this->rectangle($line["x1"],$line["y1"],$this->label['width'],
				$this->label['height']);


      // print the chalice
      $this->addPngFromFile('images/darkgraychalice.png',
        $this->margin_l+$labelX+35,$labelY-195,200);
//      $this->addPngFromFile('images/chaliceLogo.png',
//        $this->margin_l+$labelX+10,$labelY-100,70);

      
			//move writing point back to the top
			$this->ezSetY($labelY);

			// determine font size and print first name
			$this->selectFont($this->font_dir.'verdanab.afm');
			$thesize=36;
			$FirstName=strtoupper($names->FirstName);
			$howwide=$this->getTextWidth(36, $FirstName);
//echo $this->label['width'],' ',$howwide, '<br>';
			if ($howwide>$this->label['width']-9)
				$thesize=7020/$howwide;
			$this->addText($this->centerText($FirstName,
			  $this->margin_l+$labelX+$this->label['width']/2,$thesize),
			  $labelY-95,$thesize,$FirstName);
			  
			// determine font size and print last name
			$this->selectFont($this->font_dir.'verdana.afm');
			$thesize=20;
			$LastName=strtoupper($names->LastName);
			$howwide=$this->getTextWidth(20, $LastName);
//echo $this->label['width'],' ',$howwide, '<br>';
			if ($howwide>$this->label['width']-9)
				$thesize=7056/$howwide;
			$this->addText($this->centerText($LastName,
				$this->margin_l+$labelX+$this->label['width']/2,$thesize),
				$labelY-115,$thesize,$LastName);
			
			//move writing point back to the top
			$this->ezSetY($labelY);
			$this->selectFont($this->font_dir.'verdanab.afm');
			$thesize=16;
			$this->addText($this->centerText("Visitor",
				$this->margin_l+$labelX+$this->label['width']/2,$thesize),
				$labelY-210,$thesize,"Visitor");

			break;

		// other label types would be listed as new cases here
	} // end switch
} // end  formatLabelText function

function makeLabel ($labelInfo, $FillBlanks) {
	// $labelInfo=array(key, array('line1', 'line2', 'line3', 'line4', etc.))
	$count_labels=0; //used to count the labels as they are printed
  
	$offset=0; //used to move write point to next label
	$counter=0; //used to count number of labels in this row

	$this->selectFont($this->font_dir.'Helvetica');
	//move writing pointer down one line to compensate for quirk in the parent class
	$this->ez['topMargin']=(($this->ez['topMargin'])+($this->getFontHeight($this->label['textsize'])));
var_dump($labelInfo); exit;	
	while (list($key, $add_arr)=each($labelInfo)) {
//echo "counter: $counter Per page: ",$this->label['per_page']," This count: ",$count_labels%$this->label['per_page']," Count_labels: $count_labels <br>";
    if ($counter==0 and ($this->label['per_page']<>0) and ($count_labels%$this->label['per_page']==0 and $count_labels>0)) {
    	// if the label[per_page] setting exists and if this equals that setting then make a new page
    	$this->ezNewPage();
//echo "NEW PAGE<br>";
    }
    $counter++; // count the labels in this row
 		$count_labels++; // count the total number of labels
//echo "count_labels: $count_labels counter: $counter Per page: ",$this->label['per_page']," This count: ",$count_labels%$this->label['per_page'],"<br>";
		$returnheight=$this->formatLabel($this->label['type'],$add_arr, $offset, $this->y, $FillBlanks);
		if ($counter<$this->label['per_row']) {
      $this->ezSetDY($returnheight); //return to current height for next label in row
      $offset+=$this->label['width']; //set offset to next label in row
    } else {
      $this->ezSetDY($returnheight); //return to current height for next label in row
      $this->ezSetDy(-($this->label['height'])); //move the write point down one label
        $offset=0; // set the write point back to the left margin
        $counter=0; //reset the counter
    	}
	}
	// send the page to the browser
	$this->ezStream(array('Content-Disposition'=>'CVUUF_labels.pdf'));
} // end makeLabel function

function centerText ($text, $center, $size) {
	// function returns the starting x value in order to center text on
	// the provided center point
	// requires: $text - the text being centered
	//           $center - X value upon which the text is to be centered
	//           $size - the size of the text
	return($center-($this->getTextWidth($size,$text)/2));
} // end centerText

function u2px ($value=0,$unit="in") {
	// function returns converted value from any unit to pdf screen units
	// requires: $value - the value to be converted
	//           $unit - the unit that the $value is measured in
	$fuzz=0.00000001; // facilitates rounding
	switch ($unit) {
		case "in": // english inches
			$newvalue=round($value*72+$fuzz);
			return($newvalue);
			break;
		case "cm": // metric centimeters
			$newvalue=round($value*28.3465+$fuzz);
			return($newvalue);
			break;
		case "mm": // metric millimeters
			$newvalue=round($value*2.83465+$fuzz);
			return($newvalue);
			break;
	} // end switch
} //end u2px


} //end class

?>
