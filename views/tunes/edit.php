<form action="page/tunes" method="post">
<input type="hidden" name="tune_id" value="<?php echo $_GET['tune_id'];?>"/>
<!--FORM TABLE-->
<table>
<tr>
    <td><!--TUNE TITLE-->
        <label>Title: </label>
    </td>
    <td>
        <input type="text" name="title" value="<?php echo $row['title'];?>"/>
    </td>
</tr>
<tr>
    <td><!--TUNE TYPE-->
        <label>Tune Type: </label>
    </td>
    <td>
        <select name="type">
            <!--TUNE TYPE SELECTION OPTIONS (is within td)-->
            <option value="Reel" <?php selectOption($row, 'type', 'Reel');?>>Reel</option>
            <option value="Jig" <?php selectOption($row, 'type', 'Jig');?>>Jig</option>
            <option value="Slip Jig" <?php selectOption($row, 'type', 'Slip Jig');?>>Slip Jig</option>
            <option value="Polka" <?php selectOption($row, 'type', 'Polka');?>>Polka</option>
            <option value="Hornpipe" <?php selectOption($row, 'type', 'Hornpipe');?>?>>Hornpipe</option>
            <option value="Slide" <?php selectOption($row, 'type', 'Slide');?>>Slide</option>
            <option value="Mazurka" <?php selectOption($row, 'type', 'Mazurka');?>>Mazurka</option>
            <option value="Barndance" <?php selectOption($row, 'type', 'Barndance');?>>Barndance</option>
            <option value="Waltz" <?php selectOption($row, 'type', 'Waltz');?>>Waltz</option>
            <option value="Strathspey" <?php selectOption($row, 'type', 'Strathspey');?>>Strathspey</option>
            <option value="Highland" <?php selectOption($row, 'type', 'Highland');?>>Highland</option>
            <option value="March" <?php selectOption($row, 'type', 'March');?>>March</option>
            <option value="Air" <?php selectOption($row, 'type', 'Air');?>>Air</option>
            <option value="Song" <?php selectOption($row, 'type', 'Song');?>>Song</option>
            <option value="Ballad" <?php selectOption($row, 'type', 'Ballad');?>>Ballad</option>
            <option value="Hymn" <?php selectOption($row, 'type', 'Hymn');?>>Hymn</option>  
            <option value="An dro" <?php selectOption($row, 'type', 'An dro');?>>An Dro</option>
            <option value="Dans fisel" <?php selectOption($row, 'type', 'Dans fisel');?>>Dans fisel</option>
            <option value="Dans plin" <?php selectOption($row, 'type', 'Dans plin');?>>Dans plin</option>
            <option value="Gavotte" <?php selectOption($row, 'type', 'Gavotte');?>>Gavotte</option>
            <option value="Hanter dro" <?php selectOption($row, 'type', 'Hanter dro');?>>Hanter dro</option>
            <option value="Laride" <?php selectOption($row, 'type', 'Laride');?>>Laride</option>
            <option value="Ridee" <?php selectOption($row, 'type', 'Ridee');?>>Ridee</option>
            <option value="Rond de Loudeac" <?php selectOption($row, 'type', 'Rond de Loudeac');?>>Rond de Loudeac</option>
            <option value="Rond de Saint-Vincent" <?php selectOption($row, 'type', 'Rond de Saint-Vincent');?>>Rond de Saint-Vincent</option>
            <option value="Other" <?php selectOption($row, 'type', 'Other');?>>Other</option>
            <!--END SELECTION OPTIONS-->
        </select>
    </td>
