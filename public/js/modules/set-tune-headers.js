$(document).ready(function(){
    
        
    $('#tune_mode_input').change(function(){
        var id = $(this).find("option:selected").attr("id");
        switch (id){
            case "maj":
                $('#key').load('fragment/mode-options/major?id=' + $('#key').find("option:selected").attr("id"), function(){$('#tune_mode_input').focus();});
            break;
            case "min":
                $('#key').load('fragment/mode-options/minor?id=' + $('#key').find("option:selected").attr("id"), function(){$('#tune_mode_input').focus();});
            break;
            case "dor":
                $('#key').load('fragment/mode-options/dorian?id=' + $('#key').find("option:selected").attr("id"), function(){$('#tune_mode_input').focus();});
            break;
            case "mix":
                $('#key').load('fragment/mode-options/mixolydian?id=' + $('#key').find("option:selected").attr("id"), function(){$('#tune_mode_input').focus();});
            break;            
        }
        start_new_abc();
    })  
    
    
    function start_new_abc(){        
        
        var hdr = build_abc_hdr(            
            $('#tune_title').val(), 
            $('#tune_type').val(), 
            $('#metre').val(), 
            $('#default_note_length').val(), 
            $('#key').val()            
        );
        
        abc_editor = new ABCJS.Editor("abc", { canvas_id: "canvas", midi_id:"midi", warnings_id:"warnings" });
        
        window.ABCJS.edit.EditArea.prototype.getString = function() {
            return hdr + this.textarea.value;
        }    
        
    };
    
    //var editor1 = document.getElementById("abc");
    //editor1.spellcheck = false;
    
    
    var lastTitle = '';
    var lastType = $('#tune_type').find("option:selected").attr("id");
    var lastMetre = $('#metre').find("option:selected").attr("id");
    var lastLength = $('#default_note_length').find("option:selected").attr("id");
    var lastKey = $('#key').find("option:selected").attr("id");
    var lastMode = $('#tune_mode_input').find("option:selected").attr("id");
    setInterval(function (){         
        if($('#tune_title').val() !== lastTitle){
            lastTitle = $('#tune_title').val();
            start_new_abc();                       
        }else 
        if($('#tune_type').find("option:selected").attr("id") !== lastType){
            lastType = $('#tune_type').find("option:selected").attr("id")
            start_new_abc();                       
        }else 
        if($('#metre').find("option:selected").attr("id") !== lastMetre){
            lastMetre = $('#metre').find("option:selected").attr("id");
            start_new_abc();           
        }else 
        if($('#default_note_length').find("option:selected").attr("id") !== lastLength){
            lastLength = $('#default_note_length').find("option:selected").attr("id");
            start_new_abc();            
        }else 
        if($('#key').find("option:selected").attr("id") !== lastKey){
            lastKey = $('#key').find("option:selected").attr("id");
            start_new_abc();            
        } else 
        if($('#tune_mode_input').find("option:selected").attr("id") !== lastMode){            
            /*var lastKey2 = '';
            var intervalId = setInterval(function(){
                if($('#key').val() !== lastKey2){
                   lastKey2 = $('#key').val();
                    start_new_abc();
                }        
            }, 666);*/
            lastMode = $('#tune_mode_input').find("option:selected").attr("id");
            start_new_abc();
        }       
    }, 500);    
    $('#tune_title').on('change keyup paste mouseup', function(){
         start_new_abc();
    })
    $('#tune_type').change(function(){
        start_new_abc();
    })
    $('#metre').change(function(){
        start_new_abc();
    })
    $('#default_note_length').change(function(){
        start_new_abc();
    })
    $('#tune_mode_input').on('change keyup paste mouseup', function(){
        start_new_abc();
    })    
    $('#key').on('change keyup paste mouseup', function(){
        start_new_abc();
        //clearInterval(intervalId);
    })
    
    
    function build_abc_hdr(title, type, metre, length, key){
    
        var hdr = "X:1\n";
        if(title){
            hdr += "T:" + title + "\n";            
        }
        if(type){
            hdr += "R:" + type + "\n";
        }
        if(metre){
            hdr += "M:" + metre + "\n";
        }
        if(length){
            hdr += "L:" + length + "\n";
        }
        if(key){
            hdr += "K:" + key + "\n";
        }                
        return hdr;
    }
    
})
