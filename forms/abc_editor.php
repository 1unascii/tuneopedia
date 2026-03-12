<?php
    session_start();
    include_once('../connect.php');
    //include_once('../functions.php');
    //include_once('../links.php');
?>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/abc_editor.js"></script>
<script src="js/functions.js" type="text/javascript"></script> 
<script src="js/jquery-turtle.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="abcjs_basic_1.10-min.js"></script>

<div id="form_wrapper">
<!--FORM BEGINS-->
<form id="form_for_new_tune">

<label>Title: </label>
<input type="text" id="tune_title" <?php if(isset($_GET['title'])){?>value=<?php echo $_GET['title'];}?> name="tune_title"/>
<br />
<!--pre>
<?php
    $query = "SELECT * FROM tune_types";
    $tune_types = simpleQuery($query);
?>
</pre-->
<label>Tune Type: </label>
<select id="tune_type" name="tune_type">
    <!--option value="0">None</option-->
    <?php
        $count = 0;
        foreach($tune_types as $value){
            echo "<option>" . $tune_types[$count]['tune_type'] . "</option>";
            $count++;
        }
    ?>
    <!--option id="composer_not_listed">Add another</option-->
</select>
<br />
<!--pre>
<?php
    $query = "SELECT * FROM composers";
    $composers = simpleQuery($query);
?>
</pre-->
<label>Composer: </label>
<select id="composer" name="composer">
    <!--option value="0">None</option-->
    <?php
        $count = 0;
        echo "<option></option>";
        foreach($composers as $value){
            echo "<option>" . $composers[$count]['composer_name'] . "</option>";
            $count++;
        }
    ?>
    <!--option id="composer_not_listed">Add another</option-->
</select>
<br />

<label>Metre: </label>
<select id="metre" name="metre">
    <option id="4/4">4/4</option>
    <option id="3/4">3/4</option>
    <option id="2/4">2/4</option>
    <option id="6/8">6/8</option>
    <option id="9/8">9/8</option>
    <option id="12/8">12/8</option>
    <option id="other_metre">Other(Advanced)</option>                
</select>
<br />

<label>Mode: </label>                  
<select id="tune_mode_input">
    <option id="maj">Major/Ionian</option>
    <option id="min">Minor/Aoelian/(natural minor)</option>
    <option id="dor">Dorian</option>
    <option id="mix">Mixolydian</option>
</select>
<br />

<div id="tune_key_input" name="tune_key"><!--This will change with the "tune_mode_input" so the user is shown the options for each mode-->
<label>Key: </label>                  
<select id="key">
    <option id="c">C</option>
    <option id="g">G</option>
    <option id="d">D</option>
    <option id="a">A</option>
    <option id="e">E</option>
    <option id="b">B</option>
    <option id="fsharp">F#</option>
    <option id="csharp">C#</option>
    <option id="f">F</option>
    <option id="bb">Bb</option>
    <option id="eb">Eb</option>
    <option id="ab">Ab</option>
    <option id="db">Db</option>
    <option id="gb">Gb</option>
    <option id="cb">Cb</option>                
</select>
<span id="play_selection"></span>
</div>
<br />

<label>ABC: </label>
<div id="abc_editor">            
<textarea id="abc" rows="10" cols="45" name="tune_body"></textarea>  
<!--span id="abc_code"></span-->        
</div>
<br />
<div id="save_or_login">
<?php
    if(array_key_exists('Authenticated', $_SESSION)){                
       echo '<input type="button" value="save" id="save"/>';                
    }else{
        //Log in to save button
        include('login_from_tune_editor.php');
    }
?>    
</div>
</form>
</div> 

<div id="canvas_wrapper">   
    <div id="warning_canvas_wrapper">    
        <div id="canvas" ></div>
        <div id="warnings">        
    </div>
</div>
<!--div id="midi"></div-->
<div id="warnings"></div-->