</tr>
    <td><!--TIME SIGNATURE-->
        <label>Metre: </label>
    </td>
    <td>
        <select name="metre">
            <option value="2/8" <?php selectOption($row, 'metre', '2/8');?>>2/8</option>
            <option value="3/8" <?php selectOption($row, 'metre', '3/8');?>>3/8</option>
            <option value="4/8" <?php selectOption($row, 'metre', '4/8');?>>4/8</option>
            <option value="5/8" <?php selectOption($row, 'metre', '5/8');?>>5/8</option>
            <option value="6/8" <?php selectOption($row, 'metre', '6/8');?>>6/8</option>
            <option value="7/8" <?php selectOption($row, 'metre', '7/8');?>>7/8</option>
            <option value="8/8" <?php selectOption($row, 'metre', '8/8');?>>8/8</option>
            <option value="9/8" <?php selectOption($row, 'metre', '9/8');?>>9/8</option>
            <option value="12/8" <?php selectOption($row, 'metre', '12/8');?>>12/8</option>
            <option value="2/4" <?php selectOption($row, 'metre', '2/4');?>>2/4</option>
            <option value="3/4" <?php selectOption($row, 'metre', '3/4');?>>3/4</option>
            <option value="4/4" <?php selectOption($row, 'metre', '4/4');?>>4/4</option>
            <option value="5/4" <?php selectOption($row, 'metre', '5/4');?>>5/4</option>
            <option value="6/4" <?php selectOption($row, 'metre', '6/4');?>>6/4</option>
            <option value="7/4" <?php selectOption($row, 'metre', '7/4');?>>7/4</option>
            <option value="7/4" <?php selectOption($row, 'metre', '8/4');?>>8/4</option>
            <option value="9/4" <?php selectOption($row, 'metre', '9/4');?>>9/4</option>
            <option value="7/4" <?php selectOption($row, 'metre', '12/4');?>>12/4</option>
            <option value="2/2" <?php selectOption($row, 'metre', '2/2');?>>2/2</option>
            <option value="3/2" <?php selectOption($row, 'metre', '3/2');?>>3/2</option>
            <option value="4/2" <?php selectOption($row, 'metre', '4/2');?>>4/2</option>
            <option value="5/2" <?php selectOption($row, 'metre', '5/2');?>>5/2</option>
            <option value="6/2" <?php selectOption($row, 'metre', '6/2');?>>6/2</option>
            <option value="7/2" <?php selectOption($row, 'metre', '7/2');?>>7/2</option>
            <option value="8/2" <?php selectOption($row, 'metre', '8/2');?>>8/2</option>
            <option value="9/2" <?php selectOption($row, 'metre', '9/2');?>>9/2</option>
            <option value="12/2" <?php selectOption($row, 'metre', '12/2');?>>12/2</option>
            <option value="2/16" <?php selectOption($row, 'metre', '2/16');?>>2/16</option>
            <option value="3/16" <?php selectOption($row, 'metre', '3/16');?>>3/16</option>
            <option value="4/16" <?php selectOption($row, 'metre', '4/16');?>>4/16</option>
            <option value="5/16" <?php selectOption($row, 'metre', '5/16');?>>5/16</option>
            <option value="6/16" <?php selectOption($row, 'metre', '6/16');?>>6/16</option>
            <option value="7/16" <?php selectOption($row, 'metre', '7/16');?>>7/16</option>
            <option value="8/16" <?php selectOption($row, 'metre', '8/16');?>>8/16</option>
            <option value="9/16" <?php selectOption($row, 'metre', '9/16');?>>9/16</option>
            <option value="12/16" <?php selectOption($row, 'metre', '12/16');?>>12/16</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <label>Length: </label>
    </td>
    <td>
        <select name="length">
            <option value="1/8" <?php selectOption($row, 'length', '1/8');?>>1/8</option>
            <option value="1/2" <?php selectOption($row, 'length', '1/2');?>>1/2</option>
            <option value="1/4" <?php selectOption($row, 'length', '1/4');?>>1/4</option>
            <option value="1/16" <?php selectOption($row, 'length', '1/16');?>>1/16</option>
            <option value="1/32" <?php selectOption($row, 'length', '1/32');?>>1/32</option>
            <option value="1/64" <?php selectOption($row, 'length', '1/64');?>>1/64</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <label>Composer: </label>
    </td>
    <td>
        <input type="text" name="composer" value="<?php echo $row['composer'];?>"/>
    </td>
</tr>
<tr>
    <td>
        <label>Origin: </label>
    </td>
    <td>
        <select name="nationality">
            <option value="Irish" <?php selectOption($row, 'nationality', 'Irish');?>>Irish</option>
            <option value="Scottish" <?php selectOption($row, 'nationality', 'Scottish');?>>Scottish</option>
            <option value="Welsh" <?php selectOption($row, 'nationality', 'Welsh');?>>Welsh</option>
            <option value="Breton" <?php selectOption($row, 'nationality', 'Breton');?>>Breton</option>
            <option value="Cape Breton" <?php selectOption($row, 'nationality', 'Cape Breton');?>>Cape Breton</option>
            <option value="Appalachian"<?php selectOption($row, 'nationality', 'Appalachian');?> >Appalachian</option>
            <option value="German" <?php selectOption($row, 'nationality', 'German');?>>German</option>
            <option value="Swedish" <?php selectOption($row, 'nationality', 'Swedish');?>>Swedish</option>
            <option value="English" <?php selectOption($row, 'nationality', 'English');?>>English</option>
            <option value="French" <?php selectOption($row, 'nationality', 'French');?>>French</option>
            <option value="Spanish"<?php selectOption($row, 'nationality', 'Spanish');?> >Spanish</option>
            <option value="Scandinavian"<?php selectOption($row, 'nationality', 'Scandinavian');?>>Scandinavian</option>
            <option value="Other"<?php selectOption($row, 'nationality', 'Other');?>>Other</option>
        </select>
    </td>
