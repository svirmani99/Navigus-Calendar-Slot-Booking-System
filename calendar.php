<?php
    session_start();
    function build_calendar($month, $year) {
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
    $daysOfWeek = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
    $numberDays = date('t',$firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    if($daysOfWeek==0){
        $daysOfWeek=6;
    }
    else{$dayOfWeek = $dayOfWeek-1;}
    $datetoday = date('Y-m-d');
    $calendar = "<table class='table table-bordered'>";
    $calendar .= "<center><h2>$monthName $year</h2>";
    $calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month-1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month-1, 1, $year))."'>Previous Month</a> ";
    $calendar.= " <a class='btn btn-xs btn-primary' href='?month=".date('m')."&year=".date('Y')."'>Current Month</a> ";
    
    $calendar.= "<a class='btn btn-xs btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month+1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month+1, 1, $year))."'>Next Month</a></center><br>";
    
    $calendar .= "<tr>";

    foreach($daysOfWeek as $day) {
    $calendar .= "<th  class='header'>$day</th>";
    } 

    $currentDay = 1;

    $calendar .= "</tr><tr>";

    if ($dayOfWeek > 0) { 
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar .= "<td  class='empty'></td>"; 

        }   
    }
    
     
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
  
    while ($currentDay <= $numberDays) {

    if ($dayOfWeek == 7) {

    $dayOfWeek = 0;
    $calendar .= "</tr><tr>";

    }
          
    $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
    $date = "$year-$month-$currentDayRel";
          
    $dayname = strtolower(date('l', strtotime($date)));
    $eventNum = 0;
    $today = $date==date('Y-m-d')? "today" : "";
    if($dayname=='saturday'||$dayname=='sunday'){
            $calendar.="<td><h4>$currentDay</h4> <button class='btn btn-danger btn-xs'>Holiday</button>";
            }
             else if($date<date('Y-m-d')){
             $calendar.="<td><h4>$currentDay</h4> <button class='btn btn-danger btn-xs'>N/A</button>";
         }else{
            $totalbookings=checkslots($mysqli,$date);
            if($totalbookings==1){
             $calendar.="<td class='$today'><h4>$currentDay</h4> <a href='book.php?date=".$date."' class='btn btn-success btn-xs'>someone is available</a>";
            }

            else{
              $calendar.="<td class='$today'><h4>$currentDay</h4> <a href='book.php?date=".$date."' class='btn btn-success btn-xs'>Book</a>";
            }
         }
            
            
           
            
          $calendar .="</td>";
          // Increment counters
 
          $currentDay++;
          $dayOfWeek++;

     }
     

     if ($dayOfWeek != 7) { 
     
          $remainingDays = 7 - $dayOfWeek;
            for($l=0;$l<$remainingDays;$l++){
                $calendar .= "<td class='empty'></td>"; 
         }

     }
     
    $calendar .= "</tr>";

    $calendar .= "</table>";

    echo $calendar;
 }


function checkslots($mysqli,$date){
$stmt = $mysqli->prepare("select * from bookings where date = ? ");
    $stmt->bind_param('s', $date);
    $totalbookings = 0;
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $totalbookings++;
            }
            
            $stmt->close();
        }
    }
return $totalbookings;
}







?>


<!doctype html>
<html lang="en">
  <head>



    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
       @media only screen and (max-width: 760px),
        (min-device-width: 802px) and (max-device-width: 1020px) {

            table, thead, tbody, th, td, tr {
                display: block;

            }
            
            

            .empty {
                display: none;
            }

            th {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                border: 1px solid #ccc;
            }

            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }



            td:nth-of-type(1):before {
                content: "Sunday";
            }
            td:nth-of-type(2):before {
                content: "Monday";
            }
            td:nth-of-type(3):before {
                content: "Tuesday";
            }
            td:nth-of-type(4):before {
                content: "Wednesday";
            }
            td:nth-of-type(5):before {
                content: "Thursday";
            }
            td:nth-of-type(6):before {
                content: "Friday";
            }
            td:nth-of-type(7):before {
                content: "Saturday";
            }


        }


        @media only screen and (min-device-width: 320px) and (max-device-width: 480px) {
            body {
                padding: 0;
                margin: 0;
            }
        }


        @media only screen and (min-device-width: 802px) and (max-device-width: 1020px) {
            body {
                width: 495px;
            }
        }

        @media (min-width:641px) {
            table {
                table-layout: fixed;
                background-color: #5F9EA0;
            }
            td {
                width: 33%;
            }
        }
        
        .row{
            margin-top: 20px;
        }
        
        .today{
            background:yellow;
        }
        
        
        
    </style>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Book a slot</title>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <h3><a class="navbar-brand" href="#">Book a slot</a></h3>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <h3><a class="nav-link" href="index.php">Home</a></h3>
      </li>

      <li class="nav-item">
     <h3><a class="nav-link" href="index.php?logout='1'">logout</a></h3> 
      </li>

     
    </ul>

  <div class="navbar-collapse collapse">
  <ul class="navbar-nav ml-auto">
  <li class="nav-item active">
       <h3> <a class="nav-link" href="#"> <img src="https://img.icons8.com/metro/26/000000/guest-male.png"> <?php echo "Welcome ". $_SESSION['username']?></a></h3>
      </li>
  </ul>
  </div>


  </div>
</nav>

<div class="container mt-4">
<h3><?php echo "Welcome ". $_SESSION['username']?>! Book a slot</h3>
<hr>

<div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                     $dateComponents = getdate();
                     if(isset($_GET['month']) && isset($_GET['year'])){
                         $month = $_GET['month'];            
                         $year = $_GET['year'];
                     }else{
                         $month = $dateComponents['mon'];            
                         $year = $dateComponents['year'];
                     }
                    echo build_calendar($month,$year);
                ?>
            </div>
        </div>
    </div>

</div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>