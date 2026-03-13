$(document).ready(function(){
    function keyPress(){
        
        //$('#abc').on('keypress', function(event){
            
            findSurroundingChars();
            key = $('#key').val();        
            var c = event.which;//character code        
            var keyPress = String.fromCharCode(c);//convert it to a string
            
            if(keyPress == '^' || keyPress == '_' || keyPress == '='){
                //Double Accidental with octave modifier
                if(nextChar == keyPress && threeCharsAhead == ',' || threeCharsAhead == '\''){
                    $(this).play(keyPress + nextChar + charAfterNext + threeCharsAhead);
                //Double Accidental without octave modifier
                }else if(nextChar == keyPress){
                    $(this).play(keyPress + nextChar + charAfterNext);
                //An accidental sandwiched between and accidental and a note with octave modifier
                }else if (lastChar == '^' || lastChar == '_' && nextChar.match(letters) && charAfterNext  == ',' || charAfterNext == '\''){
                    $(this).play(lastChar + keyPress + nextChar + charAfterNext)
                //accidental was sandwiched between accidental and a note
                }else if(lastChar == '^' || lastChar == '_' && nextChar.match(letters)){
                    $(this).play(lastChar + keyPress + nextChar)
                //Accidental added before a letter with octave modifier
                }else if(nextChar.match(letters) && charAfterNext == ',' || charAfterNext =='\''){
                    $(this).play(keyPress + nextChar + charAfterNext);
                // "" ""  without octave modifier
                }else if(nextChar.match(letters)){
                    $(this).play(keyPress + nextChar);
                }
            }else
            if(lastChar == '^' || lastChar == '_'){
                if(charBeforeLast == lastChar){                
                    $(this).play(charBeforeLast + lastChar + keyPress);//double accidental                
                }else{                
                    $(this).play(lastChar + keyPress);//accidental note 
                }
            }else        
            if(keyPress == ',' || keyPress =='\''){  
                sharps = [];
                sharps.push("F", "f");
                flats = [];
                flats.push("B", "b");
                if(charBeforeLast == '^' || charBeforeLast == '_' || charBeforeLast == '='){//if the user modified the note
                    
                    if(threeCharsAgo == charBeforeLast){//just how modified is this note anyway?
                        if(nextChar == ',' || nextChar == '\''){
                            $(this).play(threeCharsAgo + charBeforeLast + lastChar + keyPress + nextChar);  
                        } else {
                            $(this).play(threeCharsAgo + charBeforeLast + lastChar + keyPress);                    
                        }                    
                    }else{//ok it's only a single accidental
                        $(this).play(charBeforeLast + lastChar + keyPress);
                    } 
                    
                }else if(lastChar == ',' || lastChar == '\'') { 
                    
                    if(sixCharsAgo == fiveCharsAgo) {
                        if(sixCharsAgo == '^' || sixCharsAgo == '_' || sixCharsAgo == '='){
                            $(this).play(sixCharsAgo + fiveCharsAgo + fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);
                        }
                    }
                    if(fiveCharsAgo == fourCharsAgo){
                        if(fiveCharsAgo == '^' || fiveCharsAgo == '_' || fiveCharsAgo == '='){
                            $(this).play(fiveCharsAgo + fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);
                        }               
                    }else 
                    if(fourCharsAgo == threeCharsAgo){
                        if(fourCharsAgo == '^' || fourCharsAgo == '_' || fourCharsAgo == '=') {
                            $(this).play(fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);    
                        }            
                    }
                    
                }else { 
                    
                    if(key == "C" || key == "D dorian" || key == "G Mixolydian" || key == "A minor"){    
                        $(this).play(lastChar + keyPress); 
                    }else
                    //Sharp keys
                    if(key == "G" || key == "A dorian" || key == "D Mixolydian" || key == "E minor"){
                        $(this).play(accidentalNotes(sharps, '^', keyPress, true));
                    }else{
                        sharps.push("C", "c"); 
                    } 
                    if(key == "D" || key == "E dorian" || key == "A Mixolydian" || key == "B minor"){                               
                    }else{
                        sharps.push("G", "g");    
                    } 
                    if(key == "A" || key == "B dorian" || key == "E Mixolydian" || key == "F# minor"){                            
                        $(this).play(accidentalNotes(sharps, '^', keyPress, true));                
                    }else{
                        sharps.push("D", "d"); 
                    } 
                    if(key == "E" || key == "F# dorian" || key == "B Mixolydian" || key == "C# minor"){                               
                        $(this).play(accidentalNotes(sharps, '^', keyPress, true));                
                    }else{
                        sharps.push("A", "a");  
                    } 
                    if(key == "B" || key == "C# dorian" || key == "F# Mixolydian" || key == "G# minor"){                                
                        $(this).play(accidentalNotes(sharps, '^', keyPress, true));                
                    }else{
                        sharps.push("E", "e");
                    }
                    if(key == "F#" || key == "G# dorian" || key == "C# Mixolydian" || key == "D# minor"){                                
                        $(this).play(accidentalNotes(sharps, '^', keyPress, true));                
                    }else{
                        sharps.push("B", "b"); 
                    } 
                    if(key == "C#" || key == "D# dorian" || key == "G# Mixolydian" || key == "A# minor"){                               
                        $(this).play(accidentalNotes(sharps, '^', keyPress, true));
                    //Flat keys                
                    }else if(key == "F" || key == "G dorian" || key == "C Mixolydian" || key == "D minor"){
                        $(this).play(accidentalNotes(flats, '_', keyPress, true));
                    }else{
                        flats.push("E", "e");
                    }
                    if(key == "Bb" || key == "C dorian" || key == "F Mixolydian" || key == "G minor"){
                        $(this).play(accidentalNotes(flats, '_', keyPress, true));
                    }else{
                        flats.push("A", "a");
                    }
                    if(key == "Eb" || key == "F dorian" || key == "Bb Mixolydian" || key == "C minor"){
                        $(this).play(accidentalNotes(flats, '_', keyPress, true));
                    }else{
                        flats.push("D", "d");
                    }
                    if(key == "Ab" || key == "Bb dorian" || key == "Eb Mixolydian" || key == "F minor"){
                        $(this).play(accidentalNotes(flats, '_', keyPress, true));
                    }else{
                        flats.push("G", "g");
                    }
                    if(key == "Db" || key == "Eb dorian" || key == "Ab Mixolydian" || key == "Bb minor"){
                        $(this).play(accidentalNotes(flats, '_', keyPress, true));
                    }else{
                        flats.push("C", "c");
                    }
                    if(key == "Gb" || key == "Ab dorian" || key == "Db Mixolydian" || key == "Eb minor"){
                        $(this).play(accidentalNotes(flats, '_', keyPress, true));
                    }else{
                        flats.push("F", "f");
                    }
                    if(key == "Cb" || key == "Db dorian" || key == "Gb Mixolydian" || key == "Ab minor"){
                        $(this).play(accidentalNotes(flats, '_', keyPress, true));
                    }
                }
            }else{//if keypress was not a modifier AND last key press also was not a modifier
                //then just play the keyed note value, modified by key, of course
                sharps = [];
                sharps.push("F", "f");
                
                flats = [];
                flats.push("B", "b");
                if(key == "C" || key == "D dorian" || key == "G Mixolydian" || key == "A minor"){                
                    $(this).play(keyPress);//just send the current key press.
                }else
                if(key == "G" || key == "A dorian" || key == "D Mixolydian" || key == "E minor"){
                    $(this).play(accidentalNotes(sharps, '^', keyPress));
                }else{
                    sharps.push("C", "c"); 
                } 
                if(key == "D" || key == "E dorian" || key == "A Mixolydian" || key == "B minor"){                               
                    $(this).play(accidentalNotes(sharps, '^', keyPress));
                }else{
                    sharps.push("G", "g");    
                } 
                if(key == "A" || key == "B dorian" || key == "E Mixolydian" || key == "F# minor"){                            
                    $(this).play(accidentalNotes(sharps, '^', keyPress));                
                }else{
                    sharps.push("D", "d"); 
                } 
                if(key == "E" || key == "F# dorian" || key == "B Mixolydian" || key == "C# minor"){                               
                    $(this).play(accidentalNotes(sharps, '^', keyPress));                
                }else{
                    sharps.push("A", "a");  
                } 
                if(key == "B" || key == "C# dorian" || key == "F# Mixolydian" || key == "G# minor"){                                
                    $(this).play(accidentalNotes(sharps, '^', keyPress));                
                }else{
                    sharps.push("E", "e");
                }
                if(key == "F#" || key == "G# dorian" || key == "C# Mixolydian" || key == "D# minor"){                                
                    $(this).play(accidentalNotes(sharps, '^', keyPress));                
                }else{
                    sharps.push("B", "b"); 
                } 
                if(key == "C#" || key == "D# dorian" || key == "G# Mixolydian" || key == "A# minor"){                               
                    $(this).play(accidentalNotes(sharps, '^', keyPress));
                //Flat keys                
                }else if(key == "F" || key == "G dorian" || key == "C Mixolydian" || key == "D minor"){
                    $(this).play(accidentalNotes(flats, '_', keyPress));
                }else{
                    flats.push("E", "e");
                }
                if(key == "Bb" || key == "C dorian" || key == "F Mixolydian" || key == "G minor"){
                    $(this).play(accidentalNotes(flats, '_', keyPress));
                }else{
                    flats.push("A", "a");
                }
                if(key == "Eb" || key == "F dorian" || key == "Bb Mixolydian" || key == "C minor"){
                    $(this).play(accidentalNotes(flats, '_', keyPress));
                }else{
                    flats.push("D", "d");
                }
                if(key == "Ab" || key == "Bb dorian" || key == "Eb Mixolydian" || key == "F minor"){
                    $(this).play(accidentalNotes(flats, '_', keyPress));
                }else{
                    flats.push("G", "g");
                }
                if(key == "Db" || key == "Eb dorian" || key == "Ab Mixolydian" || key == "Bb minor"){
                    $(this).play(accidentalNotes(flats, '_', keyPress));
                }else{
                    flats.push("C", "c");
                }
                if(key == "Gb" || key == "Ab dorian" || key == "Db Mixolydian" || key == "Eb minor"){
                    $(this).play(accidentalNotes(flats, '_', keyPress));
                }else{
                    flats.push("F", "f");
                }
                if(key == "Cb" || key == "Db dorian" || key == "Gb Mixolydian" || key == "Ab minor"){
                    $(this).play(accidentalNotes(flats, '_', keyPress));
                }
            }
       //});
    }
})