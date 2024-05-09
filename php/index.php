<?php
include "database.php";
$sql = "SELECT * FROM doctor";
$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="front.css" />
  <title>SmartCare Hub</title>
</head>

<body>
  <header>
    <nav class="section__container nav__container">
      <div class="nav__logo">SmartCare<span>Hub</span></div>
      <ul class="nav__links">
        <li class="link"><a href="#">Home</a></li>
        <li class="link"><a href="#aboutus">About Us</a></li>
        <!-- <li class="link"><a href="#">Services</a></li> -->
        <li class="link"><a href="#">Pages</a></li>
        <li class="link"><a href="#">Blog</a></li>
      </ul>
      <button class="btn">Contact Us</button>
    </nav>
    <div class="section__container header__container">
      <div class="header__content">
        <h1>Providing an Exceptional Patient Experience</h1>
        <p>
          Welcome, where exceptional patient experiences are our priority.
          With compassionate care, state-of-the-art facilities, and a
          patient-centered approach, we're dedicated to your well-being. Trust
          us with your health and experience the difference.
        </p>
        <button class="btn">See Services</button>
      </div>
      <div class="header__form">
        <form>
          <h4>Book Now</h4>
          <input type="text" placeholder="First Name" />
          <input type="text" placeholder="Last Name" />
          <input type="text" placeholder="Address" />
          <input type="text" placeholder="Phone No." />
          <script>
            function working(){
              alert("working on it");
            }
          </script>
          <button class="btn form__btn" onclick="working()">Book Appointment</button>
        </form>
      </div>
    </div>
  </header>
  <section class="section__container about__container" id="aboutus">
    <div class="about__content">
      <h2 class="section__header">About Us</h2>
      <p>
        Welcome to our healthcare website, your one-stop destination for
        reliable and comprehensive health care information. We are committed
        to promoting wellness and providing valuable resources to empower you
        on your health journey.
      </p>
      <p>
        Explore our extensive collection of expertly written articles and
        guides covering a wide range of health topics. From understanding
        common medical conditions to tips for maintaining a healthy lifestyle,
        our content is designed to educate, inspire, and support you in making
        informed choices for your health.
      </p>
      <p>
        Discover practical health tips and lifestyle advice to optimize your
        physical and mental well-being. We believe that small changes can lead
        to significant improvements in your quality of life, and we're here to
        guide you on your path to a healthier and happier you.
      </p>
    </div>
    <div class="about__image">
      <img src="../img/about.jpg" alt="about" />
    </div>
  </section>
  <section class="section__container doctors__container">
    <div class="doctors__header">
      <div class="doctors__header__content">
        <h2 class="section__header">Our Doctors</h2>
        <p>
          We take pride in our exceptional team of doctors, each a specialist
          in their respective fields.
        </p>
      </div>

    </div>

    <div class="doctors__grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="doctors__card">
          <div class="doctors__card__image">
            <img src="../img/admin_profile.jpg" alt="Image Not Found">
          </div>
          <h4><?php echo $row['fullName']; ?></h4>
          <p><?php echo $row['speciality']; ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  </section>

  <footer class="footer">
    <?php
    include ("footer.php");
    ?>
  </footer>
</body>

</html>