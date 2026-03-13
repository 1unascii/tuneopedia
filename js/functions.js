$(document).ready(function(){
//octave is a boolean
    //$.accidentalNotes = function(accidentals, modifierString, keyPress, octave){
    function accidentalNotes(accidentals, modifierString, keyPress, octave){
        var accidental = false;
        for(var i=0; i<accidentals.length; i++){
            if(accidentals[i] == keyPress){//keyPress
                accidental = true;
            }
        }
        if(accidental){
            if(octave){
                return (modifierString + lastChar + keyPress);
            }else{
                return (modifierString + keyPress);
            }
        }else{
            if(octave){
                return (lastChar + keyPress);
            }else{
                return (keyPress);
            }
        }
    }
    $.keySpecificPlayBack = function(key, accidentalArray, sharpsOrFlats, abcArray, accidentalChar, sharpsOrFlatsToPush){
        
        for(var i = 0; i < accidentalArray.length; i++){
            //first and last have special cases //they call functions with null values
            //alert(accidentalArray[i]);
            if(i == 0 || i == accidentalArray.length){
                if($.playBack(key, accidentalArray[i], [], abcArray, '')){
                    return $.playBack(key, accidentalArray[i], [], abcArray, '');
                }
            }else
            if(($.playBack(key, accidentalArray[i], sharpsOrFlats, abcArray, accidentalChar))){
                return $.playBack(key, accidentalArray[i], sharpsOrFlats, abcArray, accidentalChar);
            }
            sharpsOrFlats.push(sharpsOrFlatsToPush[i][0], sharpsOrFlatsToPush[i][1]);
        }
    }
    function autoPlayByKey(key, keys, args, sharpsOrFlats, sharpsOrFlatsToPush){
         for(var i = 0; i < keys.length; i++){  
            //first and last have special cases //they call functions with null values 
            //alert(accidentalArray[i]);                   
            if( keys[i][0] == "C" && keys[i+1][0] !== "F" ){
                for(var c = 0; c < keys[i].length; i++){
                    if( key == keys[i][c] ) {
                        return( args[1] + args[0] ); //should be lastChar and keyPress
                    }
                }                                                 
            }else { 
                for(var d = 0; d < keys[i].length; i++){
                    if( key == keys[i][d] ) {
                        return( $.accidentalNotes( sharpsOrFlats, accidentalChar, args[0], args[2] ) );
                    }
                } 
            }  
            sharpsOrFlats.push( sharpsOrFlatsToPush[i][0], sharpsOrFlatsToPush[i][1] );                         
        }               
    }
   
    $.letterCharsAgo = function(chars) {
        var output = '';
        // second character is one behind the found letter
        //so when it is an accidental
        if($.accidentalCharsAgo(chars[1])){
            //pass along to test if it's a double
            return($.doubleAccidentalCharsAgo(chars));
        }else {
            //no accidental was found
            //output will include everything but the last two characters (our suspected accidental notes)
            for(var i = 0; i < chars.length; i++){
                if(i > 1){
                    output += chars[i];
                }
            }
            if(output.length){
                return output;
            }else {
                return false;
            }
        }
    }


    //tests for a double accidental and appends it to the output if it exists
    //always called by letterCharsAgo
    $.doubleAccidentalCharsAgo = function(chars) {
        var output = '';
        //var i = 0
        for(var i = 0; i < chars.length; i++) {
            //if the first char is an accidental
            if(i == 0) {
                //if the second char is the same accidental
                if(chars[0] == chars[1]) {
                    output += chars[0];
                }
            }else {
                //everything but the first char
                output += chars[i];
            }
        }
        if(output.length){
            return output;
        }else {
            return false;
        }
    }
    
    //tests for an accidental the specified number of Characters Ago (charsAgo) e.g. fourCharsAgo /
    $.accidentalCharsAgo = function(charsAgo){
        if(charsAgo == '^' || charsAgo == '_' || charsAgo == '='){
            return true;
        }else {
            return false;
        }
    }

       
    $.getSelectionText = function() {
    
        var text = "";
        if (window.getSelection) {
            text = window.getSelection().toString();
        } else if (document.selection && document.selection.type != "Control") {
            text = document.selection.createRange().text;
        }
        if(text !== ""){
            return text;
        }else{
            return false;
        }
    }
    $.playBack = function(key, keys, accidentals, splitArray, modifier){
        var new_abc = '';
        //testing if the key matches a specified key
        if(key == keys[0] || key == keys[1] || key == keys[2] || key == keys[3]){
            for(var i = 0; i < splitArray.length; i++){
                //accidental or octave mod detection
                for(var c = 0; c < accidentals.length; c++){
                    if(accidentals[c] == splitArray[i] && splitArray[i-1] !== '^' && splitArray[i-1] !== '_' && splitArray[i-1] !== '='){
                        //accidentals or octave modifiers are being added here
                        splitArray[i] = modifier + splitArray[i];
                    }
                }
                //triplet detection
                if(splitArray[i] == '3' && splitArray[i-1] == '(' ){
                    var count = 0;
                    var count2 = 1;
                    while(count < 2){
                        //triplet detection has it's own accidental detection .... for now
                        if(splitArray[i + count2] !== ' ' && splitArray[i + count2] !== '^' && splitArray[i + count2]
                            !== '_' && splitArray[i + count2] !== '=' && splitArray[i + count2]
                            !== ',' && splitArray[i + count2] !== '\'' && splitArray[i + count2]
                            !== '/' && splitArray[i + count2] !== '\'/' && splitArray[i + count2] !== ',/')
                        {
                            for(var c=0; c<accidentals.length; c++){
                                if(accidentals[c] == splitArray[i + count2]){
                                    splitArray[i + count2] = modifier + splitArray[i + count2];
                                }
                            }
                            if(splitArray[i + count2 + 1] == ',' || splitArray[i + count2 + 1] == '\''){
                                splitArray[i + count2 + 1] = splitArray[i + count2 + 1] + '/';
                            }else{
                                splitArray[i + count2] = splitArray[i + count2] + '/';
                            }
                            count++;
                        }
                        count2++;
                    }
                }
                new_abc += splitArray[i];
            }
            
            //alert(new_abc);
            //$('#play').fadeOut(250);
            return new_abc;
        }else {
            return false;
        }
    }
    $.start_new_abc = function(){
        
        var hdr_array = [["X:", 1], ["T:", $('#tune_title').val()], ["R:", $('#tune_type').val()], ["M:", $('#metre').val()],
                         ["L:", $('#defaule_note_length').val()], ["K:", $('#key').val()]];
        var hdr = $.build_abc_hdr(hdr_array);
        if(!abc){

            var abc_editor = new ABCJS.Editor("abc", { canvas_id: "canvas", midi_id:"midi", warnings_id:"warnings"});            
            abc = true;
        }
        
        window.ABCJS.edit.EditArea.prototype.getString = function() {
            return hdr + this.textarea.value;
        }
        
    }
    $.build_abc_hdr = function(headers){
        var hdr = headers[0][0] + [0][1];
        for(i = 0; i < headers.length; i++){
            if(headers[i].length){
                hdr += headers[i][0] + headers[i][1] + "\n";
            }
        }
        return hdr;
    }

})