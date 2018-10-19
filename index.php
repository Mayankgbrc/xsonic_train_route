<!DOCTYPE html>
<?php
    session_start();
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Xsonic Train Route | Train Route of Indian Railway</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <style type="text/css">
            body{
                font-family: Arail, sans-serif;
            }
            /* Formatting search box */
            .search-box{
                width: 300px;
                position: relative;
                display: inline-block;
                font-size: 14px;
            }
            .search-box input[type="text"]{
                height: 32px;
                padding: 5px 10px;
                border: 1px solid #CCCCCC;
                font-size: 14px;
            }
            .result{
                position: absolute;        
                z-index: 999;
                top: 100%;
                left: 0;
            }
            .search-box input[type="text"], .result{
                width: 100%;
                box-sizing: border-box;
            }
            /* Formatting result items */
            .result p{
                margin: 0;
                padding: 7px 10px;
                border: 1px solid #CCCCCC;
                border-top: none;
                cursor: pointer;
            }
            .result p:hover{
                background: #f2f2f2;
            }
        </style>
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script type="text/javascript">
        $(document).ready(function(){
            $('.search-box input[type="text"]').on("keyup input", function(){
                /* Get input value on change */
                var inputVal = $(this).val();
                var resultDropdown = $(this).siblings(".result");
                if(inputVal.length){
                    $.get("test.php", {term: inputVal}).done(function(data){
                        var inputVal_new = $('.search-box input[type="text"]').val();
                        
                        if(inputVal_new == inputVal){
                        // Display the returned data in browser
                        resultDropdown.html(data);
                        
                        }
                    });
                } else{
                    resultDropdown.empty();
                }
            });
            
            // Set search input value on click of result item
            $(document).on("click", ".result p", function(){
                var data =$(this).text();
                var arr = data.split(' ');
                $(this).parents(".search-box").find('input[type="text"]').val(arr[0]);
                $(this).parent(".result").empty();
            });
        });
        </script>
        <script>
        function sendto()
        {
                num1 = document.getElementById("in").value;
                document.getElementById("out").value = num1;
        }
        </script>
    </head>
<body >
    <header class="w3-container w3-teal w3-center">
      <h1>Xsonic Rail Route</h1>
    </header>
    <div class="w3-container">
        <?php
            if($_SESSION['err']){
                echo "<span class=w3-text-red >".$_SESSION['err']."</span>";
            }
        ?>
        <h3>Enter train number: </h3>
            <div class="search-box">
                <input type="text" autocomplete="off" placeholder="" id="in"/>
                <div class="result w3-pale-red w3-text-black"></div>
            </div>
            <br><br><br>
        <form method="get" action="buffer.php">
            <input type="hidden" name="t_no" id="out"/>
            <button onClick="sendto()" type="submit">Enter</button>
        </form>
    </div>
    <footer class="w3-container w3-teal w3-center" style="position:fixed;bottom:0;left:0;width:100%;">
      <h4>Developed with <span class="w3-text-red">&hearts;</span> by <a href="https://www.facebook.com/mayankgbrc" target="_blank">Mayank Gupta</a> </h4>
    </footer>
</body>
</html>
<?php
    session_unset();
?>