<!DOCTYPE HTML>
<html>
  <head>
    <title>MEGA2560 WITH MYSQL DATABASE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="icon" href="data:,">
    <style>
      html {font-family: Arial; display: inline-block; text-align: center;}
      p {font-size: 1.2rem;}
      h4 {font-size: 0.8rem;}
      body {margin: 0;}
      .topnav {overflow: hidden; background-color: #0c6980; color: white; font-size: 1.2rem;}
      .content {padding: 5px; }
      .card {background-color: white; box-shadow: 0px 0px 10px 1px rgba(140,140,140,.5); border: 1px solid #0c6980; border-radius: 15px;}
      .card.header {background-color: #0c6980; color: white; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-top-right-radius: 12px; border-top-left-radius: 12px;}
      .cards {max-width: 700px; margin: 0 auto; display: grid; grid-gap: 2rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));}
      .reading {font-size: 1.3rem;}
      .packet {color: #bebebe;}
      .temperatureColor {color: #fd7e14;}
      .pressureColor {color: #1b78e2;}
      .statusreadColor {color: #702963; font-size:12px;}
      .LEDColor {color: #183153;}

      .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
      }

      .switch input {display:none;}

      .sliderTS {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #D3D3D3;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px;
      }

      .sliderTS:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: #f7f7f7;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50%;
      }

      input:checked + .sliderTS {
        background-color: #00878F;
      }

      input:focus + .sliderTS {
        box-shadow: 0 0 1px #2196F3;
      }

      input:checked + .sliderTS:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
      }

      .sliderTS:after {
        content:'OFF';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 70%;
        font-size: 10px;
        font-family: Verdana, sans-serif;
      }

      input:checked + .sliderTS:after {  
        left: 25%;
        content:'ON';
      }

      input:disabled + .sliderTS {  
        opacity: 0.3;
        cursor: not-allowed;
        pointer-events: none;
      }

    </style>
  </head>
  
  <body>
    <div class="topnav">
      <h3>MEGA2560 WITH MYSQL DATABASE</h3>
    </div>
    
    <br>
    
    <div class="content">
      <div class="cards">
        <div class="card">
          <div class="card header">
            <h3 style="font-size: 1rem;">MONITORING</h3>
          </div>        
          <!-- Displays the temperature and pressure and altitude values received from mega2560. *** -->
          <h4 class="temperatureColor"><i class="fas fa-thermometer-half"></i> TEMPERATURE</h4>
          <p class="temperatureColor"><span class="reading"><span id="mega_01_Temp"></span> &deg;C</span></p>
          <h4 class="pressureColor"><i class="fas fa-tint"></i> PRESSURE </h4>
          <p class="pressureColor"><span class="reading"><span id="mega_01_Pres"></span> Pa</span></p>
          <h4 class="altitudeColor"><i class="fas fa-tint"></i> ALTITUDE </h4>
          <p class="altitudeColor"><span class="reading"><span id="mega_01_Alti"></span> m</span></p>
        </div>
        
        <div class="card">
          <div class="card header">
            <h3 style="font-size: 1rem;">CONTROLLING</h3>
          </div>
          <!-- Buttons for controlling the LEDs on Slave 2. ************************** -->
          <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> LED 1</h4>
          <label class="switch">
            <input type="checkbox" id="mega_01_TogLED_01" onclick="GetTogBtnLEDState('mega_01_TogLED_01')">
            <div class="sliderTS"></div>
          </label>
          <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> LED 2</h4>
          <label class="switch">
            <input type="checkbox" id="mega_01_TogLED_02" onclick="GetTogBtnLEDState('mega_01_TogLED_02')">
            <div class="sliderTS"></div>
          </label>
        </div>
      </div>
    </div>
    
    <br>
    
    <div class="content">
      <div class="cards">
        <div class="card header" style="border-radius: 15px;">
            <h3 style="font-size: 0.7rem;">LAST TIME RECEIVED DATA FROM MEGA2560 [ <span id="mega_01_LTRD"></span> ]</h3>
            <button onclick="window.open('recordtable.php', '_blank');">Open Record Table</button>
        </div>
      </div>
    </div>
    
<script>
  // Initialize display elements
  document.getElementById("mega_01_Temp").innerHTML = "NM"; 
  document.getElementById("mega_01_Pres").innerHTML = "NN";
  document.getElementById("mega_01_Alti").innerHTML = "NN";
  document.getElementById("mega_01_LTRD").innerHTML = "NN";

  // Fetch initial data
  Get_Data("mega_01");
  setInterval(myTimer, 5000);

  function myTimer() {
    Get_Data("mega_01");
  }

function Get_Data(id) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log("Raw Response:", this.responseText); 

      try {
        const myObj = JSON.parse(this.responseText);
        console.log("Parsed Object:", myObj); 

        // Update your page elements using myObj here
        document.getElementById("mega_01_Temp").innerHTML = myObj.temperature;
        document.getElementById("mega_01_Pres").innerHTML = myObj.pressure;
        document.getElementById("mega_01_Alti").innerHTML = myObj.altitude;
        document.getElementById("mega_01_LTRD").innerHTML = "Time : " + myObj.ls_time + " | Date : " + myObj.ls_date;
        document.getElementById("mega_01_TogLED_01").checked = (myObj.LED_01 == "ON");
        document.getElementById("mega_01_TogLED_02").checked = (myObj.LED_02 == "ON");
      } catch (e) {
        console.error("Error parsing JSON:", e);
        // Optionally, update your page to indicate an error has occurred
      }
    }
  };
  xmlhttp.open("POST", "getdata2.php", true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send("id=" + id);
}

  function GetTogBtnLEDState(togbtnid) {
    var togbtnchecked = document.getElementById(togbtnid).checked;
    var togbtncheckedsend = togbtnchecked ? "ON" : "OFF";
    Update_LEDs("mega_01", togbtnid.includes("TogLED_01") ? "LED_01" : "LED_02", togbtncheckedsend);
  }

  function Update_LEDs(id, lednum, ledstate) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST","updateLEDs.php",true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("id=" + id + "&lednum=" + lednum + "&ledstate=" + ledstate);
  }
</script>

  </body>
</html>