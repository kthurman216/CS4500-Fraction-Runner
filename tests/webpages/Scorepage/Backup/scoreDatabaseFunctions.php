<?php
//This sets the connection up, also has the password included
$servername = "127.0.0.1";
////////////////////REMOVE THISv
#$sqlusername = "root";
#$sqlpassword = "VfX!565WW!t552";
////////////////////REMOVE THIS^
$sqlusername = "siteuser";
$sqlpassword = "edcvfr43edcvfr4";
$dbname = "scoreboard_dba";
//these variables are for status reporting
$connectionstatus = "Connection not attempted.";
$connectbool = false;
try {
    // Create connection, username and pw here are for the sql server
    $dbconn = $mysqli = new mysqli($servername, $sqlusername, $sqlpassword, $dbname);
// Check connection and report errors
    error_reporting(E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connectionstatus = "Connection Successful.";
    $connectbool = true;
} catch (Exception $e){
    $connectionstatus = "Connection to server failed";
    echo $connectionstatus;
}

#echo "Connection successful.";
# So what the following does is effectively makes a second table where there is a counter column called score_rank
# because mysql has deprecated the simple version of this that might be buggy sometimes instead of fixing it so we can
# have a normal ranking column because "ouh mah-hai, it's a QUERY language you shouldn't put your *rank* on the table".
# This means I can't just have the ranks on the table and auto-update with an incrementor every time a row is added
# which would allow me to pull ranks directly by number or username, That would be too simple, of course.
# So instead of auto-updating with a simple in-query incrementor I now have to query it to:
# 1) Generate a second temporary table off of the username (That's the first line) from the original table (line 2)
# 2) Join these two together by comparing the scores to *themselves* (i.e. the score_rank column on t2 is generated by
#    literally taking the row number after the table has been ordered by "how many user scores are higher than or equal
#    to the user score being looked at,
# 3) Select the username in question when applicable
# 4) include columns not referenced in the where clause in the group by (group by is used for the aggregation of results)
# 5) finally order the thing by the score_rank. but we can't SELECT by the score rank because that would be too easy.


########unfinished pull of digits table
/*$simdigits = mysqli_query($mysqli, "SELECT digits.user_name, users.user_score, users.digits, count(t2.user_name) score_rank
FROM users
LEFT JOIN users t2 ON t2.user_score >= users.user_score
/*WHERE users.user_name='test2'*/
/*GROUP BY user_name, user_score, digits
ORDER BY score_rank;");*/
class scoreDatabaseFunctions
{
    //constructor for a ranking board
    public $ranking;
    public $currentDigits;
    public $currentScore;
    //constructor for a ranking board
    function __construct(mysqli $dbconn){
        try {
            $this->ranking = mysqli_query($dbconn, "SELECT users.user_name, users.user_score, users.digits, count(t2.user_name) score_rank
                FROM users
                LEFT JOIN users t2 ON t2.user_score >= users.user_score
                GROUP BY user_name, user_score, digits
                ORDER BY score_rank;");
        } catch (PDOException $e){
            echo "ERROR: Incorrect database permissions or disconnection.";
        }
    }
    //function to retrieve pre-existing digits strings, it returns a string as a status note, and changes public variables
    function retrieveDigits(mysqli $dbconn, $digits){
        try {
            //Note: If( Exists is relatively slow, but other implementations are more complex. If this was built to exist
            //      on a well-trafficked site with a more complex database, I would change it to that
            //What this actually does is check if, within the fractions table in scoreboard_dba database, a row exists
            //where digits are equal to the input digits and returns 1/0 as a true or false
            $result = mysqli_query($dbconn, "SELECT IF( EXISTS( 
                SELECT divisor, fraction 
                FROM scoreboard_dba.fractions 
                WHERE digits = $digits), 
                1, 0) as RESULT;");
            //if false, we use currentDigits as a string array to avoid misoutputs and
            //return a positive on the function working, but negative on digits being retrieved
            if(mysqli_fetch_array($result) == 0){
                $this->currentDigits[0] = "Not Found.";
                $this->currentDigits[1] = "This is the first time these digits have been generated!";
                return "Successful check. Digits did not previously exist.";
            }
            //if true, we turn currentDigits into a mysqli_query that the information can be pulled from
            else{
                $this->currentDigits = mysqli_query($dbconn, "SELECT divisor, fraction 
                    FROM scoreboard_dba.fractions 
                    WHERE digits = $digits;");
            }
            return "Successful retrieval.";
        }catch (PDOException $e){
            return "ERROR: Incorrect database permissions or disconnection.";
        }
    }
    //function to retrieve a specific user's score, it returns a string as a status note, and changes public variables
    function retrieveUserScore(mysqli $dbconn, $username){
        try {
            $result = mysqli_query($dbconn, "SELECT IF( EXISTS( 
                SELECT user_score, digits 
                FROM scoreboard_dba.users 
                WHERE user_name = $username), 
                1, 0) as RESULT;");
            if(mysqli_fetch_array($result) == 0){
                $this->currentScore[0] = "Not Found.";
                $this->currentScore[1] = "User does not exist.";
                return "Successful check. User does not exist.";
            }
            else{
                $this->currentScore = mysqli_query($dbconn, "SELECT user_score, digits
                    FROM scoreboard_dba.users 
                    WHERE user_name = $username;");
            }
            return "Successful retrieval";
        }catch (PDOException $e){
            return "ERROR: Incorrect database permissions or disconnection.";
        }
    }
    //function to add a new user, dbconn must be mysqli,
    function addNewUser(mysqli $dbconn, string $newname, string $newpass){
        try {
            mysqli_query($dbconn, "INSERT INTO scoreboard_dba.users VALUES (0,$newname, 0, $newpass, '0')");
            return "Successful new user insertion.";
        }catch (PDOException $e){
            if ($e->errorInfo[1]==1062)
                return "ERROR: Username already exists.";
            else{
                return "ERROR: Incorrect database permissions or disconnection.";
            }
        }
    }
    //Function to change user score and most recent input digits
    function setUserScore(mysqli $dbconn, string $name, int $userscore, string $digits){
        try {
            mysqli_query($dbconn, "UPDATE scoreboard_dba.users 
            SET user_score = $userscore,
                digits = $digits
                WHERE user_name = $name AND $userscore > user_score;");
        }catch(PDOException $e){
            return "ERROR: Incorrect database permissions or disconnection.";
        }
    }
    //function to change user's password
    function changePass(mysqli $dbconn, string $name, string $oldpass, string $newpass){
        try {
            $passfinder = mysqli_query($dbconn, "UPDATE scoreboard_dba.users 
            SET password = $newpass,
            WHERE user_name = $name AND password = $oldpass;");
        } catch (PDOException $e){
            return "ERROR: Incorrect database permissions or disconnection.";
        }
    }
    //Function to delete a user
    function deleteUser(mysqli $dbconn, $name, $pass){
        //First query to find the entry password for this user and check for correct permissions
        try {
            $passfinder = mysqli_query($dbconn, "SELECT users.password
            FROM users
            WHERE users.user_name='$name'
            ");
        } catch(PDOException $e){
            return "Error in permissions or connection to database.";
        }
        //the second block is to find out if the user exists with the if statement, then check if the password is correct
        if ($passfinder->num_rows > 0) {
            try{
                mysqli_query($dbconn, "DELETE FROM scoreboard_dba.users
                        WHERE user_name = $name AND password = $pass;");
                return "Successfully deleted";
            } catch(PDOException $e){
                return "ERROR: incorrect password";
            }
        } else {
            return "ERROR: user does not exist";
        }
    }

}