</tr>
<tr>
    <td>
        <label>Instrument: </label>
    </td>
    <td>
        <?php
        //WE NEED THE AUTHOR_ID TO LOAD CUSTOMIZED VALUES IN THE FORM
        //AS WELL AS TO SUBMIT IT TO THE DATABASE WITH THE FORM DATA
        $author_id = $_SESSION['author_id'];
        //SELECT THE INSTRUMENTS THE USER PLAYS FROM THE USER TABLE
        $instrument_query = "SELECT primary_instrument, secondary_instrument, 
                    tertiary_instrument, quaternary_instrument, 
                    quinary_instrument, senary_instrument, 
                    octonary_instrument, nonary_instrument
                    FROM users
                    WHERE user_id = '$author_id';";
        //STANDARD TURNING RESULTS INTO AN ARRAY            
        $result2 = mysql_query($instrument_query, $con);
        $row2 = mysql_fetch_array($result2);                    
        ?>
    <!--DISPLAY EACH INSTRUMENT IN THE SELECT FIELD, SELECTING THE CURRENT VALUE-->
        <select name="instrument">
            <?php
                //FILTER OUT NULL VALUES
                foreach ($row2 as $value2){
                    if ($value2 == NULL){
                        unset($row2[$value2]);
                    }
                }
                //PRE SELECTS THE CURRENT VALUE IN THE INSTRUMENT FIELD
                $index=0;
                while ($row2[$index]){
                    if ($row2[$index] == $row['instrument']){
                        //SELECTED
                    ?>                                
                        <option value="<?php echo $row2[$index];?>" selected="true">
                        <?php echo $row2[$index];?>
                        </option>
                    <?php
                    } else {
                        //NOT SELECTED
                    ?>
                        <option value="<?php echo $row2[$index];?>">
                        <?php echo $row2[$index];?>
                        </option>
                    <?php
                    }
                    $index++;
                }
            ?>
                           
        </select>
    </td>
