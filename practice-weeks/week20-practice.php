<?php
  error_reporting(E_ALL);

  require_once 'week-19-php/tableBuilder.php';
  require_once 'week-19-php/dbConn.class.php';

  $conn = dbConn::getInstance();

  $customers = $conn->query('SELECT * FROM customers');
  if(!$customers){
    echo "Error: no customers";
    die();
  }

  $cust_head = array_keys($customers->fetch_assoc());
  $cust_rows = $customers->fetch_all();
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
  <meta charset="utf-8">
  <meta name="author" content="KMiskell">
  <meta name="description" content="Week 20 General Practice">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="../root-assets/favicon/javascript-original.svg" type="image/x-icon">
  <link rel="stylesheet" href="css/practice-stylesheet.css">
  <link rel="stylesheet" href="css/stylesheet-w20-practice.css">
  <link rel="stylesheet" href="../root-css/header.css">
  <link rel="stylesheet" href="../root-css/nav-buttons.css">
  <link rel="stylesheet" href="../Higlightjs/styles/tomorrow-night-bright.css">
  <link rel="stylesheet" href='https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css'>
  <script src="../Higlightjs/highlight.pack.js"></script>
  <script>hljs.initHighlightingOnLoad();</script>
  <title>Week 20 Sandbox</title>
</head>

