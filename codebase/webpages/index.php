<?php session_start();?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="homestyle.css">
  </head>
  <body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-container">
            <!-- home button/logo -->
            <a href="./mainpage.html" id="home-button">Fraction Runner</a>

            <!-- other navbar items -->
            <ul class="navbar-menu">
                <li class="navbar-item">
                    <!-- SESSION USAGE -->
                    <!-- line below displays username, put it in the navbar -->
                    <p><?php
                    if (isset($_SESSION["username"])){
                      echo 'Hello, '; echo $_SESSION['username'];
                    }
                    ?></p>
                </li>
                <li class="navbar-item">
                    <?php
                    if (isset($_SESSION["username"])){
                        echo '<a class="navbar-link" href="./GameLogin/signout.php">Account</a>';
                    }
                    else{
                        echo '<a class="navbar-link" href="./GameLogin/signlog.php">Log In</a>';
                    }
                ?>
                </li>
                <li class="navbar-item">
                    <a class="navbar-link" href="./Gamepage/game.php">Play</a>
                </li>
                <li class="navbar-item">
                    <a class="navbar-link" href="./Scorepage/ScorePage.php">Scoreboard</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ABOUT SECTION -->
    <div class="main" id="about">
        <div class="main-container">
            <div class="about-content">
                <h1>WHAT IS FRACTION RUNNER?</h1>
                <h2>Let's learn about math and have some fun!</h2>
                <p>Here you can learn about the concept of our game and also watch a video to see how it works. We have included some static images as well as an animated gif to give you a better idea of what the game is like. If you're interested in playing, click the button below to start the game. And if you want to see the extended scoreboard, click the other button.</p>
                <a href=https://www.youtube.com/watch?v=daro6K6mym8>Watch video</a>
            </div>
            <div class="about-img-container">
                <img class="-about-img" src="static_image_1.jpg" alt="Static Image 1">
                <img class="-about-img" src="static_image_2.jpg" alt="Static Image 2">
                <img class="-about-img" src="static_image_3.jpg" alt="Static Image 3">
                <img class="-about-img" src="animated_gif.gif" alt="Animated Gif of Game">
            </div>
        </div>
    </div>

    <!-- LOGIN/SIGN UP SECTION -->
      <div class="main" id="signlog">
        <div class="alt-container">
            <div class="alt-content">
                <h1>Log In or Sign Up</h1>
                <?php
                    if (isset($_SESSION["username"])){
                        echo '<a href="./GameLogin/signout.php"><button>Account Maintenance and Log Out</button></a>';
                    }
                    else{
                        echo '<a href="./GameLogin/signlog.php"><button>Log In and Sign Up</button></a>';
                    }
                ?>
            </div>
        </div>
      </div>

      <!-- PLAY SECTION -->
      <div class="main" id="play">
        <div class="alt-container">
            <div class="alt-content">
                <h1>Start Playing!</h1>
                <a href="./Gamepage/game.php"><button>Play</button></a>
            </div>
        </div>
      </div>

      <!-- SCOREBOARD SECTION -->
      <div class="main" id="scoreboard">
        <div class="alt-container">
            <div class="alt-content">
                <h1>What's Your Rank?</h1>
                <a href="./Scorepage/ScorePage.php"><button>Scoreboard</button></a>
            </div>
        </div>
      </div>


    <footer class="main">
      <p>&copy; 2023 Game Company. All rights reserved.</p>
    </footer>

  </body>
</html>