</tr>
<tr>
    <td>
        <label>Key: </label>
    </td>
    <td>                        
        <select name="key">
            <option value="C" <?php selectOption($row, 'key', 'C');?>>C</option>
            <option value="G" <?php selectOption($row, 'key', 'G');?>>G</option>
            <option value="D" <?php selectOption($row, 'key', 'D');?>>D</option>
            <option value="A" <?php selectOption($row, 'key', 'A');?>>A</option>
            <option value="E" <?php selectOption($row, 'key', 'E');?>>E</option>
            <option value="B" <?php selectOption($row, 'key', 'B');?>>B</option>
            <option value="F#" <?php selectOption($row, 'key', 'F#');?>>F#</option>
            <option value="F" <?php selectOption($row, 'key', 'F');?>>F</option>  
            <option value="Bb" <?php selectOption($row, 'key', 'Bb');?>>Bb</option>                                              
            <option value="Eb" <?php selectOption($row, 'key', 'Eb');?>>Eb</option>
            <option value="Ab" <?php selectOption($row, 'key', 'Ab');?>>Ab</option>
            <option value="Db" <?php selectOption($row, 'key', 'Db');?>>Db</option> 
            <option value="C dorian" <?php selectOption($row, 'key', 'C dorian');?>>C dorian</option>
            <option value="G dorian" <?php selectOption($row, 'key', 'G dorian');?>>G dorian</option>
            <option value="D dorian" <?php selectOption($row, 'key', 'D dorian');?>>D dorian</option>
            <option value="A dorian" <?php selectOption($row, 'key', 'A dorian');?>>A dorian</option>
            <option value="E dorian" <?php selectOption($row, 'key', 'E dorian');?>>E dorian</option>
            <option value="B dorian" <?php selectOption($row, 'key', 'B dorian');?>>B dorian</option>
            <option value="F# dorian" <?php selectOption($row, 'key', 'F# dorian');?>>F# dorian</option>
            <option value="F dorian" <?php selectOption($row, 'key', 'F dorian');?>>F dorian</option>  
            <option value="Bb dorian" <?php selectOption($row, 'key', 'Bb dorian');?>>Bb dorian</option>                                              
            <option value="Eb dorian" <?php selectOption($row, 'key', 'Eb dorian');?>>Eb dorian</option>
            <option value="Ab dorian" <?php selectOption($row, 'key', 'Ab dorian');?>>Ab dorian</option>
            <option value="Db dorian" <?php selectOption($row, 'key', 'Db dorian');?>>Db dorian</option>
            <option value="C mixolydian" <?php selectOption($row, 'key', 'C mixolydian');?>>C mixolydian</option>
            <option value="G mixolydian" <?php selectOption($row, 'key', 'G mixolydian');?>>G mixolydian</option>
            <option value="D mixolydian" <?php selectOption($row, 'key', 'D mixolydian');?>>D mixolydian</option>
            <option value="A mixolydian" <?php selectOption($row, 'key', 'A mixolydian');?>>A mixolydian</option>
            <option value="E mixolydian" <?php selectOption($row, 'key', 'E mixolydian');?>>E mixolydian</option>
            <option value="B mixolydian" <?php selectOption($row, 'key', 'B mixolydian');?>>B mixolydian</option>
            <option value="F# mixolydian" <?php selectOption($row, 'key', 'F# mixolydian');?>>F# mixolydian</option>
            <option value="F mixolydian" <?php selectOption($row, 'key', 'F mixolydian');?>>F mixolydian</option>  
            <option value="Bb mixolydian" <?php selectOption($row, 'key', 'Bb mixolydian');?>>Bb mixolydian</option>                                              
            <option value="Eb mixolydian" <?php selectOption($row, 'key', 'Eb mixolydian');?>>Eb mixolydian</option>
            <option value="Ab mixolydian" <?php selectOption($row, 'key', 'Ab mixolydian');?>>Ab mixolydian</option>
            <option value="Db mixolydian" <?php selectOption($row, 'key', 'Db mixolydian');?>>Db mixolydian</option>
            <option value="C minor" <?php selectOption($row, 'key', 'C minor');?>>C minor</option>
            <option value="G minor" <?php selectOption($row, 'key', 'G minor');?>>G minor</option>
            <option value="D minor" <?php selectOption($row, 'key', 'D minor');?>>D minor</option>
            <option value="A minor" <?php selectOption($row, 'key', 'A minor');?>>A minor</option>
            <option value="E minor" <?php selectOption($row, 'key', 'E minor');?>>E minor</option>
            <option value="B minor" <?php selectOption($row, 'key', 'B minor');?>>B minor</option>
            <option value="F# minor" <?php selectOption($row, 'key', 'F# minor');?>>F# minor</option>
            <option value="F minor" <?php selectOption($row, 'key', 'F minor');?>>F minor</option>  
            <option value="Bb minor" <?php selectOption($row, 'key', 'Bb minor');?>>Bb minor</option>                                              
            <option value="Eb minor" <?php selectOption($row, 'key', 'Eb minor');?>>Eb minor</option>
            <option value="Ab minor" <?php selectOption($row, 'key', 'Ab minor');?>>Ab minor</option>
            <option value="Db minor" <?php selectOption($row, 'key', 'Db minor');?>>Db minor</option> 
        </select>
    </td>
</tr>
<tr>
    
    <td colspan=7>
    <h6>Please do not attempt to alter anything other than the tune body in the text area below. 
    It is there only for the functionality of the interactive editor. Do not add or remove any lines before and including "K:"</h6>
        <textarea name="body" id="abc" spellcheck="false" rows=10 cols=100><?php
        $n="\n";
        echo "X:1".$n;
        echo "T:".$row['title'].$n;
        echo "M:".$row['metre'].$n;
        echo "L:".$row['length'].$n;
        echo "K:".$row['key'].$n;                    
        echo $row['body'];
        ?></textarea>
    </td>
    <td>
        
    </td>                      
</tr>
<tr>
    <td> 
    <input type="hidden" name="author_id" value="<?php echo $author_id;?>"/>
    </td>
    <td>
        <input type="submit" value="update" name="update_tune"/>
    </td>
</tr>
</table>
        </form>