<body>
  <div>
    <header class="header-v2">
      <h1>Week 20 Sandbox</h1>
    </header>

    <nav class="nav-v2">
      <ul>
        <li><a href="../index.html" title="Home">Home</a></li>
        <li><a href="../practice-weeks/week19-practice.php" title="Previous Work">Previous Work</a></li>
        <li><a href="../study-weeks/week20.html" title="Week Log">Week Log</a></li>
        <!--<li><a href="../practice-weeks/week21-practice.html" title="Next Work">Next Work</a></li>-->
        <li><a href="../roadmap.html" title="Roadmap">Roadmap</a></li>
        <li><select onchange="location = this.value;" target="_blank" onclick"hideFirst">
              <option value="">Notes</option>
              <option value="../notes/Python.pdf" title="Python Studies in PDF">Python</option>
              <option value="../notes/React-II.pdf" title="React Studies in PDF">REACT II</option>
              <option value="../notes/MySQL.pdf" title="React Studies in PDF">MySQL & DB Design</option>
              <option value="../notes/PHP7.pdf" title="PHP Studies in PDF">PHP</option>
              <option value="../notes/Functional-JS-I.pdf">Functional JS</option>
              <option value="../notes/React-I.pdf">React</option>
              <option value="../notes/Git.pdf">Git</option>
              <option value="../notes/JS-I.pdf">JS</option>
              <option value="../notes/CSS3-I.pdf">CSS</option>
              <option value="../notes/HTML-I.pdf">HTML</option>
            </select>
        </li>
        <li><select onchange="location = this.value;" onclick"hideFirst">
              <option value="">Learning Resources</option>
              <option value="https://www.udemy.com/course/complete-react-developer-zero-to-mastery/">Complete React Developer 2020</option>
              <option value="../notes/Prof-Frisby-Funct-JS.pdf">Prof Frsiby's Functional JS</option>
              <option value="../notes/React-Quickly-2017.pdf">React Quickly</option>
              <option value="https://eloquentjavascript.net/">Eloquent JavaScript</option>
              <option value="https://developer.mozilla.org/en-US/docs/Learn">MDN Docs</option>
            </select>
        </li>
      </ul>
    </nav>
  </div>

  <main>
    <section>
      <h2>PHP Form Sanitzation</h2>
        <pre><code class='php'>

      //Use proper semantic hmtl to limit input first &#40;ex. &lt;input type='number'&gt; instead of &lt;input type='text'&gt;
      //Still need to do proper sanitizing via PHP before submitting, though:

      //string sanitization
      function sanitizeString($string){
        $string = stripslashes($string);  //removes slashes
        $string = htmlentities($string);  //replaces html tags with proper &lt;, etc.
        $string = strip_tags($string);   //removes angle brackets fully

        return $string;
      }

      //SQL sanitization
      function sanitizeSQL($string, $conn){
        $string = $conn->real_escape_string($string);   //escapes special chars
        return sanitizeString($string);
      }
        </code>
      </pre>
    </section>

    <div class="table-container">
      <h5>DB connection status: <?= $conn->testConnection() ?></h5>
      <h4>Customers</h4>
      <table class="practice_table" id="practice_tableA" class="display" style="width: 100%">
          <thead style="font-weight: bold;">
              <?php add_head_row($cust_head); ?>
          </thead>
          <tbody>
              <?php get_row_group($cust_rows); ?>
          </tbody>
      </table>
    </div>

    <section>
      <h2>PHP Form GET w/ Ajax & Response View Update</h2>
        <pre><code class='php'>
          //get_customer.php

          error_reporting(E_ALL);
          header('Content-type: application/json');
          require_once 'dbConn.class.php';

          $conn = dbConn::getInstance();
          $custID = $_GET['custID'];

          if(!isset($custID)){
            echo 'Cannot query without pkey custID';
          }

          $result = $conn->query("SELECT * FROM customers WHERE custID = '$custID'");
          $customer = $result->fetch_assoc();

          if(!$result){
            echo 'No customer exists with specified ID.';
          }

          echo json_encode($customer);
          </code>
        </pre>

        <pre><code class='js'>
          //form submission handling function in this page

          $('#cust_pop').submit(function(event){
            event.preventDefault();

            $.ajax({
              url: 'week-20-php/get_customer.php',
              type: 'GET',
              data: $('#cust_pop').serialize(),
              dataType: 'json',
              success: function(response){
                if(response == null){
                  alert("No customer found with this ID");
                }
                for(key in response){
                  $(`input[name="${key}"]`).val(response[key]);
                }
              },
              error: function(response){
                alert('Request failed');
              }
            });
          });
        </code>
      </pre>
    </section>

    <section class='formSection'>
      <form id="cust_pop" action="index.php" method="post">
        <h4>Enter customer ID to populate Update Form</h4>
        <input id='sendID' type='text' name='custID'>
        <input type='submit' value='Populate Customer Edit'>
      </form>
    </section>

    <section class='formSection'>
      <h4>Update Customer Info</h4>
      <form class='flex-form' action="update" method="post">
        <label>
          Title:
          <input type='text' name='title'>
        </label>
        <label>
          Last Name:
          <input type='text' name='last'>
        </label>
        <label>
          First Name:
          <input type='text' name='first'>
        </label>
        <label>
          Phone:
          <input type='text' name='phone'>
        </label>
        <label>
          Unit:
          <input type='text' name='unit'>
        </label>
        <label>
          City:
          <input type='text' name='city'>
        </label>
        <label>
          State:
          <input type='text' name='state'>
        </label>
        <label>
          Zip:
          <input type='text' name='zip'>
        </label>
        <label>
          Country:
          <input type='text' name='country'>
        </label>
        <label>
          Employee Num:
          <input type='text' name='employee num'>
        </label>
        <label>
          Credit Limit:
          <input type='text' name='credit limit'>
        </label>
        <br>
        <input class='submit' type="submit" value="Update Customer">
      </form>
    </section>
    <br><br>

  </main>

</body>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

</html>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready( function () {
  $('#practice_tableA').DataTable({
      'autowidth': false
  });
  $('#practice_tableB').DataTable({
      'autowidth': false
  });
});

$('#cust_pop').submit(function(event){
  event.preventDefault();

  $.ajax({
    url: 'week-20-php/get_customer.php',
    type: 'GET',
    data: $('#cust_pop').serialize(),
    dataType: 'json',
    success: function(response){
      if(response == null){
        alert("No customer found with this ID");
      }
      for(key in response){
        $(`input[name="${key}"]`).val(response[key]);
      }
    },
    error: function(response){
      alert('Request failed');
    }
  });
});
</script>

</html>

<?php
  $conn->close;
  $customers->close;
?>
